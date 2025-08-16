<x-filament-forms::field-wrapper :id="$getId()" :label="$getLabel()" :label-sr-only="$isLabelHidden()" :hint="$getHint()" :hint-icon="$getHintIcon()" :required="$isRequired()" :state-path="$getStatePath()">
    <div>
        <iframe style="width: 100%; height:75vh;" srcdoc=" {{$getState() }}" seamless frameborder="0"></iframe>
    </div>
</x-filament-forms::field-wrapper>
