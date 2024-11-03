<?php $attachments = $getRecord()->attachments; ?>

@if(!empty($attachments))
    <div class="flex flex-wrap gap-8">
        @foreach($attachments as $attachment)
            <x-filament::section class="text-center grow">
                <x-slot name="heading">
                    <strong>{{ $attachment['name'] }}</strong>
                </x-slot>
                {{ ($this->downloadAction)(['path' => $attachment['path'], 'name' => $attachment['name'] , 'type' => $attachment['contentType']]) }}
            </x-filament::section>
        @endforeach
    </div>
@endif

