<?php
namespace clover\platform\preferences;

use ide\ui\elements\DNCheckbox;

class UIxPreferenceItemCheckBox extends UIxPreferenceItemHBox
{

    protected $__item;
    public function __construct(string $preferenceCaption)
    {
        $items = [];
        
        $items[] = $this->__item = new DNCheckbox($preferenceCaption);
        
        
        parent::__construct($items);
        $this->alignment = 'CENTER_LEFT';
    }

    public function setOnAction(\Closure $closure)
    {
        $this->__item->on('action', $closure);
        return $this;
    }

    public function getValue() : bool
    {
        return $this->__item->selected;
    }
}