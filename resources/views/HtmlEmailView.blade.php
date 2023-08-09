<x-filament-forms::field-wrapper :id="$getId()" :label="$getLabel()" :label-sr-only="$isLabelHidden()" :helper-text="$getHelperText()" :hint="$getHint()" :hint-icon="$getHintIcon()" :required="$isRequired()" :state-path="$getStatePath()">
    <div>
        <iframe class="w-full h-screen" srcdoc=" {{$getState() }}" seamless frameborder="0"></iframe>
    </div>
</x-filament-forms::field-wrapper>
