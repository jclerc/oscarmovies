<?php

namespace View;
use Base\View;

/**
 * Error view
 */
class Error extends View {

    public function index() {
        $this->set('title', 'Error');
        $this->render([
            'parts/head/default',
            'parts/navbar/default',
            'page/error/index',
            'parts/footer/default'
        ]);
    }

}
