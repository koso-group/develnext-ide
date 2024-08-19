<?php
namespace clover\themes;

use clover\platform\themes\Theme;
use ThemeManagerPlugin;

class CloverLightExpUI extends Theme
{

    public function getName(): string { return "Clover Light ExpUI"; }
    public function getDescription(): string { return ""; }
    public function getVersion(): float { return 1.0; }
    public function getAuthor(): string { return "meatsuko"; }

    public function isDarked(): bool { return false; }

    public function getStylesheetFilePath()
    {
        return ThemeManagerPlugin::resourceFile("style.css")->getCanonicalPath();
    }

    public function apply($form)
    {
        $form->clearStylesheets();
        $form->addStylesheet($this->getStylesheetFilePath());
        
        // connect dwm
    }
}