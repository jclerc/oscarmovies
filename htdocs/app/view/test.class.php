<?php

namespace View;
use Base\View;

/**
 * Test view
 */
class Test extends View {

    public function index() {
        $this->set('title', 'Test');
        $this->render([
            'parts/head/default',
            'parts/navbar/default',
            'page/test/index',
            'parts/footer/default'
        ]);
    }

}
