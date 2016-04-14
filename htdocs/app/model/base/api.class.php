<?php

namespace Model\Base;
use Base\Model;

/**
 * Base API class
 */
abstract class Api extends Model {

    protected function callJson($api, array $params = [], $expiration = null) {
        return json_decode($this->callApi($api, $params, $expiration));
    }

    protected function callApi($api, array $params = [], $expiration = null) {

        if (isset($expiration) and $cache = $this->cache->get([$api, $params])) {
            return $cache;
        }

        $url = constant(get_called_class() . '::API_' . str_replace('.', '_', strtoupper($api)));

        $url = $this->parseUrl($url, $params);

        $result = $this->curl($url);

        if (!empty($result) and isset($expiration)) {
            $this->cache->set([$api, $params], $result, $expiration);
        }

        return $result;
    }

    protected function parseUrl($url, $params) {
        foreach ($params as $key => $value) {
            $url = str_replace('{' . $key . '}', $value, $url);
        }
        return $url;
    }

    protected function curl($url, $opts = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        foreach ($opts as $key => $value) {
            curl_setopt($ch, $key, $value);
        }
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}
