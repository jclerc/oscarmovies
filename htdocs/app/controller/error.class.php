<?php

namespace Controller;
use Base\Controller;
use Model\Service\Request;

/**
 * Error controller
 */
class Error extends Controller {

    public function index(Request $request) {
        // No view are explicitely defined, so it will use View\Error::index()
    }

}
