<?php

App::import('Component', 'Auth');

class LogAuthComponent extends AuthComponent {

    var $Log = null;

    /**
     * Initialize method ensures that the Auth methods remain working as is described in the CakePHP cookbook
     * while also allowing the use of the Log model.
     * 
     * @param object $controller A reference to the instantiating controller object
     * @param array $settings One or more options that the AuthComponent class takes in
     */
    function initialize(&$controller, $settings=array()) {

        // Create the log object that we will be using
        $this->Log = $controller->loadModel('Log');
        $this->Log = $controller->Log;

        // Pass data to the AuthComponent parent
        parent::initialize($controller, $settings);
    }

    /**
     * Main execution method.  Handles redirecting of invalid users, and processing
     * of login form data.
     *
     * @param object $controller A reference to the instantiating controller object
     * @return boolean success
     */
    function startup(&$controller) {

        $success = true;

        $methods = array_flip($controller->methods);

        $isErrorOrTests = (
                strtolower($controller->name) == 'cakeerror' ||
                (strtolower($controller->name) == 'tests' && Configure::read() > 0)
                );
        if ($isErrorOrTests) {
            return success;
        }

        $isMissingAction = (
                $controller->scaffold === false &&
                !isset($methods[strtolower($controller->params['action'])])
                );
        if ($isMissingAction) {
            return success;
        }

        if (!$this->__setDefaults()) {
            return!success;
        }

        $url = (isset($controller->params['url']['url'])) ? $controller->params['url']['url'] : '';
        $url = Router::normalize($url);

        $loginAction = Router::normalize($this->loginAction);

        $isAllowed = (
                $this->allowedActions == array('*') ||
                in_array($controller->params['action'], $this->allowedActions)
                );

        if ($loginAction != $url && $isAllowed) {
            return success;
        }

        // Handle login attempt
        if ($loginAction == $url) {
            if (empty($controller->data) || !isset($controller->data[$this->userModel])) {
                if (!$this->Session->check('Auth.redirect') && env('HTTP_REFERER')) {
                    $this->Session->write('Auth.redirect', $controller->referer(null, true));
                }
                return!success;
            }

            $isValid = !empty($controller->data[$this->userModel][$this->fields['username']]);
            if ($isValid) {
                $username = $controller->data[$this->userModel][$this->fields['username']];
                $password = $controller->data[$this->userModel][$this->fields['password']];
                $data = array(
                    $this->userModel => array(
                        'username' => $username,
                        'password' => $password,
                    ),
                );
                $data = $this->hashPasswords($data);

                if ($this->login($data, $password)) {
                    if ($this->autoRedirect) {
                        $controller->redirect($this->redirect(), null, true);
                    }
                    return success;
                } else {
                    $this->Session->setFlash(__('You are not authorized to use this application!', true));
                    $controller->redirect($this->redirect(), null, true);
                }
            }
            $this->Session->setFlash($this->loginError, 'default', array(), 'auth');
            $controller->data[$this->userModel][$this->fields['password']] = null;
            return!success;
        } else {
            if (!$this->user()) {
                $this->Session->setFlash($this->authError, 'default', array(), 'auth');
                $this->Session->write('Auth.redirect', $url);
                $controller->redirect($loginAction);
                return!success;
            }
        }


        if (!$this->authorize) {
            return success;
        }

        extract($this->__authType());
        switch ($type) {
            case 'controller':
                $this->object = & $controller;
                break;
            case 'crud':
            case 'actions':
                //TODO: ACL on actions?
                break;
            case 'model':
                if (!isset($object)) {
                    $hasModel = (
                            isset($controller->{$controller->modelClass}) &&
                            is_object($controller->{$controller->modelClass})
                            );
                    $isUses = (
                            !empty($controller->uses) && isset($controller->{$controller->uses[0]}) &&
                            is_object($controller->{$controller->uses[0]})
                            );

                    if ($hasModel) {
                        $object = $controller->modelClass;
                    } elseif ($isUses) {
                        $object = $controller->uses[0];
                    }
                }
                $type = array('model' => $object);
                break;
        }

        if ($this->isAuthorized($type)) {
            return success;
        }

        $this->Session->setFlash($this->authError, 'default', array(), 'auth');
        $controller->redirect($controller->referer(), null, true);
        return!success;
    }

    /**
     * Log the user into the system using the supplied credentials.
     *
     * @param $data User model data containing username and a hashed password
     * @param $clear User password
     * @return boolean $this->_loggedIn Were we able to log in?
     */
    function login($data, $clear=null) {
        $this->_loggedIn = false;

        $uid = $data[$this->userModel]['username'];
        $password = $clear;
        $this->__setDefaults();

        $loginResult = false;
        if (!is_null($password)) {
            $loginResult = $this->logauth($uid, $password);
        }

        if ($loginResult == true) {
            $this->_loggedIn = true;
            $data[$this->userModel][$this->fields['username']] = $uid;
            $data[$this->userModel][$this->fields['password']] = $this->password($password);
            $user_data = $data[$this->userModel];

            $user_data['bindPasswd'] = $password;
            $this->Session->write($this->sessionKey, $user_data);
            $this->Session->write('Log', $user_data);
            $this->Session->write('Auth.User.name', $uid);
        } else {
            $this->loginError = "LOGIN ERROR";
        }
        return $this->_loggedIn;
    }

    /**
     * Log a user out of the system.
     * @return boolean Was the logout successful?
     *
     */
    function logout() {
        $this->Session->delete('Log');
        $this->Session->delete('Auth.User.name');
        return parent::logout();
    }

    /**
     * Communicate with the olog datasource to determine whether the incoming user is authorized to use the system.
     *
     * @param $uid Unique user id (username)
     * @param $password
     * @return $result Was the authorization successful?
     */
    function logauth($uid, $password) {
        $result = false;

        $db = &ConnectionManager::getDataSource('olog');
        $this->Log->request['auth']['user'] = $uid;
        $this->Log->request['auth']['pass'] = $password;
        $this->Log->request['auth_request'] = true;
        if (!is_null($password)) {
            $result = $db->create($this->Log, null, null);
        }

        return $result;
    }

    /**
     * Validate a user's group - TODO: Parent is not working... not sure why
     *
     * @param $type Authorization type
     * @param $object
     * @param $user
     * @return $valid
     */
    function isAuthorized($type = null, $object = null, $user = null) {
        $valid = true;

        $actions = $this->__authType($type);
        if ($actions['type'] != 'actions') {
            return parent::isAuthorized($type, $object, $user);
        }
        if (empty($user) && !$this->user()) {
            return false;
        } elseif (empty($user)) {
            $user = $this->user();
        }

        return $valid;
    }

}

?>