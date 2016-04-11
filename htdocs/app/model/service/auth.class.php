<?php

namespace Model\Service;
use Model\Base\Service;

/**
 * Auth service
 */
class Auth extends Service {

    const TOKEN_NAME = 'fb_access_token';

    public function isLogged() {
        return $this->session->has(self::TOKEN_NAME);
    }

    public function getToken() {
        return $this->session->get(self::TOKEN_NAME);
    }

    public function getLoginUrl() {
        $helper = $this->facebook->getRedirectLoginHelper();

        // Optional permissions
        $permissions = ['email'];

        return $helper->getLoginUrl('http://localhost:8888/facebook/connect/', $permissions);
    }

    public function getLogoutUrl() {
        return '/facebook/logout/';
    }

    public function getUser() {
        if ($this->isLogged()) {
            try {
                // Returns a `Facebook\FacebookResponse` object
                $response = $this->facebook->get('/me', $this->session->get('fb_access_token'));

                return $response->getGraphUser();

            } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                // echo 'Graph returned an error: ' . $e->getMessage();
                // exit;
            } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                // echo 'Facebook SDK returned an error: ' . $e->getMessage();
                // exit;
            }
        }
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

                    // Update token
                    $this->session->set('fb_access_token', (string) $accessToken);
                    return true;

                } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                    // echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
                }
            } else {
                // And success !
                $this->session->set('fb_access_token', (string) $accessToken);
                return true;
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
        $this->session->delete('fb_access_token');
    }

}
