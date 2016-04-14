<?php

namespace Model\Api;
use Model\Base\Api;
use Model\Mapper\Cache;

/**
 * Ip API
 */
class Ip extends Api {

    const API_IP = 'http://ip-api.com/json/{ip}';

    public function getCurrent() {
        return $this->get($this->getClientIp());
    }

    public function get($ip) {
        return $this->callJson('ip', ['ip' => $ip], Cache::EXPIRE_WEEK);
    }

    private function getClientIp() {
        $ip = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (!empty($_SERVER['HTTP_X_FORWARDED']))
            $ip = $_SERVER['HTTP_X_FORWARDED'];
        else if (!empty($_SERVER['HTTP_FORWARDED_FOR']))
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (!empty($_SERVER['HTTP_FORWARDED']))
            $ip = $_SERVER['HTTP_FORWARDED'];
        else if (!empty($_SERVER['REMOTE_ADDR']))
            $ip = $_SERVER['REMOTE_ADDR'];

        if (strlen($ip) > 3 and $ip !== 'localhost' and $ip !== '127.0.0.1') {
            return $ip;
        } else {
            return null;
        }
    }

}
