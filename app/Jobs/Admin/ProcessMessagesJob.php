<?php
namespace App\Jobs\Admin;

use App\Contracts\Repositories\EmailAccountFolderRepository;
use App\Contracts\Repositories\EmailAccountMessageRepository;
use App\Contracts\Repositories\EmailAccountRepository;
use App\Innoclapps\Google\Services\MessageCollection;
use App\MailClient\EmailAccountIdBasedSynchronization;
use App\MailClient\GmailEmailAccountSynchronization;
use App\MailClient\ImapEmailAccountSynchronization;
use App\MailClient\OutlookEmailAccountSynchronization;
use App\Models\EmailAccount;
use Closure;
use Ddeboer\Imap\Exception\UnexpectedEncodingException;
use Ddeboer\Imap\Exception\UnsupportedCharsetException;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Opis\Closure\SerializableClosure;
use ReflectionObject;
use stdClass;

class ProcessMessagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $messages;
    protected $folder;
    protected $provider;

    public function __construct($provider, $messages, $folder = null)
    {
        $this->messages = $messages;
        $this->folder = $folder;
        $this->provider = $provider;
    }
  
    public function handle(EmailAccountMessageRepository $msgRepo, EmailAccountRepository $accounts, EmailAccountFolderRepository $folders,EmailAccount $account)
    {
        \Log::info('in handle');
        $this->messages= unserialize($this->messages);
      switch ($this->provider) 
        {
            case 'Imap':
                $obj=new ImapEmailAccountSynchronization($accounts,$msgRepo,$folders,$account);
                try {
                $obj->processMessages($this->messages, $this->folder);
                } catch (UnexpectedEncodingException | UnsupportedCharsetException $e) {
                $obj->error('Mail message was skipped from import because of ' . Str::of($e::class)->headline()->lower() . ' exception.');
                }
                break;
            case 'Outlook':
                $obj=new OutlookEmailAccountSynchronization($accounts,$msgRepo,$folders,$account);
                $obj->processMessages($this->messages);
                break;
            default:
                $obj=new GmailEmailAccountSynchronization($accounts,$messages,$folders,$account);
                $obj->processMessages($this->messages);
                break;
        }

    }
}