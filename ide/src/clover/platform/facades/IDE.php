<?php
namespace clover\platform\facades;

use php\io\File;
use php\lang\System;

class IDE
{
    public static function file($filePath)
    {
        return static::folder($filePath);        
    }

    public static function folder($folderPath)
    {
        $ideHome = File::of(implode('/', [
            System::getProperty('user.home'),
            ".DevelNextCE"
        ]));
        
        if (!$ideHome->isDirectory()) {
            $ideHome->mkdirs();
        }
        
        $folderPath = File::of(implode('/', [$ideHome, $folderPath]));
        if(!$folderPath->isDirectory())
        {
            $folderPath->mkdirs();
        }

        return $folderPath;
    }
}
