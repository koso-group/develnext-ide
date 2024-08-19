<?php

namespace clover\platform\plugins\traits;

trait Preferences
{   
    public function getPreferenceTabs(): array
    {
        return [];
    }

    abstract function getPreferenceItems(): array;
}