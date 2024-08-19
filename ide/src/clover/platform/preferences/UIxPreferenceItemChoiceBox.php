<?php
namespace clover\platform\preferences;

use ide\ui\elements\DNLabel;
use php\gui\layout\UXHBox;
use php\gui\UXChoiceBox;

class UIxPreferenceItemChoiceBox extends UIxPreferenceAbstractItem
{
    protected function __CreateItem()
    {
        $choiceBox = new UXChoiceBox();
        return $choiceBox;
    }
    
}