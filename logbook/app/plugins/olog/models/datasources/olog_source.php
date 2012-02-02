<?php

// Import the rest data source from the rest plugin
App::import('Datasource', 'Rest.RestSource');

class OlogSource extends RestSource {

    /**
     * Overloads the RestSource::request() method to add Olog API
     * specific elements to the request property of the passed model before
     * sending it off to the RestSource::request() method that actually issues the
     * request and decodes the response.
     *
     * @param AppModel $model The model the call was made on. Expects the model
     * object to have a request property in the form of HttpSocket::request
     * @return mixed
     */
    public function request(&$model) {
        App::import('Component', 'CakeSession');
        $session = new CakeSession();

        if (!isset($model->request['uri']['host'])) {
            $model->request['uri']['host'] = $this->config['host'];
            $model->request['uri']['port'] = $this->config['port'];
            $model->request['uri']['path'] = $this->config['path'] . '/' . $model->request['uri']['path'];
            $model->request['uri']['scheme'] = $this->config['scheme'];
            $model->request['header']['Content-Type'] = 'application/xml';
            $model->request['header']['X-Forwarded-For'] = $_SERVER['REMOTE_ADDR'];
            $model->request['auth']['method'] = 'Basic';
            $xmlObj = (isset($model->request['body'])) ? $model->request['body'] : '';
            $auth = $session->read('Log');
            if (isset($auth['username']) && isset($auth['bindPasswd'])) {
                $model->request['auth']['user'] = $auth['username'];
                $model->request['auth']['pass'] = $auth['bindPasswd'];
            }
        }

        $response = parent::request($model);

        return $response;
    }

    /**
     * Overloads method = POST in request if not already set
     * 
     * @param AppModel $model
     * @param array $fields 
     * @param array $values
     */
    public function create(&$model, $fields = null, $values = null) {
        $model->request['uri']['path'] = strtolower(Inflector::pluralize($model->name));
        
        $id_keys = array_keys($fields, 'id');
        if (is_array($fields) && isset($values[$id_keys[0]])) {
            $model->request['uri']['path'] = $model->request['uri']['path'] . '/' . $values[$id_keys[0]];
        }
        
//        $body = $model->xmlFormater($fields, $values);
        $model->request['body'] = $body;
        $response = parent::create($model, $fields, $values);
        return $response;
    }

    /**
     * Overloads method = GET in request if not already set
     *
     * @param AppModel $model
     * @param array $queryData Unused
     */
    public function read(&$model, $queryData = array()) {
        if (!isset($model->request['uri']['path'])) {
            $model->request['uri']['path'] = strtolower(Inflector::pluralize($model->name));
        }
        if (is_array($queryData) && isset($queryData['conditions'][$model->name . '.id'])) {
            $model->request['uri']['path'] = $model->request['uri']['path'] . '/' . $queryData['conditions'][$model->name . '.id'];
            unset($queryData['conditions'][$model->name . '.id']);
        }
        if (is_array($queryData['conditions'] && isset($model->request['uri']['query']))) {
            $model->request['uri']['query'] = $model->request = array_merge($queryData['conditions'], $model->request['uri']['query']);
        }
        if (is_array($queryData['conditions'] && !isset($model->request['uri']['query']))) {
            $model->request['uri']['query'] = $model->request = $queryData['conditions'];
        }

        $response = parent::read($model, $queryData);

        return $response;
    }

    /**
     * Overloads method = PUT in request if not already set
     *
     * @param AppModel $model
     * @param array $fields Unused
     * @param array $values Unused
     */
    public function update(&$model, $fields = null, $values = null) {

        if ($model->request['uri']['path'] == null) {
            $model->request['uri']['path'] = strtolower(Inflector::pluralize($model->name));

            $id_keys = array_keys($fields, 'id');
            if (is_array($fields) && isset($values[$id_keys[0]])) {
                $model->request['uri']['path'] = $model->request['uri']['path'] . '/' . $values[$id_keys[0]];
            }
        }

        $body = $model->xmlFormater($fields, $values);
        $model->request['body'] = $body;
        $response = parent::update($model, $fields, $values);
        return $response;
    }

    /**
     * Overloads method = DELETE in request if not already set
     *
     * @param AppModel $model
     * @param mixed $id Unused
     */
    public function delete(&$model, $id = null) {
        if (isset($id)) {
            $model->request['uri']['path'] = $model->request['uri']['path'] . '/' . $id;
        }
        $response = parent::delete($model, $id);
        return $response;
    }

}

?>