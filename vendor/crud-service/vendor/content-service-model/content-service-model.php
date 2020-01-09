<?php
namespace Mezon\Service;

/**
 * Class ContentServiceModel
 *
 * @package CrudService
 * @subpackage ContentServiceModel
 * @author Dodonov A.A.
 * @version v.1.0 (2019/11/06)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Model for content entities
 *
 * @author Dodonov A.A.
 */
class ContentServiceModel extends \Mezon\Service\DbServiceModel
{

    /**
     * Method increments ammount of views
     *
     * @param int $id
     *            Id of the record
     */
    public function incrementViews($id): void
    {
        $Where = [
            'id = ' . intval($id)
        ];

        $Connection = $this->getConnection();

        $Connection->update($this->TableName, [
            'views' => 'INC'
        ], implode(' AND ', $Where));
    }
}
