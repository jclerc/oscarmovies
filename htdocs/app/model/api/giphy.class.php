<?php

namespace Model\Api;
use Model\Base\Api;

/**
 * Giphy API
 */
class Giphy extends Api {

    const API_SEARCH = 'http://api.giphy.com/v1/gifs/search?q={query}&limit=3&api_key=dc6zaTOxFJmzC';

    public function get($query) {
        $json = $this->callJson('search', ['query' => urlencode($query)]);
        var_dump($json); exit;
        if (!empty($json) and !empty($json->data)) {
            $max = count($json->data);
            $index = rand(0, $max-2);
            $gif = $json->data[$index];
            if (!empty($gif)) {
                return $gif->embed_url;
            }
        }
        return null;
    }

}
