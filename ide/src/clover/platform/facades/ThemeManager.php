<?php
namespace clover\platform\facades;

use clover\platform\plugins\traits\Themes;
use clover\platform\themes\Theme;
use php\gui\framework\FormCollection;

class ThemeManager
{
    public static function allThemes() : array

    {
        $themes = [];

        foreach(PluginManager::forTrait(Themes::class) as $plugin)
        {
           $themes = array_merge($themes, $plugin->getThemes());
        }

        return $themes;
    }

    public static function applyTheme(Theme $theme)
    {
        foreach(FormCollection::getForms() as $form) $theme->apply($form);
    }

    public static function currentTheme() : Theme 
    {
        $themeName = PreferenceManager::pull('theme');

        foreach(static::allThemes() as $theme)
        {
            if($theme->getName() == $themeName) return $theme;
        }

        return ThemeManager::allThemes()[0];
    }
}