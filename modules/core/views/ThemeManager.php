<?php

namespace core\views;
class ThemeManager
{
    public static function getThemeClass(): string
    {
        $theme = is_string($_SESSION['theme'] ?? null) ? $_SESSION['theme'] : 'normal';
        $allowed = ['normal', 'rgb', 'cat', 'space'];

        if (!in_array($theme, $allowed)) {
            $theme = 'normal';
        }

        return 'theme-' . $theme;
    }
}