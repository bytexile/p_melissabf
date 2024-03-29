<?php

namespace Core;

use PDO;
use PDOException;

class DataBase
{
    // ///// METODOS
    public static function getDB()
    {
        $conf = include_once(__DIR__.DS.'..'.DS.'app'.DS.'database.php');

        // SQLite
        if($conf['driver'] == 'sqlite') {
            $sqlite = __DIR__.DS.'..'.DS.'storage'.DS.$conf['sqlite']['database'];
            $sqlite = "sqlite:{$sqlite}";

            try {
                $pdo = new PDO($sqlite);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

                return $pdo;
            }
            catch (PDOException $e) {
                return $e->getMessage();
            }
        }
        // mySQL
        elseif($conf['driver'] == 'mysql') {
            $host      = $conf['mysql']['host'];
            $db        = $conf['mysql']['database'];
            $user      = $conf['mysql']['user'];
            $pass      = $conf['mysql']['pass'];
            $charset   = $conf['mysql']['charset'];
            $collation = $conf['mysql']['collation'];

            try {
                $pdo = new PDO("mysql:host=$host; dbname=$db; charset=$charset", $user, $pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES '$charset' COLLATE '$collation'");
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

                return $pdo;
            }
            catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
    }
}