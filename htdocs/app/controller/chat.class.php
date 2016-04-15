<?php

namespace Controller;
use Base\Controller;
use Model\Service\Request;
use Model\Domain\Suggestion;

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

    const DEFAULT_SUGGESTION = [
        'Here is another suggestion! 😉',
        'What about this ? 🙃',
        'You should like this movie!',
        'I think this movie may interest you.',
        'I\'m sure you will enjoy this movie! 😀',
    ];

    const DEFAULT_NOTHING_FOUND = [
        'Sorry, no movies match what you told me.. 😢 <br>So, what do you want ?',
        'I can\'t find any movies.. <br>Let\'s start again !',
        'Nothing found.. Maybe you were a bit too specific.. 🤔 <br>So, tell me what do you want.',
        'Oops, no movies could be found with that.. <br>New try, what do you have in mind ?',
    ];

    const PARAMS = [
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

    private function getRandomNotFound() {
        return $this->getRandomMessage(self::DEFAULT_NOTHING_FOUND, 'not.found');
    }

    private function getRandomSuggestion() {
        return $this->getRandomMessage(self::DEFAULT_SUGGESTION, 'suggestion');
    }

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
            $message = isset($post['message']) ? $post['message'] : null;
            $action = isset($post['action']) ? $post['action'] : null;
            $id = $this->session->get('converse.id');

            if ($action === 'converse') {
                $data = $this->converse($id, $message);
            } else if ($action === 'deny' or $action === 'already-seen') {
                $movie = $this->find();
                if (is_bool($movie)) {
                    $data = ['message' => $this->getRandomNotFound()];
                } else {                
                    $data = [
                        'message' => $this->getRandomSuggestion(),
                        'movie' => $movie
                    ];
                }
            } else if ($action === 'accept') {
                $response = 'Great !';
                if (isset($post['movie']['title'])) {
                    $response .= ' I hope you will enjoy ' . $post['movie']['title'] . ' !';
                    $availability = $this->api->availability->get($post['movie']['title']);
                    if (!empty($availability)) {
                        $response .= '<br>You can view it on ';
                        $i = 0;
                        $last = count((array) $availability) - 1;
                        foreach ($availability as $service) {
                            if ($i === 0) {
                                
                            } else if ($i === $last) {
                                $response .= ', or ';
                            } else {
                                $response .= ', ';
                            }
                            $response .= '<a target="_blank" href="' . $service->direct_url . '">' . trim(str_ireplace('rental', '', $service->friendlyName)) . '</a>';
                            $i++;
                        }
                        $response .= '.';
                    }
                }
                if ($this->auth->isLogged() and isset($post['movie']) and isset($post['movie']['id'])) {
                    $suggestion = $this->di->create(Suggestion::class);
                    $suggestion->fromProperty('movie_id', $post['movie']['id']);
                    if (!$suggestion->exists()) {
                        $suggestion->create([
                            'user_id' => $this->auth->getUser()->getId(),
                            'movie_id' => $post['movie']['id'],
                            'movie_data' => $post['movie'],
                        ]);
                    } else {
                        $suggestion->setTime(time());
                    }
                    $suggestion->save();
                }
                $data = ['message' => $response];
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

        // Retrieve message
        $response = $talk['msg'];

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
                } else if (is_bool($movie)) {
                    $response = $this->getRandomNotFound();
                    $this->session->set('converse.entities', []);
                }
            }
        }

        // Chat response
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
