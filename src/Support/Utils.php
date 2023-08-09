<?php

namespace RickDBCN\FilamentEmail\Support;

class Utils
{
    public static function getResourceSlug(): string
    {
        return (string) config('filament-email.resource.slug');
    }
    public static function getResourceNavigationSort(): ?int
    {
        return config('filament-email.resource.navigation_sort');
    }
    public static function isResourceNavigationRegistered(): bool
    {
        return config('filament-email.resource.should_register_navigation', true);
    }
    public static function isResourceNavigationGroupEnabled(): bool
    {
        return config('filament-email.resource.navigation_group', true);
    }

    public static function isModalButtonEnabled(): bool
    {
        return config('filament-email.modal.show_modal_button', true);
    }


}
