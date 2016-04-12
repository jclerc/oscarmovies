<?php

namespace Controller;
use Base\Controller;
use Model\Service\Request;

/**
 * Trending controller
 */
class Trending extends Controller {

    public function index(Request $request) {
        // No view are explicitely defined, so it will use View\Trending::index()
    }

}
