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

            // Get what we have
            $post = $request->getAjax();
            $message = $post['message'];
            $id = $this->session->get('converse.id');
            $entities = $this->session->get('converse.entities');
            if (!is_array($entities)) $entities = [];

            // Prepare response
            $data = [
                'message' => null,
                'gif' => null,
                'movie' => null,
            ];

            // Call api
            $talk = $this->api->wit->talk($id, $message);
            if (DEBUG) $data['debug'] = $talk;

            // Process entities
            foreach ($talk['entities'] as $entity) {
                # code...
            }

            $data['entities'] = $entities;
            $this->session->set('converse.entities', $entities);

            // Chat response
            $response = $talk['msg'];

            if (empty($response)) {
                if ($talk['success']) {
                    // Bot undestood, but not specific response
                    $response = $this->getRandomSuccess();
                } else {
                    $response = $this->getRandomError();
                    $json['gif'] = $this->api->giphy->get('nope');
                }
            }

            $response = str_replace([';-)', ';)', ':-(', ':('], ['😉', '😉', '😞', '😞'], $response);

            if (stripos($message, 'gif') === 0) {
                $gif = substr($message, 3);
                $response = '';
                $data['gif'] = $this->api->giphy->get(trim($gif) ?: 'happy');
            }

            $data['message'] = $response;

            // And pass data to AJAX
            $this->view($data);

        }

        $this->set('weather', $this->api->weather->getCurrentState());

    }

}
