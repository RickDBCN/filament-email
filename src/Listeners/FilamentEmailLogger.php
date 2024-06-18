<?php

namespace RickDBCN\FilamentEmail\Listeners;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

        $attachmentsDisk = config('filament-email.attachments_disk', 'local');
        $storeAttachments = config('filament-email.store_attachments', true);

        $model = config('filament-email.resource.model') ?? Email::class;

        $attachments = [];
        $savePath = 'filament-email-log'.DIRECTORY_SEPARATOR.date('YmdHis').'_'.Str::random(5).DIRECTORY_SEPARATOR;

        if ($storeAttachments) {

            foreach ($event->message->getAttachments() as $attachment) {
                $filePath = $savePath.Str::random(5).'_'.$attachment->getFilename();
                Storage::disk($attachmentsDisk)
                    ->put($filePath, $attachment->getBody());
                $attachments[] = [
                    'name' => $attachment->getFilename(),
                    'contentType' => $attachment->getContentType(),
                    'path' => $filePath,
                ];
            }

            $savePathRaw = $savePath.$rawMessage->getMessageId().'.eml';

            Storage::disk($attachmentsDisk)
                ->put($savePathRaw, $rawMessage->getMessage()->toString());

        } else {
            $savePathRaw = null;
        }
        $model::create([
            'team_id' => Filament::getTenant()?->id ?? null,
            'from' => $this->recipientsToString($email->getFrom()),
            'to' => $this->recipientsToString($email->getTo()),
            'cc' => $this->recipientsToString($email->getCc()),
            'bcc' => $this->recipientsToString($email->getBcc()),
            'subject' => $email->getSubject(),
            'html_body' => $email->getHtmlBody(),
            'text_body' => $email->getTextBody(),
            'raw_body' => $savePathRaw,
            'sent_debug_info' => $rawMessage->getDebug(),
            'attachments' => ! empty($attachments) ? $attachments : null,
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
