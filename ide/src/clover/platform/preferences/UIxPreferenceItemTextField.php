<?php
namespace clover\platform\preferences;

use ide\ui\elements\DNButton;
use ide\ui\elements\DNLabel;
use ide\ui\elements\DNTextField;
use php\gui\layout\UXHBox;

class UIxPreferenceItemTextField extends UIxPreferenceAbstractItem
{   

    public function __createItem()
    {
        $textField =  new DNTextField();
        return $textField;
    }

    public function setOnAction(\Closure $closure)
    {
        $this->__item->observer('text')->addListener($closure);
        return $this;
    }
}