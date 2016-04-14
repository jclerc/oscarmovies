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
            $data['message'] = call_user_func(function () {
                $msg = [
                    'Please wait..',
                    'I am Oscar ! 👻',
                    'What do you want ?',
                    'Ok dude.',
                    'You are ugly.',
                    'I love potatoes !',
                    'STOP TALKING TO ME.',
                ];
                return $msg[array_rand($msg)];
            });

            if (stripos($message, 'gif') > -1) {
                if (stripos($message, 'gif ') === 0) {
                    $data['gif'] = $this->api->giphy->get(substr($message, 4));
                } else {
                    $data['gif'] = $this->api->giphy->get('happy');
                }
            } else if (stripos($message, 'cat') > -1) {
                $data['gif'] = $this->api->giphy->get('cat');
                $data['message'] = 'I HEARD YOU SAY CAT ?';
            }

            $this->view($data);

        }

        $this->set('weather', $this->api->weather->getCurrentState());

    }

}
