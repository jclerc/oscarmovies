<?php

namespace Model\Api;
use Model\Base\Api;
use Model\Mapper\Cache;

/**
 * Movies API
 */
class Movies extends Api {

    const API_QUERY = 'https://api.themoviedb.org/3/{url}?api_key=b2c09eb301c08aec545e015ab0fb2a40{query}';

    public function find($params) {
        return $this->query('discover/movie', $params);
    }

    // public function findByGenre($genre) {
    //     $id = $this->getGenreId($genre);
    //     if ($id) {
    //         return $this->query('genre/' . $id . '/movies');
    //     }
    //     return null;
    // }

    public function trending() {
        return $this->query('discover/movie', [
            'vote_average.gte' => 7,
            'primary_release_date.gte' => date('Y-m-d', time() - 86400 * 30 * 6),
        ]);
    }

    private function query($url, $params = []) {
        $query = '';
        foreach ($params as $key => $value) {
            $query .= '&' . urlencode($key) . '=' . urlencode($value);
        }
        $response = $this->callJson('query', ['url' => $url, 'query' => $query], Cache::EXPIRE_WEEK);
        return isset($response->results) ? $response->results : $response;
    }

    public function getGenreName($id) {
        $list = $this->query('genre/movie/list');
        if (isset($list->genres)) {
            foreach ($list->genres as $g) {
                if (intval($g->id) === intval($id)) {
                    return $g->name;
                }
            }
        }
        return null;
    }

    public function getGenreId($genre) {
        $list = $this->query('genre/movie/list');
        $genre = trim(str_replace('movie', '', $genre));
        if (isset($list->genres)) {
            foreach ($list->genres as $g) {
                if (strtolower($g->name) === strtolower($genre)) {
                    return $g->id;
                }
            }
        }
        return null;
    }

    public function getPeopleId($people) {
        $list = $this->query('search/person', [
            'query' => urlencode($people)
        ]);
        if (is_array($list) and count($list) > 0 and isset($list[0]->id)) {
            return $list[0]->id;
        }
        return null;
    }

}
