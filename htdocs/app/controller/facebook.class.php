<?php

namespace Controller;
use Base\Controller;
use Model\Service\Request;

/**
 * Facebook connect
 */
class Facebook extends Controller {

    public function logout(Request $request) {
        $this->auth->logout();
        $this->flash->success('Déconnexion de Facebook réussie !');

        $redirect = $request->getQuery('redirect');

        if (!empty($redirect))
            $request->redirect($redirect);
        else
            $request->redirect();
    }

    public function connect(Request $request) {
        if ($this->auth->connect()) {
            $this->flash->success('Connexion à Facebook réussie !');
        } else {
            $this->flash->error('Impossible de se connecter à Facebook..');
        }

        $redirect = $request->getQuery('redirect');

        if (!empty($redirect))
            $request->redirect($redirect);
        else
            $request->redirect();
    }
}
