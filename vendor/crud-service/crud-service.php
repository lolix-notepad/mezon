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

// TODO add camel-case
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
            parent::__construct($ServiceTransport, $SecurityProvider, $ServiceLogic, $this->init_model($Entity, $ServiceModel));
        } catch (\Exception $e) {
            $this->ServiceTransport->handle_exception($e);
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
    protected function init_model(array $Entity, $ServiceModel)
    {
        $Fields = isset($Entity['fields']) ? $Entity['fields'] : $this->get_fields_from_config();

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
    protected function get_fields_from_config()
    {
        if (file_exists('./conf/fields.json')) {
            return (json_decode(file_get_contents('./conf/fields.json'), true));
        }

        throw (new \Exception('fields.json was not found'));
    }

    /**
     * Method inits common servoce's routes
     */
    protected function init_common_routes(): void
    {
        parent::init_common_routes();

        $this->ServiceTransport->add_route('/list/', 'list_record', 'GET');
        $this->ServiceTransport->add_route('/all/', 'all', 'GET');
        $this->ServiceTransport->add_route('/exact/list/[il:ids]/', 'exact_list', 'GET');
        $this->ServiceTransport->add_route('/exact/[i:id]/', 'exact', 'GET');
        $this->ServiceTransport->add_route('/fields/', 'fields', 'GET');
        $this->ServiceTransport->add_route('/delete/[i:id]/', 'delete_record', 'GET');
        $this->ServiceTransport->add_route('/delete/', 'delete_filtered', 'POST');
        $this->ServiceTransport->add_route('/create/', 'create_record', 'POST');
        $this->ServiceTransport->add_route('/update/[i:id]/', 'update_record', 'POST');
        $this->ServiceTransport->add_route('/new/from/[s:date]/', 'new_records_since', 'GET');
        $this->ServiceTransport->add_route('/records/count/', 'records_count', 'GET');
        $this->ServiceTransport->add_route('/last/[i:count]/', 'last_records', 'GET');
        $this->ServiceTransport->add_route('/records/count/[s:field]/', 'records_count_by_field', 'GET');
    }
}

?>