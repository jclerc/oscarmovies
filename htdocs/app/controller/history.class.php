<?php

namespace Controller;
use Base\Controller;
use Model\Service\Request;

/**
 * History controller
 */
class History extends Controller {

    public function index(Request $request) {
        // No view are explicitely defined, so it will use View\History::index()

        $movies = $this->api->movies->trending();

        foreach ($movies as $movie) {
            $id = reset($movie->genre_ids);
            $movie->genre_name = $this->api->movies->getGenreName($id);
        }

        $this->set('movies', $movies);

    }

}
