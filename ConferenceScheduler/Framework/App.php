<?php

namespace Framework;

use Framework\DB\SimpleDB;
use Framework\Routers\DefaultRouter;
use Framework\Routers\IRouter;
use Framework\Sessions\ISession;
use Framework\Sessions\NativeSession;

require_once 'Autoloader.php';

class App
{
    private static $_instance = null;

    /**
     * @var Config
     */
    private $_config = null;
    private $_router = null;
    private $_dbConnections = [];

    /**
     * @var ISession
     */
    private $_session = null;

    private function __construct()
    {
        Autoloader::registerNamespace('Framework', dirname(__FILE__) . DIRECTORY_SEPARATOR);
        Autoloader::registerAutoLoad();
        $this->_config = Config::getInstance();
    }

    /**
     * @return App
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new App();
        }

        return self::$_instance;
    }

    public function getConfigFolder()
    {
        return $this->_config->getConfigFolder();
    }

    public function setConfigFolder($path)
    {
        $this->_config->setConfigFolder($path);
    }

    public function getRouter()
    {
        return $this->_router;
    }

    function setRouter($router)
    {
        $this->_router = $router;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    public function getDbConnection($connection = 'default')
    {
        if (!$connection) {
            throw new \Exception('No connection string provided', 500);
        }

        if (isset($this->_dbConnections[$connection])) {
            return $this->_dbConnections[$connection];
        }

        $dbConfig = $this->getConfig()->database;

        if (!$dbConfig[$connection]) {
            throw new \Exception('No valid connection string found in config file', 500);
        }

        $database = new \PDO(
            $dbConfig[$connection]['connection_url'],
            $dbConfig[$connection]['username'],
            $dbConfig[$connection]['password'],
            $dbConfig[$connection]['pdo_options']
        );

        $this->_dbConnections[$connection] = $database;

        return $database;
    }

    /**
     * @return ISession
     */
    public function getSession(){
        return $this->_session;
    }

    public function setSession(ISession $session){
        $this->_session = $session;
    }

    public function run()
    {
        if ($this->_config->getConfigFolder() == null) {
            $this->setConfigFolder('ConferenceScheduler/config');
        }

        if ($this->_session == null) {
            $sessionInfo = $this->_config->app['session'];

            if ($sessionInfo['auto_start']) {
                if ($sessionInfo['type'] == 'native') {
                    $this->_session = new NativeSession(
                        $sessionInfo['name'],
                        $sessionInfo['lifetime'],
                        $sessionInfo['path'],
                        $sessionInfo['domain'],
                        $sessionInfo['secure']
                    );
                }
            }
        }
    }

    public function __destruct()
    {
        if ($this->_session != null) {
            $this->_session->saveSession();
        }
    }

    public function getUsername()
    {
        return $this->_session->escapedUsername;
    }

    public function isLogged()
    {
        return $this->_session->escapedUsername !== null;
    }
}