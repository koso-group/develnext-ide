<?php
namespace clover\platform\facades;

use clover\platform\forms\IDEPreferencesForm;

class PreferenceManager
{
    public static function show()
    {
        $form = new IDEPreferencesForm;
        $form->showAndWait();
    }


    protected static $__prefJSON = null;
    public static function pull($key, $group = "clover")
    {
        if(!static::$__prefJSON) static::$__prefJSON = new PrefJSON(".pref.json");
        return static::$__prefJSON->pull($key, $group);
    }

    public static function push($key, $value, $group = "clover")
    {
        if(!static::$__prefJSON) static::$__prefJSON = new PrefJSON(".pref.json");
        static::$__prefJSON->push($key, $value, $group);
        static::$__prefJSON->commit();
    }
    
}

class PrefJSON
{
    protected $__filePath;

    protected $__data;

    public function __construct($filePath)
    {
        $this->__filePath = IDE::file($filePath)->getPath();

        $this->__data = json_decode(file_get_contents($this->__filePath), true);
    }

    public function commit()
    {
        file_put_contents($this->__filePath, json_encode($this->__data, JSON_PRETTY_PRINT));
    }

    public function pull($key, $group = "clover")
    {
        return $this->__data[$group][$key];
    }

    public function push($key, $value, $group = "clover")
    {
        $this->__data[$group][$key] = $value;
    }
}