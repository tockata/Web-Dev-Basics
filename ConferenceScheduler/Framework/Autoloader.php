<?php

namespace Framework;

final class Autoloader
{
    private static $namespaces = [];

    private function __construct()
    {
    }

    public static function registerAutoLoad()
    {
        spl_autoload_register([ '\Framework\Autoloader', 'autoload' ]);
    }

    public static function autoload($class)
    {
        self::loadClass($class);
    }

    public static function loadClass($class)
    {
        foreach (self::$namespaces as $namespace => $path) {
            if (strpos($class, $namespace) === 0) {
                $invariantSystemPath = str_replace('\\', DIRECTORY_SEPARATOR, $class);
                $filePath = substr_replace($invariantSystemPath, $path, 0, strlen($namespace)) . '.php';
                $realPath = realpath($filePath);

                if ($realPath && is_readable($realPath)) {
                    require_once $realPath;
                } else {
                    throw new \Exception('File cannot be included: ' . $filePath, 404);
                }

                break;
            }
        }
    }

    public static function registerNamespace($namespace, $path)
    {
        $namespace = trim($namespace);

        if (strlen($namespace) > 0) {
            if (!$path) {
                throw new \Exception('Invalid path: ' . $namespace, 404);
            }

            $realPath = realpath($path);

            if ($realPath && is_dir($realPath) && is_readable($realPath)) {
                self::$namespaces[$namespace . '\\'] = $realPath . DIRECTORY_SEPARATOR;
            } else {
                throw new \Exception('Namespace directory read error in:' . $path, 500);
            }
        } else {
            throw new \Exception('Invalid namespace: ' . $namespace, 500);
        }
    }

    public static function registerNamespaces($namespaces)
    {
        if (is_array($namespaces)) {
            foreach ($namespaces as $namespace => $path) {
                self::registerNamespace($namespace, $path);
            }
        } else {
            throw new \Exception('Invalid namespaces!');
        }
    }
}