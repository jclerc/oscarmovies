<?php

namespace Model\Service;
use Model\Base\Service;
use Model\Domain\User;

/**
 * Auth service
 */
class Auth extends Service {

    const ACCESS_ID = 'fb_access_id';
    const ACCESS_TOKEN = 'fb_access_token';

    private $user = null;

    public function isLogged() {
        return $this->getUser()->exists();
    }

    public function getLoginUrl() {
        $helper = $this->facebook->getRedirectLoginHelper();

        // Optional permissions
        $permissions = ['email'];

        $redirect = urlencode($this->request->getRouteUrl());
        return $helper->getLoginUrl($this->request->getBaseUrl() . 'facebook/connect/?redirect=' . $redirect, $permissions);
    }

    public function getLogoutUrl() {
        $redirect = urlencode($this->request->getRouteUrl());
        return $this->request->getBaseUrl() . 'facebook/logout/?redirect=' . $redirect;
    }

    public function getUser(/* $property = null */) {
        if (!isset($this->user)) {
            $user = $this->di->create(User::class);
            if ($this->session->has(self::ACCESS_ID)) {
                $user->fromProperty('id', $this->session->get(self::ACCESS_ID));
            }
            $this->user = $user;
        }
        // if (isset($property))
        //     return $this->user->get($property);
        // else
            return $this->user;
    }

    public function connect() {
        $fb = $this->facebook;

        try {
            $helper = $fb->getRedirectLoginHelper();
            $accessToken = $helper->getAccessToken();

            // The OAuth 2.0 client handler helps us manage access tokens
            $oAuth2Client = $fb->getOAuth2Client();

            // Get the access token metadata from /debug_token
            $tokenMetadata = $oAuth2Client->debugToken($accessToken);

            // Validation (these will throw FacebookSDKException's when they fail)
            $tokenMetadata->validateAppId('1372343316125328');

            // If you know the user ID this access token belongs to, you can validate it here
            //$tokenMetadata->validateUserId('123');
            $tokenMetadata->validateExpiration();

            if (!$accessToken->isLongLived()) {
                // Exchanges a short-lived access token for a long-lived one
                try {
                    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);

                    // And process connection
                    return $this->processConnect($accessToken);

                } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                    // echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
                }
            } else {
                // And process connection
                return $this->processConnect($accessToken);
            }

        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            // echo 'Graph returned an error: ' . $e->getMessage();
            // echo 'Error: ' . $helper->getError();
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            // echo 'Facebook SDK returned an error: ' . $e->getMessage();
            // echo 'Error: ' . $helper->getError();
        }

        return false;

    }

    public function logout() {
        $this->session->delete(self::ACCESS_ID);
        $this->session->delete(self::ACCESS_TOKEN);
    }

    private function processConnect($token) {
        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $this->facebook->get('/me?fields=id,first_name,last_name,gender,email,picture.width(200).height(200)', $token);
            $graph = $response->getGraphUser();

            $user = $this->di->create(User::class);
            $user->fromProperty('facebook_id', $graph->getId());

            if ($user->exists()) {
                $user->set([
                    'first_name'  => $graph->getFirstName(),
                    'last_name'   => $graph->getLastName(),
                    'email'       => $graph->getEmail(),
                    'picture'     => $graph->getPicture()->getUrl(),
                ]);
            } else {
                $user->create([
                    'facebook_id' => $graph->getId(),
                    'first_name'  => $graph->getFirstName(),
                    'last_name'   => $graph->getLastName(),
                    'email'       => $graph->getEmail(),
                    'picture'     => $graph->getPicture()->getUrl(),
                ]);
            }

            $user->save();
            $this->user = $user;

            $this->session->set(self::ACCESS_ID, $user->getId());
            $this->session->set(self::ACCESS_TOKEN, (string) $token);

            return $user->exists();

        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            // echo 'Graph returned an error: ' . $e->getMessage();
            // exit;
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            // echo 'Facebook SDK returned an error: ' . $e->getMessage();
            // exit;
        }

        return false;
    }


}
