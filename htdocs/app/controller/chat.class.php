<?php

namespace Controller;
use Base\Controller;
use Model\Service\Request;

/**
 * Chat controller
 */
class Chat extends Controller {

    // We must define key that aren't numeric, to be able to store it in session
    const DEFAULT_SUCCESS = [
        'Ok. What else? 🤔',
        'Sure! Any other indication?',
        'I\'ve got it. 👌 Do you want anything specific?',
        'Alright! Can you tell me more?',
        'Good choice. 👍 Anything else in mind?',
    ];

    const DEFAULT_ERROR = [
        'Sorry, I didn\'t understand.. 😭',
        'Oops ! 😫 I wasn\'t able to understand what you say.',
        'Hmm... Can you rephrase please ? 🤔',
        'That isn\'t in my language.. 😭',
        'I wasn\'t configured to understand that. Please rephrase.',
        'I don\'t understand Spanish.. 😤 Try in english?',
        'Is that even english ? 🙃',
        'That\'s embarrassing.. 😢 What did you mean exactly?',
        'Oh dear.. 😱 What did you intend to say?',
    ];

    private function getRandomSuccess() {
        return $this->getRandomMessage(self::DEFAULT_SUCCESS, 'success');
    }

    private function getRandomError() {
        return $this->getRandomMessage(self::DEFAULT_ERROR, 'error');
    }

    private function getRandomMessage($base, $type) {
        $messages = $this->session->get('converse.messages.' + $type);
        if (empty($messages)) {
            $messages = $base;
        }
        $randomKey = array_rand($messages, 1);
        $message = $messages[$randomKey];
        unset($messages[$randomKey]);
        $this->session->set('converse.messages.' . $type, $messages);
        return $message;
    }

    public function index(Request $request) {
        // No view are explicitely defined, so it will use View\Chat::index()

        if (!$request->isAjax()) {

            // We are starting a new conversation
            $this->session->set('converse.id', str_replace('.', '', uniqid('', true)));
            $this->session->delete('converse.entities');
            $this->session->delete('converse.messages.success');
            $this->session->delete('converse.messages.error');

        } else {

            $post = $request->getAjax();
            $message = $post['message'];
            $id = $this->session->get('converse.id');
            $entities = $this->session->get('converse.entities');

            $data = [
                'message' => null,
                'gif' => null,
                'movie' => null,
                'debug' => null,
            ];

            $talk = $this->api->wit->talk($id, $message);
            if (DEBUG) $data['debug'] = $talk;

            $response = $talk['msg'];
            $entities = array_merge(is_array($entities) ? $entities : [], $talk['entities']);
            $this->session->set('converse.entities', $entities);

            $data['entities'] = $entities;

            if (empty($response)) {
                if ($talk['success']) {
                    // We have entities (= keywords to search), but not specific response
                    $response = $this->getRandomSuccess();
                } else {
                    $response = $this->getRandomError();
                    $json['gif'] = $this->api->giphy->get('nope');
                }
            }

            if (is_string($response)) {
                $response = str_replace([
                    ';-)',
                    ';)',
                    ':-(',
                    ':(',
                ], [
                    '😉',
                    '😉',
                    '😞',
                    '😞',
                ], $response);
            }

            $data['message'] = $response;


            // $data['message'] = call_user_func(function () {
            //     $msg = [
            //         'Please wait..',
            //         'I am Oscar ! 👻',
            //         'What do you want ?',
            //         'Ok dude.',
            //         'You are ugly.',
            //         'I love potatoes !',
            //         'STOP TALKING TO ME.',
            //     ];
            //     return $msg[array_rand($msg)];
            // });

            // if (stripos($message, 'gif') > -1) {
            //     if (stripos($message, 'gif ') === 0) {
            //         $data['gif'] = $this->api->giphy->get(substr($message, 4));
            //     } else {
            //         $data['gif'] = $this->api->giphy->get('happy');
            //     }
            // } else if (stripos($message, 'cat') > -1) {
            //     $data['gif'] = $this->api->giphy->get('cat');
            //     $data['message'] = 'I HEARD YOU SAY CAT ?';
            // } else if (stripos($message, 'i want to see a ') === 0) {
            //     $genre = substr($message, strlen('i want to see a '));
            //     $movies = $this->api->movies->findByGenre($genre);
            //     if (isset($movies->results) and count($movies->results) > 0) {

            //         $movie = $movies->results[ rand(0, count($movies->results) - 1) ];
            //         $data['message'] = 'What about ' . $movie->title . ' ?';

            //         $availability = $this->api->availability->get($movie->title);
            //         if (!empty($availability)) {
            //             $services = [];
            //             foreach ($availability as $key => $value) {
            //                 $services[] = ucfirst(substr($key, 0, strpos($key, '_')));
            //             }
            //             $data['message'] .= ' You can watch it on ' . implode(', ', $services) . '.';
            //         }

            //     } else {
            //         $data['message'] = 'I DONT KNOW WHAT KIND OF GENRE "' . $genre . '" IS';
            //     }
            // }

            $this->view($data);

        }

        $this->set('weather', $this->api->weather->getCurrentState());

    }

}
