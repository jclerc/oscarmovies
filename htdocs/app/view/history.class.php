<?php

namespace View;
use Base\View;

/**
 * History view
 */
class History extends View {

    public function index() {
        $this->set('title', 'History');
        $this->render([
            'parts/head/default',
            'parts/navbar/default',
            'page/history/index',
            'parts/footer/default'
        ]);
    }

}
