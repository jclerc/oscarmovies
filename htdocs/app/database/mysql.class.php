<?php

namespace Database;
use PDO, Exception, InternalException;

/**
 * MySQL database class
 *
 * Allows to abstract SQL queries from MySQL database
 *
 * @see Database interface at database/Database.class.php
 * @throws InternalException
 */
class MySQL extends SQL {

    public function __construct(array $access) {
        $dsn = 'mysql:host=' . $access[0] . ';dbname=' . $access[1] . ';charset=utf8;';
        $options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES "UTF8";',
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $this->db = new PDO($dsn, $access[2], $access[3], $options);
        } catch (Exception $e) {
            throw new InternalException('Cant connect to database: ' . $e->getMessage());
        }
    }

    protected function escape($str) {
        if (ctype_alnum(str_replace('_', '', $str)))
            return '`' . $str . '`';
        else
            return $str;
    }

}
