<?php

class Property extends OlogAppModel {

    /**
     * The name of this model
     *
     * @var name
     */
    public $name = 'Property';

    /**
     * The custom find types available on the model
     * 
     * @var array
     */
    public $_findMethods = array(
        'properties' => true,
        'attributes' => true
    );

    /**
     * Find all properties
     *
     * Calls the Olog web service
     *
     * Called using Model::find('properties', $options)
     *
     *
     * Conditions can include
     *
     * @param string $state 'before' or 'after'
     * @param array $query The query options passed to the Model::find() call
     * @param array $results The results from the call
     * @return array Either the modified query params or results depending on the
     *  value of the state parameter.
     */
    protected function _findProperties($state, $query = array(), $results = array()) {

        if ($state == 'before') {

            $this->request['uri']['path'] = 'properties';

            $this->request['uri']['query'] = $query['conditions'];

            return $query;
        } else {

            return $results;
        }
    }

    /**
     * Finds attributes matching a property name.
     *
     * Calls the Olog web service
     *
     * Called using Model::find('attributes', $options)
     *
     * $options can include the usual keys for 'conditions'
     *
     * Conditions can include
     * - name : a string corresponding to a property name
     *
     * @param string $state 'before' or 'after'
     * @param array $query The query options passed to the Model::find() call
     * @param array $results The results from the call
     * @return array Either the modified query params or results depending on the
     *  value of the state parameter.
     */
    protected function _findAttributes($state, $query = array(), $results = array()) {

        if ($state == 'before') {

            if (!isset($query['conditions']['name'])) {
                return false;
            }

            $this->request['uri']['path'] = 'properties/' . $query['conditions']['name'];

            return $query;
        } else {

            return $results;
        }
    }

    /**
     * 
     *
     * @param $data
     * @param $validate
     * @param $fieldList
     * 
     * @return $success
     */
    public function save($data = null, $validate = true, $fieldList = array()) {
        foreach ($data['property'] as $field => $value) {
            list($fields[], $values[]) = array($field, $value);
        }

        $db = & ConnectionManager::getDataSource($this->useDbConfig);
        if (!empty($data['property']['name']) && !empty($data['property']['log']['id'])) {
            $this->request['uri']['path'] = 'properties/' . $data['property']['name'] . '/' . $data['property']['log']['id'];
            return var_dump($db->update($this, $fields, $values));
            return (bool) $db->update($this, $fields, $values);
        } else {
            return false;
        }
    }

    public function xmlFormater($fields, $values) {
        $body = '';
        if (is_array($fields) && is_array($fields)) {
            $body = '<?xml version="1.0" encoding="UTF-8" ?>';
            $name_keys = array_keys($fields, 'name');
            $body .='<property ' . (isset($name_keys[0]) ? 'name="' . $values[$name_keys[0]] . '"' : '') . '>';
            foreach ($fields as $key => $field) {
                if ($field == 'attributes') {
                    $body .= '<attributes>';
                    foreach ($values[$key] as $attribute) {
                        $body .= '<entry>';
                        $body .= '<key>' . $attribute['key'] . '</key>';
                        $body .= '<value>' . $attribute['value'] . '</value>';
                        $body .= '</entry>';
                    }
                    $body .= '</attributes>';
                }
            }
            $body .= '</property>';
        }
        return $body;
    }

}

?>