<?php

class LogsController extends OlogAppController {

    var $name = 'Logs';

    function beforeFilter() {
        parent::beforeFilter();
        $this->LogAuth->allowedActions = array('index', 'add', 'view', 'timespanChange', 'addproperty');
    }

    function index() {
        $this->data['Log'] = $this->passedArgs;
        $this->paginate['Log'] = array(
            'logs',
            'conditions' => $this->passedArgs,
        );

        $this->set('logs', $this->paginate('Log'));

        $levels = array("Info" => "Info",
            "Problem" => "Problem",
            "Request" => "Request",
            "Suggestion" => "Suggestion",
            "Urgent" => "Urgent");

        Controller::loadModel('Logbook');
        $logbooks = $this->Logbook->find('list');
        $this->set('logbooks', $logbooks);

        Controller::loadModel('Tag');
        $tags = $this->Tag->find('list');

        $argumentString = '';
        foreach ($this->passedArgs as $argumentName => $argumentValue) {
            $argumentString .= '/' . $argumentName . ':' . $argumentValue;
        }

        $this->set('session', $this->Session);
        $this->set('base', $this->base);
	
	if ($this->RequestHandler->isAjax()) {
		$this->render('/elements/paginateTable');
		return;
	}
        $this->set(compact('tags', 'levels', 'argumentString'));
    }

    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid log', true));
            $this->redirect(array('action' => 'index'));
        }
        $levels = array("Info" => "Info",
            "Problem" => "Problem",
            "Request" => "Request",
            "Suggestion" => "Suggestion",
            "Urgent" => "Urgent");

        Controller::loadModel('Logbook');
        $logbooks = $this->Logbook->find('list');
        $this->set('logbooks', $logbooks);

        Controller::loadModel('Tag');
        $tags = $this->Tag->find('list');
        $this->set('log', $this->Log->find('log', array('conditions' => array('id' => $id))));
        $this->set(compact('tags', 'levels'));
    }

    function add() {

        if (!empty($this->data)) {
            if (isset($this->data['log']['username']) && isset($this->data['log']['password'])) {
                $this->Log->request['auth']['user'] = $this->data['log']['username'];
                $this->Log->request['auth']['pass'] = $this->data['log']['password'];
            }

            if ($this->Log->save($this->data)) {
                $this->Session->setFlash(__('The log has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                // TODO - quick fix for tag validation error not showing up
                $print_error = "";
                foreach ($this->Log->validationErrors as $errorKey => $error) {
                    $print_error .= "For input " . $errorKey . ": " . $error . '<br>';
                }
                $this->Session->setFlash(__($print_error . 'The log could not be saved. Please, try again.', true));
                $this->redirect(array('action' => 'index'));
            }
        }
    }

    function edit($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid log', true));
            $this->redirect(array('action' => 'index'));
        }

        if (!empty($this->data)) {
            $properties = json_decode($this->data['log']['properties'], true);
            foreach ($properties as $properties_index => $property) {
                if (isset($property['name'])) {
                    if (!in_array($property['name'] . "_" . $property['id'] . "_" . $property['groupingNum'], $this->data['log']['properties_selected'])) {
                        unset($properties[$properties_index]);
                    }
                } else {
                    foreach ($property as $property_index => $prop) {
                        if (isset($prop['name'])) {
                            if (!in_array($prop['name'] . "_" . $prop['id'] . "_" . $prop['groupingNum'], $this->data['log']['properties_selected'])) {
                                unset($properties[$properties_index][$property_index]);
                            }
                        }
                    }
                }
            }
            unset($this->data['log']['properties']);
            $this->data['log']['properties'] = $properties;
            if ($this->Log->save($this->data)) {
                $this->Session->setFlash(__('The log has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The log could not be saved. Please, try again.', true));
            }
        } else {
            $this->data = $this->Log->find('log', array('conditions' => array('id' => $id)));
        }

        Controller::loadModel('Tag');
        $tags = $this->Tag->find('list');

        Controller::loadModel('Logbook');
        $logbooks = $this->Logbook->find('list');

        $this->set(compact('levels', 'tags', 'logbooks'));
    }

    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for log', true));
            $this->redirect(array('action' => 'index'));
        }
        if ($this->Log->delete($id)) {
            $this->Session->setFlash(__('Log deleted', true));
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(__('Log was not deleted', true));
            $this->redirect(array('action' => 'index'));
        }
    }

    /**
     * Adds a property to a log entry. Parameters are sent implicitly.
     * 
     * @return $flashMessage Success or failure message 
     */
    function addproperty() {
        $this->autoRender = false;

        $flashMessage = "";

        $logProperty = array();

        $logProperty['property']['attributes'] = array();
        foreach ($this->params['form'] as $key => $value) {
            if ($key == 'logId') {
                $logProperty['property']['log']['id'] = $value;
            } else if ($key == 'propName') {
                $logProperty['property']['name'] = $value;
            } else {
                $attribute['key'] = str_replace('_', ' ', $key);
                $attribute['value'] = $value;
                array_push($logProperty['property']['attributes'], $attribute);
            }
        }
        Controller::loadModel('Property');
        $saved = $this->Property->save($logProperty);
        //$saved = $this->Log->merge($logProperty);
        if ($saved) {
            $flashMessage = 'The property has been saved';
        } else {
            // TODO - quick fix for tag validation error not showing up
            foreach ($this->Log->validationErrors as $errorKey => $error) {
                $flashMessage .= "For input " . $errorKey . ": " . $error . '<br>';
            }
            $flashMessage . "The log could not be saved. Please, try again.";
        }

        return $flashMessage;
    }

    /**
     * Pass any timespan change arguments from the dropdown menu to the index page.
     * 
     * @param $newTimeSpan Incoming timespan selection
     */
    function timespanChange($newTimeSpan = null) {
        $startDate = null;
        $endDate = date('U');

        $argumentString = '';
        foreach ($this->passedArgs as $argumentName => $argumentValue) {
            if ($argumentName != '0' && $argumentName != 'start' && $argumentName != 'end') {
                $argumentString .= '/' . $argumentName . ':' . $argumentValue;
            }
        }

        switch ($newTimeSpan) {
            case 0:
                $this->redirect('/olog/logs/index/' . $argumentString);
            case 1: // Last day
                $startDate = mktime(0, 0, 0, date('m'), date('d') - 1, date('y'));
                break;
            case 2: // Last 3 Days
                $startDate = mktime(0, 0, 0, date('m'), date('d') - 3, date('y'));
                break;
            case 3: // Last week
                $startDate = mktime(0, 0, 0, date('m'), date('d') - 7, date('y'));
                break;
            case 4: // Last month
                $startDate = mktime(0, 0, 0, date('m') - 1, date('d'), date('y'));
                break;
            case 5: // Last 3 Months
                $startDate = mktime(0, 0, 0, date('m') - 3, date('d'), date('y'));
                break;
            case 6: // Last 6 Months
                $startDate = mktime(0, 0, 0, date('m') - 6, date('d'), date('y'));
                break;
            case 7: // Last year
                $startDate = mktime(0, 0, 0, date('m'), date('d'), date('y') - 1);
                break;
        }

        $this->redirect('/olog/logs/index/start:' . $startDate . '/end:' . $endDate . $argumentString);
    }

}

?>
