<?php

namespace RickDBCN\FilamentEmail\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class ResendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;

    public $addAttachments;

    public function __construct($email, bool $attachments = true)
    {
        $this->email = $email;
        $this->addAttachments = $attachments;
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
        $attachments = [];

        if ($this->addAttachments) {
            $modelAttachments = $this->email->attachments;
            if (!empty($modelAttachments)) {
                foreach ($modelAttachments as $attachment) {
                    $attachments[] = Attachment::fromPath(storage_path('app' . DIRECTORY_SEPARATOR . $attachment['path']))
                        ->as($attachment['name']);
                }
            }
        }

        return $attachments;
    }
}
