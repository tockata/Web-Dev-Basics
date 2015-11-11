<?php

namespace Framework;

class Config
{
    private static $_instance = null;
    private $_configFolder = null;
    private $_configArray = [];

    private function __construct()
    {
    }

    public function getConfigFolder()
    {
        return $this->_configFolder;
    }

    public function setConfigFolder($configFolder)
    {
        if (!$configFolder) {
            throw new \Exception('Empty Config folder path.');
        }

        $realPath = realpath($configFolder);

        if ($realPath != false && is_dir($realPath) && is_readable($realPath)) {
            $this->_configArray = [];
            $this->_configFolder = $realPath . DIRECTORY_SEPARATOR;

            $namespaces = $this->app['namespaces'];

            if (is_array($namespaces)) {
                Autoloader::registerNamespaces($namespaces);
            }
        } else {
            throw new \Exception('Config directory read error: ' . $configFolder, 500);
        }
    }

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new Config();
        }

        return self::$_instance;
    }

    public function includeConfigFile($path)
    {
        if (!$path) {
            throw new \Exception('Empty config path');
        }

        $file = realpath($path);

        if ($file != false && is_file($file) && is_readable($file)) {
            $baseName = explode('.php', basename($file))[0];
            $this->_configArray[$baseName] = include $file;
        } else {
            throw new \Exception('Config file read error: ' . $path, 404);
        }
    }

    public function __get($name)
    {
        if (!isset($this->_configArray[$name]) || $this->_configArray[$name] == null) {
            $this->includeConfigFile($this->_configFolder . $name . '.php');
        }

        if (array_key_exists($name, $this->_configArray)) {
            return $this->_configArray[$name];
        }

        return null;
    }
}