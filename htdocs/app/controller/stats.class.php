<?php

namespace Controller;
use Base\Controller;
use Model\Service\Request;

/**
 * Stats controller
 */
class Stats extends Controller {

    public function index(Request $request) {
        // No view are explicitely defined, so it will use View\Stats::index()
    }

}
