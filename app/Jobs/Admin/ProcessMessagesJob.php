<?php
namespace App\Jobs\Admin;
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

    public function __construct($messages, $folder)
    {
        $this->messages = $messages;
        $this->folder = $folder;
    }

    public function handle()
    {
        try {
            $this->processMessages($this->messages, $this->folder);
        } catch (UnexpectedEncodingException|UnsupportedCharsetException $e) {
            $this->error('Mail message was skipped from import because of ' . Str::of($e::class)->headline()->lower() . ' exception.');
        }
    }
}
