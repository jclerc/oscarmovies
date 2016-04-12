<?php

namespace View;
use Base\View;

/**
 * Trending view
 */
class Trending extends View {

    public function index() {
        $this->set('title', 'Trending');
        $this->render([
            'parts/head/default',
            'parts/navbar/default',
            'page/trending/index',
            'parts/footer/default'
        ]);
    }

}
