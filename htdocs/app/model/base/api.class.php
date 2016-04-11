<?php

namespace Model\Base;
use Base\Model;

/**
 * Base API class
 */
abstract class Api extends Model {

    public function callJson($api, array $params = []) {
        return json_decode($this->callApi($api, $params));
    }

    public function callApi($api, array $params = []) {
        $url = constant(get_called_class() . '::API_' . str_replace('.', '_', strtoupper($api)));

        foreach ($params as $key => $value) {
            $url = str_replace('{' . $key . '}', $value, $url);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

}
