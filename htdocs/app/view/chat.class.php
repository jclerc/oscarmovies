<?php

namespace View;
use Base\View;

/**
 * Chat view
 */
class Chat extends View {

    public function index() {
        $this->set('title', 'Chat');
        $this->render([
            'parts/head/default',
            'parts/navbar/default',
            'page/chat/index',
            'parts/footer/default'
        ]);
    }

}
