<?php

namespace View;
use Base\View;

/**
 * Index view
 */
class Index extends View {

    public function index() {
        $this->set('title', 'Bienvenue');
        $this->render([
            'parts/head/default',
            'parts/navbar/default',
            'page/home/index',
            'parts/footer/default'
        ]);
    }

}
