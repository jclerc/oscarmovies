<?php

namespace View;
use Base\View;

/**
 * Index view
 */
class Index extends View {

    public function index() {
        $this->set('title', 'Welcome');
        $this->render([
            'parts/head/no-nav',
            'page/home/index',
            'parts/footer/default'
        ]);
    }

}
