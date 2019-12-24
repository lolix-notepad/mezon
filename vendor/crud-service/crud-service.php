<?php
namespace Mezon;

/**
 * Class CRUDService
 *
 * @package Mezon
 * @subpackage CRUDService
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../gui/vendor/fields-algorithms/fields-algorithms.php');
require_once (__DIR__ . '/../gui/vendor/form-builder/form-builder.php');

require_once (__DIR__ . '/vendor/crud-service-client/crud-service-client.php');

/**
 * Class for custom crud service.
 *
 * @author Dodonov A.A.
 */
class CRUDService extends Service
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
    public function __construct(array $Entity, $ServiceTransport = '\Mezon\Service\ServiceRESTTransport', $SecurityProvider = '\Mezon\Service\ServiceMockSecurityProvider', $ServiceLogic = '\Mezon\CRUDService\CRUDServiceLogic', $ServiceModel = '\Mezon\CRUDService\CRUDServiceModel')
    {
        try {
            parent::__construct($ServiceTransport, $SecurityProvider, $ServiceLogic, $this->initModel($Entity, $ServiceModel));

            $this->initCRUDRoutes();
        } catch (\Exception $e) {
            $this->ServiceTransport->handleException($e);
        }
    }

    /**
     * Method inits service's model
     *
     * @param array $Entity
     *            Entity description
     * @param string|\Mezon\CRUDService\CRUDServiceModel $ServiceModel
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
    protected function initCRUDRoutes(): void
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

?>