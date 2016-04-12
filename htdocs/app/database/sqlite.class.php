<?php

namespace Database;
use PDO, PDOException, InternalException;

/**
 * SQLite database class
 *
 * Allows to abstract SQL queries from SQLite database
 *
 * @see Database interface at database/Database.class.php
 * @throws InternalException
 */
class SQLite extends SQL {

    public function __construct(array $access) {
        $dsn = 'sqlite:' . $access[0];
        $options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false
        ];

        try {
            $this->db = new PDO($dsn, null, null, $options);
            $this->db->exec('PRAGMA synchronous=OFF');
        } catch (PDOException $e) {
            throw new InternalException('Can\'t connect to database: ' . $e->getMessage());
        }
    }

}
