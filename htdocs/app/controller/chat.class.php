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

        if ($request->isAjax()) {

            $post = $request->getAjax();
            $message = $post['message'];

            // PROCESS API
            $data = [];
            $data['message'] = 'Wait a moment..';

            if (rand(0, 3) === 0) {
                $data['gif'] = $this->api->giphy->get('happy');
            }

            $this->view($data);

        }

        $this->set('weather', $this->api->weather->getCurrentState());

    }

}
