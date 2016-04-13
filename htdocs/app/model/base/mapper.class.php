<?php

namespace Model\Base;
use Base\Model;

/**
 * Base data mapper class
 */
abstract class Mapper extends Model {
    
    const EXPIRE_MINUTE = 60;
    const EXPIRE_HOUR = 3600;
    const EXPIRE_DAY = 86400;
    const EXPIRE_WEEK = 86400 * 7;
    const EXPIRE_MONTH = 86400 * 30;

    abstract public function get($key);

    abstract public function set($key, $value, $expire = 0);

    abstract public function has($key);

    abstract public function delete($key);

}
