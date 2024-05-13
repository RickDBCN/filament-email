<?php $attachments = $getRecord()->attachments; ?>

@if(!empty($attachments))
    <div class="flex flex-wrap gap-8">
        @foreach($attachments as $attachment)
            <div class="basis-1/4 me-3">
                <div class="flex items-center space-x-6 rtl:space-x-reverse">
                    <div class="flex-initial min-w-0 pe-4">
                        <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                            <strong>{{ $attachment['name'] }}</strong>
                        </p>
                    </div>
                    <div class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                        {{ ($this->downloadAction)(['path' => $attachment['path'], 'name' => $attachment['name'] , 'type' => $attachment['contentType']]) }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

