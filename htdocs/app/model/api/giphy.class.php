<?php

namespace Model\Api;
use Model\Base\Api;
use Model\Mapper\Cache;

/**
 * Giphy API
 */
class Giphy extends Api {

    const API_SEARCH = 'http://api.giphy.com/v1/gifs/search?q={query}&limit=25&api_key=dc6zaTOxFJmzC';

    public function get($query) {
        $json = $this->callJson('search', ['query' => urlencode($query)], Cache::EXPIRE_DAY);
        if (!empty($json) and !empty($json->data)) {
            $max = count($json->data);
            $index = rand(0, $max-1);
            $gif = $json->data[$index];
            if (!empty($gif)) {
                return $gif->images->original->url;
            }
        }
        return null;
    }

}
