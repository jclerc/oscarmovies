<?php

namespace View;
use Base\View;

/**
 * Index view
 */
class Legal extends View {

    public function index() {
        $this->set('title', 'Legal Notice');
        $this->render([
            'parts/head/default',
            'parts/navbar/default',
            'page/legal/index',
            'parts/footer/default'
        ]);
    }

}
