<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractTerminationMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $contract;
    protected $notifiable;
    /**
     * Create a new message instance.
     */
    public function __construct($notifiable, $contract)
    {
      $this->contract = $contract;
      $this->notifiable = $notifiable;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Contract Terminated',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
      return new Content(
        view: 'emails.admin.contract-termination',
        with: ['contract' => $this->contract, 'notifiable' => $this->notifiable],
      );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
