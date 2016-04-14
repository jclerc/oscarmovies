<?php

namespace Controller;
use Base\Controller;
use Model\Service\Request;

/**
 * Trending controller
 */
class Trending extends Controller {

    public function index(Request $request) {
        // No view are explicitely defined, so it will use View\Trending::index()

        $movies = $this->api->movies->trending();

        foreach ($movies as $movie) {
            $id = reset($movie->genre_ids);
            $movie->genre_name = $this->api->movies->getGenreName($id);
        }

        $this->set('featuring', $movies[0]);
        $this->set('movies', [
            $movies[1],
            $movies[2],
            $movies[3],
            $movies[4],
            $movies[5],
            $movies[6],
        ]);

    }

}
