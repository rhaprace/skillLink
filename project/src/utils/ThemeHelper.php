<?php

class ThemeHelper
{
    public static function getThemeClasses(string $theme = 'light'): array
    {
        return [
            'bg' => $theme === 'dark' ? 'bg-black' : 'bg-white',
            'text' => $theme === 'dark' ? 'text-white' : 'text-black',
            'border' => $theme === 'dark' ? 'border-gray-800' : 'border-gray-100',
            'iconBg' => $theme === 'dark' ? 'bg-white' : 'bg-black',
            'iconText' => $theme === 'dark' ? 'text-black' : 'text-white',
        ];
    }
    public static function getAuthSidebarDefaults(array $options = []): array
    {
        return [
            'title' => $options['title'] ?? 'Welcome',
            'subtitle' => $options['subtitle'] ?? 'Continue your journey',
            'illustration' => $options['illustration'] ?? 'default.svg',
            'theme' => $options['theme'] ?? 'light',
        ];
    }
}

