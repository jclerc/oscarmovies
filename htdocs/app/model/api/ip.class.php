<?php

namespace Model\Api;
use Model\Base\Api;

/**
 * Ip API
 */
class Ip extends Api {

    const API_IP = 'http://ip-api.com/json/{ip}';

    // Cache current result
    private $current = null;

    public function getCurrent() {
        if (empty($this->current)) {
            $this->current = $this->get($this->getClientIp());
        }
        return $this->current;
    }

    public function get($ip) {
        return $this->callJson('ip', ['ip' => $ip]);
    }

    private function getClientIp() {
        if (!empty($_SESSION['HTTP_CLIENT_IP']))
            return $_SESSION['HTTP_CLIENT_IP'];
        else if (!empty($_SESSION['HTTP_X_FORWARDED_FOR']))
            return $_SESSION['HTTP_X_FORWARDED_FOR'];
        else if (!empty($_SESSION['HTTP_X_FORWARDED']))
            return $_SESSION['HTTP_X_FORWARDED'];
        else if (!empty($_SESSION['HTTP_FORWARDED_FOR']))
            return $_SESSION['HTTP_FORWARDED_FOR'];
        else if (!empty($_SESSION['HTTP_FORWARDED']))
            return $_SESSION['HTTP_FORWARDED'];
        else if (!empty($_SESSION['REMOTE_ADDR']))
            return $_SESSION['REMOTE_ADDR'];
        else
            return null;
    }

}
