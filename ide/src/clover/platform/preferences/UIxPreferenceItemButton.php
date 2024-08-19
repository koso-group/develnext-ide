<?php
namespace clover\platform\preferences;

use ide\ui\elements\DNButton;
use ide\ui\elements\DNLabel;
use php\gui\layout\UXHBox;

class UIxPreferenceItemButton extends UIxPreferenceAbstractItem
{
    protected string $__buttonName;

    public function __createItem()
    {
        $button =  new DNButton($this->__buttonName);
        return $button;
    }

    public function __construct(string $buttonName, string $preferenceCaption = null)
    {
        $this->__buttonName = $buttonName;
        
        parent::__construct($preferenceCaption);
    }
}