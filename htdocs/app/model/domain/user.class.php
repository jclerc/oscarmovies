<?php

namespace Model\Domain;
use Model\Base\Domain;

/**
 * User class
 */
class User extends Domain {

    protected $properties = [
        'facebook_id' => 0,
        'first_name'  => '',
        'last_name'   => '',
        'email'       => '',
        'picture'     => '',
        'evaluation'  => 0,
    ];

    public function getName() {
        $this->get('first_name') . ' ' . $this->get('last_name');
    }

    public function fromFacebookId($id) {
        return $this->fromProperty('facebook_id', $id);
    }

    protected function __setFacebookId($id) {
        $this->validate->isInteger($id);
    }

    protected function __setFirstName($name) {
        $this->validate->isString($name);
    }

    protected function __setLastName($name) {
        $this->validate->isString($name);
    }

    protected function __setEmail($email) {
        $this->validate->isString($email);
    }

    protected function __setPicture($pictureLink) {
        $this->validate->isString($pictureLink);
    }

    protected function __setEvaluation($evaluation) {
        $this->validate->isInteger($evaluation);
    }

    protected function __defaultEvaluation() {
        return 0;
    }

}
