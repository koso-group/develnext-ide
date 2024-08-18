<?php

namespace clover\platform\facades;

use Closure;
use Exception;
use php\desktop\Runtime;
use ReflectionClass;

use php\io\File;
use php\lib\fs;

final class PluginManager
{
    public static function __pathNormalize($classPath)
    {
        if(DIRECTORY_SEPARATOR == '\\') return $classPath;
        return implode(DIRECTORY_SEPARATOR, explode('\\', $classPath));
    }
    protected static $__pluginStore = [];

    public static function resolvePlugins($pluginsPath)
    {
        if($pluginsPath instanceof File){}
        else $pluginsPath = new File($pluginsPath);

        //static::$__pluginStore = [];

        foreach (($pluginsPath)->findFiles() as $plugin) {
            
            try
            {
                $pluginPath = $plugin->getPath();

                if($plugin->isDirectory())
                {
                    $pluginPath = $plugin->getPath() . "\plugin.php";
                    
                    $directoryLibs = new File(self::__pathNormalize($plugin->getPath() . "\libs"));

                    if($directoryLibs->exists())
                        foreach($directoryLibs->findFiles() as $file)
                            if(fs::ext($file->getPath()) == 'jar')
                                Runtime::addJar($file);
                    
                    spl_autoload_register(function($className) use ($plugin)
                    {
                        $classPath = self::__pathNormalize($plugin->getPath() . "\src\\" . $className . ".php");
                        if(!file_exists($classPath)) return;

                        include $classPath;
                    });
                    
                }
        
                $pluginClass = include(self::__pathNormalize($pluginPath));
                
                $reflection = new ReflectionClass($pluginClass);
                foreach($reflection->getTraits() as $reflectionClass)
                {
                    static::$__pluginStore[$reflectionClass->name][] = $pluginClass;
                }

            }
            catch(Exception $exception)
            {
                
            }
        }
        
        
    }

    public static function getAll()
    {
        return static::$__pluginStore;
    }

    public static function forTrait(string $namespace, Closure $closure = null) : array
    {
        $plugins = static::$__pluginStore[$namespace] ?: [];
        
        if($closure)
            foreach($plugins as $plugin)
                $closure($plugin);
        
        return $plugins;
    }
}
