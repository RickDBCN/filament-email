<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div>
        <iframe
            style="width: 100%; height: 75vh;"
            srcdoc="{{ $getState() }}"
            frameborder="0"
        ></iframe>
    </div>
</x-dynamic-component>
