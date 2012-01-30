<?php

class Log extends OlogAppModel {

    /**
     * The name of this model
     *
     * @var name
     */
    public $name = 'Log';

    /**
     * The custom find types available on the model
     * 
     * @var array
     */
    public $_findMethods = array(
        'logs' => true,
        'log' => true
    );

    /**
     * Finds logs matching the given criteria.
     *
     * Calls the Olog web service
     *
     * Called using Model::find('logs', $options)
     *
     *
     * Conditions can include
     * - logbook : String logbook name to search for
     * - tag : String tag name to search for
     * - search : String regex to search on
     * - {key} : {key} value to search on
     *
     * @param string $state 'before' or 'after'
     * @param array $query The query options passed to the Model::find() call
     * @param array $results The results from the call
     * @return array Either the modified query params or results depending on the
     *  value of the state parameter.
     */
    protected function _findLogs($state, $query = array(), $results = array()) {

        if ($state == 'before') {

            $this->request['uri']['path'] = 'logs';

            $this->request['uri']['query'] = $query['conditions'];

            return $query;
        } else {

            return $results;
        }
    }

    /**
     * Finds log matching the given id.
     *
     * Calls the Olog web service
     *
     * Called using Model::find('log', $options)
     *
     * $options can include the usual keys for 'conditions'
     *
     * Conditions can include
     * - id : an integer corresponding to the log id
     *
     * @param string $state 'before' or 'after'
     * @param array $query The query options passed to the Model::find() call
     * @param array $results The results from the call
     * @return array Either the modified query params or results depending on the
     *  value of the state parameter.
     */
    protected function _findLog($state, $query = array(), $results = array()) {

        if ($state == 'before') {

            if (!isset($query['conditions']['id'])) {
                return false;
            }

            $this->request['uri']['path'] = 'logs/' . $query['conditions']['id'];

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
    public function merge($data = null, $validate = true, $fieldList = array()) {
        foreach ($data['log'] as $field => $value) {
            list($fields[], $values[]) = array($field, $value);
        }

        $db = & ConnectionManager::getDataSource($this->useDbConfig);
        if (!empty($data['log']['id'])) {
            return (bool) $db->create($this, $fields, $values);
        }
        if (!$db->create($this, $fields, $values)) {
            $success = false;
        } else {
            $success = true;
        }
        return $success;
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
        foreach ($data['log'] as $field => $value) {
            list($fields[], $values[]) = array($field, $value);
        }

        $db = & ConnectionManager::getDataSource($this->useDbConfig);
        if (!empty($data['log']['id'])) {
            return (bool) $db->update($this, $fields, $values);
        }
        if (!$db->create($this, $fields, $values)) {
            $success = false;
        } else {
            $success = true;
        }
        return $success;
    }

    public function xmlFormater($fields, $values) {
        $body = '';
        if (is_array($fields) && is_array($fields)) {
            $body = '<?xml version="1.0" encoding="UTF-8" ?>';
            $level_keys = array_keys($fields, 'level');
            $id_keys = array_keys($fields, 'id');
            if (!isset($id_keys[0]))
                $body .= '<logs>';
            $body .='<log ' . (isset($level_keys[0]) ? 'level="' . $values[$level_keys[0]] . '"' : '') . (isset($id_keys[0]) ? ' id="' . $values[$id_keys[0]] . '">' : '>');
            foreach ($fields as $key => $field) {
                if ($field == 'description'/* || $field == 'subject' */)
                    $body .= '<' . $field . '><![CDATA[' . $values[$key] . ']]></' . $field . '>';
                if ($field == 'tags' || $field == 'logbooks' || $field == 'properties') {
                    if (is_array($values[$key])) {
                        $body .= '<' . $field . '>';
                        foreach ($values[$key] as $childKey => $child) {
                            if (is_array($child)) {
                                foreach ($child as $c) {
                                    if ($field == 'properties' && $childKey == 'property') {
                                        $body .= '<' . $childKey . ' id="' . $c['id'] . '" name="' . $c['name'] . '">';
                                        $body .= '<attributes>';
                                        foreach ($c['attributes']['entry'] as $attribute) {
                                            $body .= '<entry>';
                                            $body .= '<key>' . $attribute['key'] . '</key>';
                                            $body .= '<value>' . $attribute['value'] . '</value>';
                                            $body .= '</entry>';
                                        }
                                        $body .= '</attributes>';
                                        $body .= '</' . $childKey . '>';
                                    } else {
                                        $body .= '<' . strtolower(Inflector::singularize($field)) . ' name="' . $child . '"/>';
                                    }
                                }
                            } else {
                                if ($field == 'properties' && $childKey == 'property') {
                                    $body .= '<' . $childKey . ' id="' . $child['id'] . '" name="' . $child['name'] . '">';
                                    $body .= '<attributes>';
                                    foreach ($child['attributes']['entry'] as $attribute) {
                                        $body .= '<entry>';
                                        $body .= '<key>' . $attribute['key'] . '</key>';
                                        $body .= '<value>' . $attribute['value'] . '</value>';
                                        $body .= '</entry>';
                                    }
                                    $body .= '</attributes>';
                                    $body .= '</' . $childKey . '>';
                                } else {
                                    $body .= '<' . strtolower(Inflector::singularize($field)) . ' name="' . $child . '"/>';
                                }
                            }
                        }
                        $body .= '</' . $field . '>';
                    }
                }
            }
            $body .= "</log>";
            if (!isset($id_keys[0]))
                $body .= "</logs>";
        }
        return $body;
    }

}

?>