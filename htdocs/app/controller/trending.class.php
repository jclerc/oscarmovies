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

        $featuring = json_decode(<<<EOL

{"adult":false,"backdrop_path":"/xGC2fY5KFmtuXnsuQwYQKFOLZFy.jpg","belongs_to_collection":{"id":48317,"name":"The Man With No Name Collection","poster_path":"/lzUZptOL6iQqulMvhB2jfT5GepD.jpg","backdrop_path":"/uAkE1MAvFUiYjOllgDRndispKu3.jpg"},"budget":1200000,"genres":[{"id":37,"name":"Western"}],"homepage":"http://www.mgm.com/view/Movie/766/The-Good,-the-Bad-and-the-Ugly/","id":429,"imdb_id":"tt0060196","original_language":"it","original_title":"Il buono, il brutto, il cattivo","overview":"While the Civil War rages between the Union and the Confederacy, three men – a quiet loner, a ruthless hit man and a Mexican bandit – comb the American Southwest in search of a strongbox containing $200,000 in stolen gold.","popularity":2.38758,"poster_path":"/v6TUio0GgIsK9pbW7FfFArbyECb.jpg","production_companies":[{"name":"United Artists","id":60},{"name":"Constantin Film Produktion","id":5755},{"name":"Produzioni Europee Associati (PEA)","id":7508},{"name":"Arturo González Producciones Cinematográficas S.A.","id":42498}],"production_countries":[{"iso_3166_1":"US","name":"United States of America"},{"iso_3166_1":"IT","name":"Italy"},{"iso_3166_1":"ES","name":"Spain"},{"iso_3166_1":"DE","name":"Germany"}],"release_date":"1966-12-23","revenue":6000000,"runtime":161,"spoken_languages":[{"iso_639_1":"it","name":"Italiano"}],"status":"Released","tagline":"For three men the Civil War wasn't hell. It was practice.","title":"The Good, the Bad and the Ugly","video":false,"vote_average":7.8,"vote_count":1160}
EOL
);

        $this->set('featuring', $featuring);
        $this->set('movies', [
            $featuring,
            $featuring,
            $featuring,
            $featuring,
            $featuring,
            $featuring,
        ]);

    }

}
