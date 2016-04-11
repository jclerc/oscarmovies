<?php

namespace Controller;
use Base\Controller;
use Model\Service\Request;

/**
 * Index controller
 */
class Index extends Controller {

    public function index(Request $request) {
        // No view are explicitely defined, so it will use View\Index::index()

        if (isset($_SESSION['fb_access_token'])) {

            $fb = $this->facebook;

            try {
              // Returns a `Facebook\FacebookResponse` object
              $response = $fb->get('/me?fields=id,name', $_SESSION['fb_access_token']);
            } catch(\Facebook\Exceptions\FacebookResponseException $e) {
              // echo 'Graph returned an error: ' . $e->getMessage();
              // exit;
            } catch(\Facebook\Exceptions\FacebookSDKException $e) {
              // echo 'Facebook SDK returned an error: ' . $e->getMessage();
              // exit;
            }

            $user = $response->getGraphUser();

            $this->set('name', $user['name']);

        } else {
            $this->set('name', 'INCONNU');
        }


    }

}
