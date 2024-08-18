<?php
namespace clover\platform\plugins\traits;

use clover\platform\plugins\AnAction;

trait Actions
{
    abstract function getActions() : array;
    
    public function getAction(string $actionClass) : AnAction
    {
        foreach($this->getActions() as $action)
        {
            if($action instanceof $actionClass) return $action;
        }
    }
}