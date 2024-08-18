<?php
namespace clover\platform\plugins\traits;

trait ProjectNavigatorActions
{
    abstract function getProjectNavigatorActions() : array;

    public function getProjectNavigatorAction(string $actionClass) : AnAction
    {
        foreach($this->getTreeActions() as $action)
        {
            if($action instanceof $actionClass) return $action;
        }
    }
}