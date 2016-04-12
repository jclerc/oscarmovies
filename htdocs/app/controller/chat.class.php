<?php

namespace Controller;
use Base\Controller;
use Model\Service\Request;

/**
 * Chat controller
 */
class Chat extends Controller {

    public function index(Request $request) {
        // No view are explicitely defined, so it will use View\Chat::index()
    }

}
