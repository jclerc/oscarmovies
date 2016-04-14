<?php

namespace Model\Base;
use Base\Model;

/**
 * Base API class
 */
abstract class Api extends Model {

    public function callJson($api, array $params = [], $expiration) {
        return json_decode($this->callApi($api, $params, $expiration));
    }

    public function callApi($api, array $params = [], $expiration) {

        if ($cache = $this->cache->get([$api, $params])) {
            return $cache;
        }

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

        if (!empty($result)) {
            $this->cache->set([$api, $params], $result, $expiration);
        }

        return $result;
    }

}
