<?php

namespace RickDBCN\FilamentEmail\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use RickDBCN\FilamentEmail\Models\Email;

class ResendMail extends Mailable
{
    use Queueable, SerializesModels;

    public Email $email;

    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->email->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: $this->email->html_body ?? $this->email->text_body,
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
