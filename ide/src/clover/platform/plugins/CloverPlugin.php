<?php

namespace clover\platform\plugins;

use clover\platform\facades\PluginManager;
use php\io\File;

abstract class CloverPlugin 
{
    abstract public function getName(): string;
    abstract public function getDescription(): string;
    abstract public function getVersion(): float;
    abstract public function getAuthor(): string;




    
    public function getIcon(): string 
    {
        return "icons/plugin32.png";
    }

    public function isCorePlugin(): bool 
    {
        return false;
    }

    public function getDependencies(): array
    {
        return [];
    }

    public static function resourceFile(string $filePath) : File
    {
        $pluginInstance = (new static());
        $pluginName = $pluginInstance->getName();
        unset($pluginInstance);

        return PluginManager::resourceFile($pluginName, $filePath);
    }
}