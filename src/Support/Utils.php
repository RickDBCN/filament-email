<?php

namespace RickDBCN\FilamentEmail\Support;

class Utils
{
    /// Email logs
    public static function getEmailResourceSlug(): string
    {
        return (string) config('filament-email.resources.emails.slug');
    }

    public static function getEmailResourceNavigationSort(): ?int
    {
        return config('filament-email.resources.emails.navigation_sort');
    }

    public static function isEmailResourceNavigationRegistered(): bool
    {
        return config('filament-email.resources.emails.should_register_navigation', true);
    }

    public static function isEmailResourceNavigationGroupEnabled(): bool
    {
        return config('filament-email.resources.emails.navigation_group', true);
    }

    /// Integrations
    public static function getIntegrationResourceSlug(): string
    {
        return (string) config('filament-email.resources.integrations.slug');
    }

    public static function getIntegrationResourceNavigationSort(): ?int
    {
        return config('filament-email.resources.integrations.navigation_sort');
    }

    public static function isIntegrationResourceNavigationRegistered(): bool
    {
        return config('filament-email.resources.integrations.should_register_navigation', true);
    }

    public static function isIntegrationResourceNavigationGroupEnabled(): bool
    {
        return config('filament-email.resources.integrations.navigation_group', true);
    }

    /// Modal
    public static function isIntegrationsButtonEnabled(): bool
    {
        return config('filament-email.show_integrations_button', true);
    }

    public static function isModalButtonEnabled(): bool
    {
        return config('filament-email.modal.show_modal_button', true);
    }
}
