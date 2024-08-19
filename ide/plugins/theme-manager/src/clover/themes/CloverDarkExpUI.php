<?php
namespace clover\themes;

use clover\platform\themes\Theme;

class CloverDarkExpUI extends CloverLightExpUI
{
    public function getName(): string { return "Clover Dark ExpUI"; }

    public function isDarked(): bool { return true; }


    public function getStylesheetFilePath()
    {
        return "/.theme/clover_dark.css";
    }
}