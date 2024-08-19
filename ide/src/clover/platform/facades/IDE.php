<?php
namespace clover\platform\facades;

use php\io\File;
use php\lang\System;

class IDE
{
    public static bool $requiredReboot = false;
    
    public static function file($filePath) : File
    {
        $filePaths = explode('/', $filePath);
        $fileName = array_pop($filePaths);
        $filePath = implode('/', $filePaths);

        $filePath = static::folder($filePath);        

        return File::of(implode('/', [$filePath, $fileName]));
    }

    public static function folder($folderPath) : File
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
