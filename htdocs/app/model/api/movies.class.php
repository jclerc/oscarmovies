<?php

namespace Model\Api;
use Model\Base\Api;
use Model\Mapper\Cache;

/**
 * Movies API
 */
class Movies extends Api {

    const API_QUERY = 'https://api.themoviedb.org/3/{query}?api_key=b2c09eb301c08aec545e015ab0fb2a40';

    public function findByGenre($genre) {
        $id = $this->getGenreId($genre);
        if ($id) {
            return $this->query('genre/' . $id . '/movies');
        }
        return null;
    }

    public function trending() {
        return $this->query('discover/movie');
    }

    private function query($query) {
        $response = $this->callJson('query', ['query' => $query], Cache::EXPIRE_WEEK);
        return isset($response->results) ? $response->results : null;
    }

    private function getGenreId($genre) {
        $list = $this->query('genre/movie/list');
        if (isset($list->genres)) {
            foreach ($list->genres as $g) {
                if (strtolower($g->name) === strtolower($genre)) {
                    return $g->id;
                }
            }
        }
        return null;
    }

}
