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

    const PARAMS = [
        'hour',
        'actor',
        'country',
        'genre',
        'rating',
        'start_year',
        'end_year',
        'before_year',
        'after_year',
        'single_year',
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
            $this->session->delete('converse.suggestion.hash');
            $this->session->delete('converse.suggestion.list');
            $this->session->delete('converse.messages.error');
            $this->session->delete('converse.messages.error');

        } else {

            // Get what we have
            $post = $request->getAjax();
            $message = $post['message'];
            $action = $post['action'];
            $id = $this->session->get('converse.id');

            if ($action === 'converse') {
                $data = $this->converse($id, $message);
            } else if ($action === 'deny' or $action === 'already-seen') {
                $movie = $this->find();
                if ($movie === true) {
                    $data = ['message' => 'Please tell me more to have other suggestions!'];
                } else if ($movie === false) {
                    $data = ['message' => 'We did\'nt found any movies..'];
                } else {                
                    $data = ['movie' => $movie];
                }
            } else if ($action === 'accept') {
                var_dump($post['movie']); exit;
            }
            $this->view($data);
        
        }

        $this->set('weather', $this->api->weather->getCurrentState());

    }

    private function converse($id, $message) {
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
        // if (DEBUG) $data['debug'] = ['talk' => $talk];

        // Process entities
        foreach ($talk['entities'] as $name => $entity) {
            if (in_array($name, self::PARAMS)) {
                $entities[$name] = $entity;
            }
        }

        if (DEBUG) $data['debug']['entities'] = $entities;
        $this->session->set('converse.entities', $entities);

        // Check if we have enough entities
        if ($talk['success']) {
            $count = 0;
            $hasYear = false;
            foreach ($entities as $name => $entity) {
                if (strpos($name, 'year') !== false) {
                    if (!$hasYear) {
                        $hasYear = true;
                        $count++;
                    }
                } else {
                    $count++;
                }
            }

            if ($count >= 2) {
                // Lets fetch a movie !
                $movie = $this->find($entities);

                if (is_array($movie) or is_object($movie)) {
                    $data['movie'] = $movie;
                } else if (DEBUG) {
                    $data['debug']['movie'] = $movie;
                }
            }
        }

        // Chat response
        $response = $talk['msg'];

        if (empty($response)) {
            if ($talk['success']) {
                // Bot undestood, but not specific response
                $response = $this->getRandomSuccess();
            } else {
                $response = $this->getRandomError();
                $data['gif'] = $this->api->giphy->get('nope');
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
        return $data;

    }

    private function find($entities = null) {
        $params = [];

        if (!isset($entities)) {
            $entities = $this->session->get('converse.entities');
        }

        foreach ($entities as $name => $entity) {
            switch ($name) {
                case 'actor':
                    $id = $this->api->movies->getPeopleId($entity);
                    if ($id) $params['with_people'] = $id;
                    break;
                
                case 'country':
                    break;
                
                case 'genre':
                    $id = $this->api->movies->getGenreId($entity);
                    if ($id) $params['with_genres'] = $id;
                    break;
                
                case 'rating':
                    if (ctype_digit($entity)) $params['vote_average.gte'] = $entity;
                    break;
                
                case 'start_year':
                    $paramName = 'primary_release_date.gte';
                case 'end_year':
                    $paramName = 'primary_release_date.lte';
                case 'before_year':
                    $paramName = 'primary_release_date.lte';
                case 'after_year':
                    $paramName = 'primary_release_date.gte';
                case 'single_year':
                    $paramName = 'year';
                    $entity = trim(str_replace(['before','after'], '', $entity));

                    if (ctype_digit($entity) and strlen($entity) === 4) $params[$paramName] = $entity;
                    break;
                
                default: break;
            }
        }

        $movies = $this->api->movies->find($params);
        if (is_array($movies)) {

            $hash = hash('sha256', serialize($params));
            $index = $this->session->get('converse.suggestion.list');

            if (!is_int($index) or $hash !== $this->session->get('converse.suggestion.hash'))
                $index = 0;

            if (isset($movies[$index])) {
                $movie = $movies[$index];
                $this->session->set('converse.suggestion.list', $index + 1);
                $this->session->set('converse.suggestion.hash', $hash);
                $id = reset($movie->genre_ids);
                $movie->genre_name = $this->api->movies->getGenreName($id);
                return $movie;
            } else {
                // End of list
                return true;
            }

        }
        // Nothing found
        return false;
    }

}
