<?php

class UsersController extends OlogAppController {

    var $name = 'Users';
    var $uses = array();
    var $helpers = array('Html', 'Form', 'Session');

    /**
     * Login action.
     */
    function login() {
        if ($this->Session->read('LogAuth.User')) {
            $this->Session->setFlash('You are logged in!');
            $this->redirect('/', null, false);
        }
    }

    /**
     * Logout action.
     */
    function logout() {
        $this->Session->setFlash('Good-Bye');
        $this->redirect($this->LogAuth->logout());
    }

}

?>