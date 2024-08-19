<?php
namespace clover\platform\preferences;


use php\gui\UXChoiceBox;

class UIxPreferenceItemChoiceBox extends UIxPreferenceAbstractItem
{
    protected function __createItem()
    {
        $choiceBox = new UXChoiceBox();
        return $choiceBox;
    }
    
}