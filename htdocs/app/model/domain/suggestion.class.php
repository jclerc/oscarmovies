<?php

namespace Model\Domain;
use Model\Base\Domain;

/**
 * An suggestion to question
 */
class Suggestion extends Domain {

    protected $properties = [
        'user_id'    => 0,
        'time'       => 0,
        'movie_id'   => '',
        'movie_data' => '',
    ];

    protected function __setUserId($id) {
        $this->validate->isInt($id);
    }

    protected function __setTime($time) {
        $this->validate->isInt($time);
    }

    protected function __setMovieId($id) {
        $this->validate->isInt($id);
    }

    protected function __defaultTime() {
        return time();
    }

    protected function __setMovieData($movie) {
        if (is_array($movie) or is_object($movie)) {
            return json_encode($movie);
        }
        return $movie;
    }

    public function getMovieData() {
        return json_decode($this->get('movie_data'));
    }

}
