<?php
namespace clover\platform\preferences;

use ide\ui\elements\DNComboBox;

class UIxPreferenceItemComboBox extends UIxPreferenceAbstractItem
{
    protected function __createItem()
    {
        $comboBox = new DNComboBox();
        return $comboBox;
    }

    public function setItems(array $items)
    {
        $this->__item->items->addAll($items);
        return $this;
    }

    public function setSelected($item)
    {
        $this->__item->selected = ($item);
        return $this;
    }

    public function getSelected()
    {
        return $this->__item->selected;
    }
    
}