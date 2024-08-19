<?php
namespace clover\platform\preferences;

use ide\ui\elements\DNScrollPane;
use php\gui\layout\UXHBox;
use php\gui\layout\UXVBox;
use php\gui\UXTab;



class UIxPreferenceTab extends UXTab
{
    protected UXVBox $__contentContainer;

    public function __construct(string $caption, $icon = null)
    {
        parent::__construct();
        
        $this->text = $caption;
        if($icon)
        {
            $this->graphic = $icon;
        }
        $this->closable = false;

        $this->content = $scroll = new DNScrollPane($this->__contentContainer = new UXVBox());
        $scroll->fitToWidth = true;
        $this->__contentContainer->spacing = 6;
        $this->__contentContainer->padding = 10;
        UXHBox::setHgrow($this->__contentContainer, 'ALWAYS');
    }

    public function __toString()
    {
        return $this->text;
    }

    public function addPreferenceItem($item)
    {
        $this->__contentContainer->add($item);
    }
}