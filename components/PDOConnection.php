<?php

namespace app\components;

use PDO;

/**
 * Class PDOConnection
 * @package app\components
 */
class PDOConnection
{
    /**
     * Getting PDO connection
     * @return PDO connection with DB
     */
    public static function getConnection()
    {
        $params = require(ROOT . "/config/db.php");

        $dsn = "mysql:host={$params['host']};dbname={$params['dbname']}";

        $db = new PDO($dsn, $params['user'], $params['password']);

        $db->exec("set names utf8");

        return $db;
    }

}