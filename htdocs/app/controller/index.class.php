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

        $this->set('username', 'INCONNU');

          $fb = $this->facebook;
          
          if ($this->session->has('fb_access_token')) {
            try {
                // Returns a `Facebook\FacebookResponse` object
                $response = $fb->get('/me', $this->session->get('fb_access_token'));

                $user = $response->getGraphUser();
                $this->set('username', $user->getName());

            } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                // echo 'Graph returned an error: ' . $e->getMessage();
                // exit;
            } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                // echo 'Facebook SDK returned an error: ' . $e->getMessage();
                // exit;
            }

        }

        $helper = $fb->getRedirectLoginHelper();

        $permissions = ['email']; // Optional permissions
        $this->set('loginUrl', $helper->getLoginUrl('http://localhost:8888/facebook/connect/', $permissions));
    }

}
