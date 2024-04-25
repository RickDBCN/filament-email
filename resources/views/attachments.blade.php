<div>
    @if(!empty($getRecord()->attachments))
        <ul class="max-w-md divide-y divide-gray-200 dark:divide-gray-700">
            @foreach(json_decode($getRecord()->attachments) as $attachment)
                <li class="pb-4 pt-4 sm:pb-4">
                    <div class="flex items-center space-x-4 rtl:space-x-reverse">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                <strong>{{ $attachment->name }}</strong>
                            </p>
                        </div>
                        <div class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                            {{ ($this->downloadAction)(['path' => $attachment->path, 'name' => $attachment->name , 'type' => $attachment->contentType]) }}
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
