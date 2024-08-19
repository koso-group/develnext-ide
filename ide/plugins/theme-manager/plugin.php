<?php

use clover\platform\facades\IDE;
use clover\platform\facades\PluginManager;
use clover\platform\facades\PreferenceManager;
use clover\platform\facades\ThemeManager;
use clover\platform\forms\IDEPreferencesForm;
use clover\platform\plugins\CloverPlugin;
use clover\platform\plugins\traits\Preferences;
use clover\platform\plugins\traits\Themes;
use clover\platform\preferences\UIxPreferenceItemChoiceBox;
use clover\platform\preferences\UIxPreferenceItemComboBox;
use clover\platform\preferences\UIxPreferenceItemHBox;
use clover\platform\themes\Theme;
use clover\themes\CloverDarkExpUI;
use clover\themes\CloverLightExpUI;



class ThemeManagerPlugin extends CloverPlugin
{
    public function getName(): string { return "ThemeManager"; }
    public function getDescription(): string { return ""; }
    public function getVersion(): float { return 1.0; }
    public function getAuthor(): string { return "meatsuko"; }

    public function isCorePlugin(): bool { return true; }

    use Themes;
    public function getThemes() : array
    {
        return [
            new CloverDarkExpUI,
            new CloverLightExpUI,

        ];
    }


    use Preferences;

    protected $__themePreferenceItemHBox = null;

    public function getPreferenceItems(): array
    {
        $preferenceItemTheme = (new UIxPreferenceItemComboBox('Select theme:'));
        $preferenceItemTheme->setOnAction(function () use ($preferenceItemTheme)
        {
            if(ThemeManager::currentTheme()->reqiuredReboot())
                IDE::$requiredReboot = true;
            
            $theme = $preferenceItemTheme->getSelected();
            ThemeManager::applyTheme($theme);
            PreferenceManager::push('theme', (string)$theme);
        });

        
        $preferenceItemTheme->setItems(ThemeManager::allThemes());
        $preferenceItemTheme->setSelected(ThemeManager::currentTheme());
        
        $this->__themePreferenceItemHBox = new UIxPreferenceItemHBox(ThemeManager::currentTheme()->getPreferenceItems());


        



        return [
            IDEPreferencesForm::$TAB_THEME => [
                $preferenceItemTheme,
                $this->__themePreferenceItemHBox,
            ]
        ];
    }


}

return new ThemeManagerPlugin;