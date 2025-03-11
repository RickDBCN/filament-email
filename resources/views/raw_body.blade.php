@php
    $content = $getRecord()?->raw_body;
    if (!empty($content)) {
        $attachmentsDisk = config('filament-email.attachments_disk', 'local');
        try {
            if (\Illuminate\Support\Facades\Storage::disk($attachmentsDisk)->exists($content)) {
                $content = \Illuminate\Support\Facades\Storage::disk($attachmentsDisk)->get($content);
            }
        } catch (\Exception) {}
    }
@endphp

<x-filament::input.wrapper>
    <textarea
        disabled="true"
        rows="{{ $rows ?? 20 }}"
        class="block w-full border-none bg-transparent px-3 py-1.5 text-base text-gray-950 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6"
    >{{ $content }}</textarea>
</x-filament::input.wrapper>
