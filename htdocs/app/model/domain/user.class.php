<?php

namespace Model\Domain;
use Model\Base\Domain;

/**
 * User class
 */
class User extends Domain {

    protected $properties = [
        'column_a' => '',
        'column_b' => '',
    ];

    protected function __setColumnA($valueA) {
        $this->validate->isString($valueA);
    }

    protected function __defaultColumnB() {
        return time();
    }

}
