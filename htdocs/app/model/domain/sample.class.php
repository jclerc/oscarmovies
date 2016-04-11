<?php

namespace Model\Domain;
use Model\Base\Domain;

/**
 * An answer to question
 */
class Answer extends Domain {

    protected $properties = [
        'column_a' => '',
        'column_b' => '',
    ];

    protected function __setColumnA($valueA) {
        $this->validate->isString($valueA);
    }

    protected function __setColumnB($valueB) {
        $this->validate->isInteger($valueB);
    }

}
