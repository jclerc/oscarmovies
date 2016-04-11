<?php

namespace Controller;
use Base\Controller;
use Model\Service\Request;

/**
 * Facebook connect
 */
class Facebook extends Controller {

    public function logout(Request $request) {
      $this->session->delete('fb_access_token');
      $request->redirect();
    }

    public function connect(Request $request) {

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
                    $this->flash->success('Connexion à Facebook réussie !');
                    $request->redirect();

                } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                    // echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
                }
            } else {
                // And success !
                $this->session->set('fb_access_token', (string) $accessToken);
                $this->flash->success('Connexion à Facebook réussie !');
                $request->redirect();
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

        // Go home dude
        $this->flash->error('Impossible de se connecter à Facebook..');
        $request->redirect();

    }
}
