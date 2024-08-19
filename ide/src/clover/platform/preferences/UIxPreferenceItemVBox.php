<?php
namespace clover\platform\preferences;

use php\gui\layout\UXHBox;
use php\gui\layout\UXVBox;

class UIxPreferenceItemVBox extends UXVBox
{
    public function __construct(array $items)
    {
        parent::__construct($items, 6);
    }

    public function _setHgrow($value = 'ALWAYS')
    {
        UXHBox::setHgrow($this, $value);
        return $this;
    }

    public function _setVgrow($value = 'ALWAYS')
    {
        UXVBox::setVgrow($this, $value);
        return $this;
    }
}