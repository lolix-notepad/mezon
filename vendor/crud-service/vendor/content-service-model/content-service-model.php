<?php

/**
 * Class ContentServiceModel
 *
 * @package     CRUDService
 * @subpackage  ContentServiceModel
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/11/06)
 * @copyright   Copyright (c) 2019, aeon.org
 */
// TODO add namespace
// TODO add camel-case
require_once (__DIR__ . '/../../../service/vendor/db-service-model/db-service-model.php');
/**
 * Model for content entities
 *
 * @author Dodonov A.A.
 */
class ContentServiceModel extends DBServiceModel
{

    /**
     * Method increments ammount of views
     *
     * @param integer $id
     *            Id of the record
     */
    public function increment_views($id): void
    {
        $Where = [
            'id = ' . intval($id)
        ];

        $Connection = $this->get_connection();

        $Connection->update($this->TableName, [
            'views' => 'INC'
        ], implode(' AND ', $Where));
    }
}

?>