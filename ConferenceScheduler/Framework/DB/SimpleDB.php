<?php

namespace Framework\DB;

use Framework\App;

class SimpleDB
{
    protected $_connection = 'default';

    /**
     * @var \PDO
     */
    private $_db = null;
    private static $database = null;

    /**
     * @var \PDOStatement
     */
    private $_statement = null;
    private $_params = [];
    private $_sql;

    public function __construct($connection = null)
    {
        if ($connection instanceof \PDO) {
            $this->_db = $connection;
            self::$database = $connection;
        } else if ($connection != null) {
            $this->_db = App::getInstance()->getDbConnection($connection);
            self::$database = App::getInstance()->getDbConnection($connection);
            $this->_connection = $connection;
        } else {
            $this->_db = App::getInstance()->getDbConnection($this->_connection);
            self::$database = App::getInstance()->getDbConnection($this->_connection);
        }
    }

    /**
     * @param $sql
     * @param array $params
     * @param array $pdoOptions
     * @return \Framework\DB\SimpleDB
     */
    public function prepare($sql, $params = array(), $pdoOptions = [])
    {
        $this->_statement = $this->_db->prepare($sql, $pdoOptions);
        $this->_params = $params;
        $this->_sql = $sql;

        return $this;
    }

    public function execute($params = array())
    {
        if ($params) {
            $this->_params = $params;
        }

        $this->_statement->execute($this->_params);

        return $this;
    }

    public function fetchAllAssoc($escape = true)
    {
        $data = $this->_statement->fetchAll(\PDO::FETCH_ASSOC);

        if ($data === false) {
            return false;
        }

        if ($escape) {
            $escaped = [];

            foreach ($data as $elementKey => $elementData) {
                foreach ($elementData as $key => $value) {
                    $escaped[$elementKey][$key] = htmlentities($value);
                }
            }

            return $escaped;
        }

        return $data;
    }

    public function fetchRowAssoc($escape = true)
    {
        $data = $this->_statement->fetch(\PDO::FETCH_ASSOC);

        if ($data === false) {
            return false;
        }

        if ($escape) {
            $escaped = [];

            foreach ($data as $key => $value) {
                $escaped[$key] = htmlentities($value);
            }

            return $escaped;
        }

        return $data;
    }

    public function getLastInsertedId()
    {
        return $this->_db->lastInsertId();
    }

    /**
     * Can be used for custom use of PDO.
     */
    public function getStatement()
    {
        return $this->_statement;
    }

    public function affectedRows()
    {
        return $this->_statement->rowCount();
    }
}