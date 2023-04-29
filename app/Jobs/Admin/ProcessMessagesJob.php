<?php
namespace App\Jobs\Admin;

use App\MailClient\EmailAccountIdBasedSynchronization;
use App\MailClient\GmailEmailAccountSynchronization;
use App\MailClient\ImapEmailAccountSynchronization;
use App\MailClient\OutlookEmailAccountSynchronization;
use Ddeboer\Imap\Exception\UnexpectedEncodingException;
use Ddeboer\Imap\Exception\UnsupportedCharsetException;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessMessagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $messages;
    protected $folder;
    protected $provider;
    protected $obj;

    public function __construct(GmailEmailAccountSynchronization $obj,$provider, $messages, $folder = null)
    {
        $this->messages = $messages;
        $this->folder = $folder;
        $this->provider = $provider;
        $this->obj=$obj;
    }

    public function handle()
    {
      \Log::info('in handle');
        switch ($this->provider) {
            case 'Imap':
                try {
                    $this->obj->processMessages($this->messages, $this->folder);
                } catch (UnexpectedEncodingException | UnsupportedCharsetException $e) {
                    $this->obj->error('Mail message was skipped from import because of ' . Str::of($e::class)->headline()->lower() . ' exception.');
                }
                break;
            case 'Outlook':
                $this->obj->processMessages($this->messages);
                break;
            default:
                $this->obj->processMessages($this->messages);
                break;
        }

    }
}