<?php
namespace clover\platform\preferences;

use ide\ui\elements\DNTextField;

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

    public function getValue()
    {
        return $this->__item->text;
    }
}