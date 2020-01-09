<?php
namespace Mezon;

/**
 * Class CrudService
 *
 * @package Mezon
 * @subpackage CrudService
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Class for custom crud service.
 *
 * @author Dodonov A.A.
 */
class CrudService extends Service
{

    /**
     * Constructor
     *
     * @param array $Entity
     *            Entity description
     * @param mixed $ServiceTransport
     *            Service's transport
     * @param mixed $SecurityProvider
     *            Service's security provider
     * @param mixed $ServiceLogic
     *            Service's logic
     * @param mixed $ServiceModel
     *            Service's model
     */
    public function __construct(
        array $Entity,
        $ServiceTransport = \Mezon\Service\ServiceRestTransport::class,
        $SecurityProvider = \Mezon\Service\ServiceMockSecurityProvider::class,
        $ServiceLogic = \Mezon\CrudService\CrudServiceLogic::class,
        $ServiceModel = \Mezon\CrudService\CrudServiceModel::class)
    {
        try {
            parent::__construct(
                $ServiceTransport,
                $SecurityProvider,
                $ServiceLogic,
                $this->initModel($Entity, $ServiceModel));

            $this->initCrudRoutes();
        } catch (\Exception $e) {
            $this->ServiceTransport->handleException($e);
        }
    }

    /**
     * Method inits service's model
     *
     * @param array $Entity
     *            Entity description
     * @param string|\Mezon\CrudService\CrudServiceModel $ServiceModel
     *            Service's model
     */
    protected function initModel(array $Entity, $ServiceModel)
    {
        $Fields = isset($Entity['fields']) ? $Entity['fields'] : $this->getFieldsFromConfig();

        if (is_string($ServiceModel)) {
            $this->Model = new $ServiceModel($Fields, $Entity['table-name'], $Entity['entity-name']);
        } else {
            $this->Model = $ServiceModel;
        }

        return ($this->Model);
    }

    /**
     * Method returns fields from config
     *
     * @return array List of fields
     */
    protected function getFieldsFromConfig()
    {
        if (file_exists('./conf/fields.json')) {
            return (json_decode(file_get_contents('./conf/fields.json'), true));
        }

        throw (new \Exception('fields.json was not found'));
    }

    /**
     * Method inits common servoce's routes
     */
    protected function initCrudRoutes(): void
    {
        $this->ServiceTransport->addRoute('/list/', 'listRecord', 'GET');
        $this->ServiceTransport->addRoute('/all/', 'all', 'GET');
        $this->ServiceTransport->addRoute('/exact/list/[il:ids]/', 'exactList', 'GET');
        $this->ServiceTransport->addRoute('/exact/[i:id]/', 'exact', 'GET');
        $this->ServiceTransport->addRoute('/fields/', 'fields', 'GET');
        $this->ServiceTransport->addRoute('/delete/[i:id]/', 'deleteRecord', 'GET');
        $this->ServiceTransport->addRoute('/delete/', 'deleteFiltered', 'POST');
        $this->ServiceTransport->addRoute('/create/', 'createRecord', 'POST');
        $this->ServiceTransport->addRoute('/update/[i:id]/', 'updateRecord', 'POST');
        $this->ServiceTransport->addRoute('/new/from/[s:date]/', 'newRecordsSince', 'GET');
        $this->ServiceTransport->addRoute('/records/count/', 'recordsCount', 'GET');
        $this->ServiceTransport->addRoute('/last/[i:count]/', 'lastRecords', 'GET');
        $this->ServiceTransport->addRoute('/records/count/[s:field]/', 'recordsCountByField', 'GET');
    }
}
