<?php
namespace clover\platform\preferences;

use ide\ui\elements\DNLabel;
use php\gui\layout\UXHBox;
use php\gui\UXChoiceBox;

abstract class UIxPreferenceAbstractItem extends UIxPreferenceItemHBox
{
    protected $__item;
    protected $__preferenceCaption;

    protected abstract function __createItem();

    public function __construct(string $preferenceCaption = null)
    {
        $this->__preferenceCaption = $preferenceCaption;
        $items = [];
        
        $this->__item = $this->__createItem();
        if($preferenceCaption)
        {
            $items[] = new DNLabel($preferenceCaption);
            $items[] = $hBox = new UXHBox();
            UXHBox::setHgrow($hBox, 'ALWAYS');
        }
        else
        {
            $this->__item->maxWidth = INF;
            UXHBox::setHgrow($this->__item, 'ALWAYS');
        }
        $items[] = $this->__item;
        
        
        parent::__construct($items);
        $this->alignment = 'CENTER_LEFT';
    }

    public function setOnAction(\Closure $closure)
    {
        $this->__item->on('action', $closure);
        return $this;
    }
    
}