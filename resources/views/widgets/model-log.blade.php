<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading>
            {{ __('clock-widget::clock-widget.title') }}
        </x-slot>

        <div class="text-center">
        <p>test</p>
        <p class="text-xl" x-text="time"></p>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
