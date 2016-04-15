<?php

namespace Controller;
use Base\Controller;
use Model\Service\Request;
use Model\Domain\Suggestion;

/**
 * History controller
 */
class History extends Controller {

    public function index(Request $request) {
        // No view are explicitely defined, so it will use View\History::index()


        if ($this->auth->isLogged()) {
            $suggestion = $this->di->create(Suggestion::class);
            $suggestions = $suggestion->find(function ($search) {
                $search->where('user_id', $this->auth->getUser()->getId())
                       ->orderBy('time', 'DESC');
            });

            $movies = [];
            foreach ($suggestions as $suggestion) {
                $movie = $suggestion->getMovieData();
                $id = reset($movie->genre_ids);
                $movie->genre_name = $this->api->movies->getGenreName($id);
                $movie->date = date('d/m/Y', $suggestion->getTime());
                $movies[] = $movie;
            }

            $this->set('movies', $movies);
        }

        // foreach ($movies as $movie) {
        //     $id = reset($movie->genre_ids);
        //     $movie->genre_name = $this->api->movies->getGenreName($id);
        // }

        // $this->set('movies', $movies);

    }

}
