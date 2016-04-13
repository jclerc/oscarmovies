<?php

namespace Model\Mapper;
use Model\Base\Mapper;

/**
 * Cookie data management
 */
class Cookie extends Mapper {

    public function get($key) {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
    }

    public function set($key, $value, $expire = self::EXPIRE_WEEK) {
        setcookie($key, $value, time() + $expire, '/');
    }

    public function has($key) {
        return isset($_COOKIE[$key]);
    }

    public function delete($key) {
        setcookie($key, '', time() - 1);
    }

}
