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

        if ($this->auth->isLogged()) {

            $user = $this->auth->getUser();

            if (!empty($user)) {
                $this->set('username', $user->getName());
            }

            $this->set('logoutUrl', $this->auth->getLogoutUrl());
        } else {
            $this->set('loginUrl', $this->auth->getLoginUrl());
        }

        $this->set('weather', $this->api->weather->getCurrent());

    }

}
