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

    public $messages;
    public $folder;
    public $provider;
    public $accounts;
    public $msgRepo;
    public $folders;
    public $account;
    public function __construct($accounts,$msgRepo,$folders,$account,$provider, $messages, $folder = null)
    {
        $this->messages = $messages;
        $this->folder = $folder;
        $this->provider = $provider;
        $this->accounts=$accounts;
        $this->msgRepo=$msgRepo;
        $this->folders=$folders;
        $this->account=$account;
    }

    public function handle()
    {
      
        switch ($this->provider) {
            case 'Imap':
                try {
                    $imapSync = new ImapEmailAccountSynchronization($this->accounts,$this->msgRepo,$this->folders,$this->account);
                    $imapSync->processMessages($this->messages, $this->folder);
                } catch (UnexpectedEncodingException | UnsupportedCharsetException $e) {
                    $imapSync->error('Mail message was skipped from import because of ' . Str::of($e::class)->headline()->lower() . ' exception.');
                }
                break;
            case 'Outlook':
                $outlookSync = new OutlookEmailAccountSynchronization($this->accounts,$this->msgRepo,$this->folders,$this->account);
                $outlookSync->processMessages($this->messages);
                break;
            default:
                $gmailSync = new GmailEmailAccountSynchronization($this->accounts,$this->msgRepo,$this->folders,$this->account);
                $gmailSync->processMessages($this->messages);
                break;
        }

    }
}