<?php
/**
 * Class ApplicationActions
 *
 * @package     CRUDService
 * @subpackage  ApplicationActions
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/12)
 * @copyright   Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../../../gentella-template/gentella-template.php');
require_once (__DIR__ . '/../../../gui/vendor/fields-algorithms/fields-algorithms.php');
require_once (__DIR__ . '/../../../gui/vendor/list-builder/list-builder.php');
require_once (__DIR__ . '/../../../gui/vendor/list-builder/vendor/crud-service-client-adapter/crud-service-client-adapter.php');
require_once (__DIR__ . '/../../../service/vendor/service-client/service-client.php');

require_once (__DIR__ . '/../crud-service-client/crud-service-client.php');

define('FIELD_NAME_DOMAIN_ID', 'domain_id');

/**
 * Class for basic CRUD client
 */
class ApplicationActions
{

    /**
     * Entity nam
     */
    var $EntityName = '';

    /**
     * Entity name for method names
     */
    var $SafeEntityName = '';

    /**
     * Show create button
     */
    var $CreateButton = false;

    /**
     * Show update button
     */
    var $UpdateButton = false;

    /**
     * Show delete button
     */
    var $DeleteButton = false;

    /**
     * Service client
     *
     * @var CRUDServiceClient
     */
    var $CRUDServiceClient = null;

    /**
     * Constructor
     *
     * @param string $EntityName
     *            Entity name
     * @param string $Login
     *            Login
     * @param string $Password
     *            Password
     */
    public function __construct(string $EntityName, string $Login = '', string $Password = '')
    {
        $this->CRUDServiceClient = new CRUDServiceClient($EntityName, $Login, $Password);

        $this->EntityName = $EntityName;

        $this->SafeEntityName = str_replace('-', '_', $EntityName);
    }

    /**
     * Method adds page parts to the result
     *
     * @param array $Result
     *            View generation result
     * @param CommonApplication $AppObject
     *            Application object
     * @return array Compiled view
     */
    protected function add_page_parts(array $Result, CommonApplication &$AppObject): array
    {
        if (method_exists($AppObject, 'cross_render')) {
            $Result = array_merge($Result, $AppObject->cross_render());
        }

        return ($Result);
    }

    /**
     * Method adds end-point for list displaying to the application object
     *
     * @param CommonApplication $AppObject
     *            CommonApplication object
     * @param string $Route
     *            Route
     * @param string $Callback
     *            Callback name
     * @param string|array $Method
     *            HTTP method name GET or POST
     */
    protected function load_route(CommonApplication &$AppObject, string $Route, string $Callback, $Method)
    {
        $AppObject->load_route([
            'route' => $Route,
            'callback' => $Callback,
            'method' => $Method
        ]);
    }

    /**
     * List builder creation function
     *
     * @param array $Options
     * @return ListBuilder
     */
    protected function create_list_builder(array $Options): ListBuilder
    {
        // create adapter
        $CRUDServiceClientAdapter = new CRUDServiceClientAdapter();
        $CRUDServiceClientAdapter->set_client($this->CRUDServiceClient);

        if (isset($Options['default-fields']) === false) {
            throw (new Exception('List of fields must be defined in the $Options[\'default-fields\']', - 1));
        }

        // create list builder
        $ListBuilder = new ListBuilder(explode(',', $Options['default-fields']), $CRUDServiceClientAdapter);

        return ($ListBuilder);
    }

    /**
     * Method adds end-point for list displaying to the application object
     *
     * @param CommonApplication $AppObject
     *            CommonApplication object
     * @param array $Options
     *            Options
     */
    public function attach_list_page(CommonApplication &$AppObject, array $Options)
    {
        $MethodName = $this->SafeEntityName . '_listing_page';

        $this->load_route($AppObject, $this->EntityName . '/list/', $MethodName, 'GET');

        $Options = $Options === false ? [] : $Options;

        $Options['create_button'] = $this->CreateButton ? 1 : 0;
        $Options['update_button'] = $this->UpdateButton ? 1 : 0;
        $Options['delete_button'] = $this->DeleteButton ? 1 : 0;

        $Options[FIELD_NAME_DOMAIN_ID] = $this->get_self_id();

        $AppObject->$MethodName = function () use ($AppObject, $Options) {
            $ListBuilder = $this->create_list_builder($Options);

            // generate list
            $Result = [
                'main' => $ListBuilder->listing_form()
            ];

            // add page parts
            return ($this->add_page_parts($Result, $AppObject, $Options));
        };
    }

    /**
     * Method adds end-point for list displaying to the application object
     *
     * @param CommonApplication $AppObject
     *            CommonApplication object
     * @param array $Options
     *            Options
     */
    public function attach_simple_list_page(CommonApplication $AppObject, array $Options)
    {
        $MethodName = $this->SafeEntityName . '_simple_listing_page';

        $this->load_route($AppObject, $this->EntityName . '/list/simple/', $MethodName, 'GET');

        $Options = $Options === false ? [] : $Options;

        $Options[FIELD_NAME_DOMAIN_ID] = $this->get_self_id();

        $AppObject->$MethodName = function () use ($AppObject, $Options) {
            $ListBuilder = $this->create_list_builder($Options);

            // generate list
            $Result = [
                'main' => $ListBuilder->simple_listing_form()
            ];

            // add page parts
            return ($this->add_page_parts($Result, $AppObject, $Options));
        };
    }

    /**
     * Method adds end-point for deleting record to the application object
     *
     * @param CommonApplication $AppObject
     *            CommonApplication object
     * @param array $Options
     *            Options
     */
    public function attach_delete_record(CommonApplication &$AppObject, array $Options)
    {
        $this->DeleteButton = true;

        $MethodName = $this->SafeEntityName . '_delete_record';

        $this->load_route($AppObject, $this->EntityName . '/delete/[i:id]/', $MethodName, 'GET');

        $Options = $Options === false ? [] : $Options;

        $Options[FIELD_NAME_DOMAIN_ID] = $this->get_self_id();

        $AppObject->$MethodName = function (...$Params) use ($AppObject, $Options) {
            $this->CRUDServiceClient->delete($Params[1]['id'], $Options[FIELD_NAME_DOMAIN_ID]);

            $AppObject->redirect_to('../../list/');
        };
    }

    /**
     * Generating form
     *
     * @param string $Type
     *            Form type
     * @param int $id
     *            id of the updating record
     * @return array Compiled result
     */
    protected function get_compiled_form(string $Type = 'creation', int $id = 0): array
    {
        // get fields
        $Data = $this->CRUDServiceClient->get_remote_creation_form_fields();

        // construct $FieldsAlgorithms
        $FieldsAlgorithms = new FieldsAlgorithms(Functional::get_field($Data, 'fields'), $this->EntityName);

        // create form builder object
        $FormBuilder = new FormBuilder($FieldsAlgorithms, false, $this->EntityName, Functional::get_field($Data, 'layout'));

        // compile form
        if ($Type == 'creation') {
            $Result = [
                'main' => $FormBuilder->creation_form()
            ];
        } else {
            $Result = [
                'main' => $FormBuilder->updating_form($this->CRUDServiceClient->get_session_id(), $this->CRUDServiceClient->get_by_id($id, $this->get_self_id()))
            ];
        }

        return ($Result);
    }

    /**
     * Method gets create record controller for the remote service
     *
     * @param CommonApplication $AppObject
     *            CommonApplication object
     * @param array $Options
     *            Options
     */
    protected function add_create_record_method(CommonApplication &$AppObject, array $Options)
    {
        $MethodName = $this->SafeEntityName . '_create_record';

        $AppObject->$MethodName = function () use ($AppObject, $Options) {
            if (count($_POST) > 0) {
                $_POST[FIELD_NAME_DOMAIN_ID] = $this->get_self_id();

                $Data = $this->pretransform_data(array_merge($_POST, $_FILES));

                $this->CRUDServiceClient->create($Data);

                $AppObject->redirect_to('../list/');
            } else {
                return ($this->add_page_parts($this->get_compiled_form(), $AppObject, $Options));
            }
        };
    }

    /**
     * Method adds end-point for creating record to the application object
     *
     * @param CommonApplication $AppObject
     *            CommonApplication object
     * @param array $Options
     *            Options
     */
    public function attach_create_record(CommonApplication &$AppObject, array $Options)
    {
        $this->CreateButton = true;

        $Options = $Options === false ? [] : $Options;

        $Route = isset($Options['create-page-endpoint']) ? $Options['create-page-endpoint'] : $this->EntityName . '/create/';

        $this->load_route($AppObject, $Route, $this->SafeEntityName . '_create_record', [
            'POST',
            'GET'
        ]);

        $this->add_create_record_method($AppObject, $Options);
    }

    /**
     * Method gets update record controller for the remote service.
     *
     * @param CommonApplication $AppObject
     *            CommonApplication object
     * @param array $Options
     *            Options
     */
    protected function add_update_record_method(CommonApplication &$AppObject, array $Options)
    {
        $MethodName = $this->SafeEntityName . '_update_record';

        $AppObject->$MethodName = function (...$Params) use ($AppObject, $Options) {
            if (count($_POST) > 0) {
                $_POST[FIELD_NAME_DOMAIN_ID] = $this->get_self_id();

                $this->post_request('/update/' . $Params[1]['id'] . '/', $_POST);

                $this->CRUDServiceClient->update($Params[1]['id'], $_POST, $_POST[FIELD_NAME_DOMAIN_ID]);

                $AppObject->redirect_to('../../list/');
            } else {
                return ($this->add_page_parts($this->get_compiled_form('updating', $Params[1]['id']), $AppObject, $Options));
            }
        };
    }

    /**
     * Method adds end-point for updating record to the application object
     *
     * @param CommonApplication $AppObject
     *            CommonApplication object
     * @param array $Options
     *            Options
     */
    public function attach_update_record(CommonApplication &$AppObject, array $Options)
    {
        $this->UpdateButton = true;

        $Options = $Options === false ? [] : $Options;

        $Route = isset($Options['update-record-endpoint']) ? $Options['update-record-endpoint'] : $this->EntityName . '/update/[i:id]/';

        $this->load_route($AppObject, $Route, $this->SafeEntityName . '_update_record', [
            'POST',
            'GET'
        ]);

        $this->add_update_record_method($AppObject, $Options);
    }
}

?>