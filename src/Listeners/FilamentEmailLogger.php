<?php

namespace RickDBCN\FilamentEmail\Listeners;

use RickDBCN\FilamentEmail\Models\Email;

class FilamentEmailLogger
{
    /**
     * Create the event listener
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event
     */
    public function handle(object $event): void
    {
        $rawMessage = $event->sent->getSymfonySentMessage();
        $email = $event->message;

        Email::create([
            'from' => $this->recipientsToString($email->getFrom()),
            'to' => $this->recipientsToString($email->getTo()),
            'cc' => $this->recipientsToString($email->getCc()),
            'bcc' => $this->recipientsToString($email->getBcc()),
            'subject' => $email->getSubject(),
            'html_body' => $email->getHtmlBody(),
            'text_body' => $email->getTextBody(),
            'raw_body' => $rawMessage->getMessage()->toString(),
            'sent_debug_info' => $rawMessage->getDebug(),
        ]);

    }

    private function recipientsToString(array $recipients): string
    {
        return implode(
            ',',
            array_map(function ($email) {
                return "{$email->getAddress()}".($email->getName() ? " <{$email->getName()}>" : '');
            }, $recipients)
        );
    }
}
