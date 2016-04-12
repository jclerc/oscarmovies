<?php

namespace View;
use Base\View;

/**
 * Stats view
 */
class Stats extends View {

    public function index() {
        $this->set('title', 'Stats');
        $this->render([
            'parts/head/default',
            'parts/navbar/default',
            'page/stats/index',
            'parts/footer/default'
        ]);
    }

}
