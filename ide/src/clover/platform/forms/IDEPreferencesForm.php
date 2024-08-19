<?php
namespace clover\platform\forms;

use clover\platform\facades\PluginManager;
use clover\platform\plugins\CloverPlugin;
use clover\platform\plugins\traits\Preferences;
use clover\platform\preferences\UIxPreferenceTab;
use php\gui\framework\AbstractForm;
use php\gui\UXTab;
use php\gui\UXTabPane;

use php\lib\str;

class IDEPreferencesForm extends AbstractForm
{
    protected static $__tabHolder = [];
    public static function createTab(string $caption, string $holder = null, $icon = null)
    {
        $tab = new UIxPreferenceTab($caption, $icon);

        if(!$holder) $holder = str::lower(str::trim($caption));
        static::$__tabHolder[$holder] = $tab;
        return $tab;
    }

    public static function resolveTab($item)
    {
        $holder = str::lower(str::trim((string)$item));
        return static::$__tabHolder[$holder];
    }



    public static $TAB_GENERAL;
    public static $TAB_EDITOR;
    public static $TAB_THEME;



    public function __construct()
    {
        parent::__construct();

        
        $this->title = "Настройки";
        
        $tabPane = $this->layout = new UXTabPane();
        $this->layout->size = [760, 620];

        $tabGeneral = static::createTab("General");
        $tabEditor = static::createTab("Editor");
        $tabTheme = static::createTab("Themes & Apperance");

        $tabPane->tabs->addAll([
            static::$TAB_GENERAL = $tabGeneral,
            static::$TAB_EDITOR = $tabEditor,
            static::$TAB_THEME = $tabTheme
        ]);

        PluginManager::forTrait(Preferences::class, function(CloverPlugin $plugin) use($tabPane)
        {
            $tabPane->tabs->addAll($plugin->getPreferenceTabs());
            
            foreach($plugin->getPreferenceItems() as $tab => $preferneceItem)
            {
                
                if(is_array($preferneceItem))
                {
                    foreach($preferneceItem as $item)
                    {
                        static::resolveTab($tab)->addPreferenceItem($item);
                    }
                    continue;
                }
                static::resolveTab($tab)->addPreferenceItem($preferneceItem);
            }
        });
    }
}