<?php
/**
 * Created by PhpStorm.
 * User: maslo
 * Date: 22-Jun-17
 * Time: 13:58
 */

namespace app\models;


use app\components\PDOConnection;
use PDO;

class Country
{
    /**
     * Getting list of countries from DB
     * @return array list of countries
     */
    public static function getCountries()
    {
        $db = PDOConnection::getConnection();

        $sql = 'SELECT * FROM country';
        $result = $db->prepare($sql);
        $result->setFetchMode(PDO::FETCH_ASSOC);
        $result->execute();
        return $result->fetchAll();
    }
}