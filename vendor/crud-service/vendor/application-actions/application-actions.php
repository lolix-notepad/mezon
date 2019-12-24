<?php
namespace Mezon\CRUDService;
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
     * @var \Mezon\CRUDService\CRUDServiceClient
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
     * @param \Mezon\CommonApplication $AppObject
     *            Application object
     * @return array Compiled view
     */
    protected function addPageParts(array $Result, \Mezon\CommonApplication &$AppObject): array
    {
        if (method_exists($AppObject, 'crossRender')) {
            $Result = array_merge($Result, $AppObject->crossRender());
        }

        return ($Result);
    }

    /**
     * Method adds end-point for list displaying to the application object
     *
     * @param \Mezon\CommonApplication $AppObject
     *            CommonApplication object
     * @param string $Route
     *            Route
     * @param string $Callback
     *            Callback name
     * @param string|array $Method
     *            HTTP method name GET or POST
     */
    protected function loadRoute(\Mezon\CommonApplication &$AppObject, string $Route, string $Callback, $Method)
    {
        $AppObject->loadRoute([
            'route' => $Route,
            'callback' => $Callback,
            'method' => $Method
        ]);
    }

    /**
     * List builder creation function
     *
     * @param array $Options
     * @return \Mezon\GUI\ListBuilder
     */
    protected function createListBuilder(array $Options): \Mezon\GUI\ListBuilder
    {
        // create adapter
        $CRUDServiceClientAdapter = new \Mezon\GUI\ListBuilder\CRUDServiceClientAdapter();
        $CRUDServiceClientAdapter->setClient($this->CRUDServiceClient);

        if (isset($Options['default-fields']) === false) {
            throw (new \Exception('List of fields must be defined in the $Options[\'default-fields\']', - 1));
        }

        // create list builder
        $ListBuilder = new \Mezon\GUI\ListBuilder(explode(',', $Options['default-fields']), $CRUDServiceClientAdapter);

        return ($ListBuilder);
    }

    /**
     * Method adds end-point for list displaying to the application object
     *
     * @param \Mezon\CommonApplication $AppObject
     *            CommonApplication object
     * @param array $Options
     *            Options
     */
    public function attachListPage(\Mezon\CommonApplication &$AppObject, array $Options)
    {
        $MethodName = $this->SafeEntityName . 'ListingPage';

        $this->loadRoute($AppObject, $this->EntityName . '/list/', $MethodName, 'GET');

        $Options = $Options === false ? [] : $Options;

        $Options['create_button'] = $this->CreateButton ? 1 : 0;
        $Options['update_button'] = $this->UpdateButton ? 1 : 0;
        $Options['delete_button'] = $this->DeleteButton ? 1 : 0;

        $Options[FIELD_NAME_DOMAIN_ID] = $this->getSelfId();

        $AppObject->$MethodName = function () use ($AppObject, $Options) {
            $ListBuilder = $this->createListBuilder($Options);

            // generate list
            $Result = [
                'main' => $ListBuilder->listingForm()
            ];

            // add page parts
            return ($this->addPageParts($Result, $AppObject, $Options));
        };
    }

    /**
     * Method adds end-point for list displaying to the application object
     *
     * @param \Mezon\CommonApplication $AppObject
     *            CommonApplication object
     * @param array $Options
     *            Options
     */
    public function attachSimpleListPage(\Mezon\CommonApplication $AppObject, array $Options)
    {
        $MethodName = $this->SafeEntityName . 'SimpleListingPage';

        $this->loadRoute($AppObject, $this->EntityName . '/list/simple/', $MethodName, 'GET');

        $Options = $Options === false ? [] : $Options;

        $Options[FIELD_NAME_DOMAIN_ID] = $this->getSelfId();

        $AppObject->$MethodName = function () use ($AppObject, $Options) {
            $ListBuilder = $this->createListBuilder($Options);

            // generate list
            $Result = [
                'main' => $ListBuilder->simpleListingForm()
            ];

            // add page parts
            return ($this->addPageParts($Result, $AppObject, $Options));
        };
    }

    /**
     * Method adds end-point for deleting record to the application object
     *
     * @param \Mezon\CommonApplication $AppObject
     *            CommonApplication object
     * @param array $Options
     *            Options
     */
    public function attachDeleteRecord(\Mezon\CommonApplication &$AppObject, array $Options)
    {
        $this->DeleteButton = true;

        $MethodName = $this->SafeEntityName . 'DeleteRecord';

        $this->loadRoute($AppObject, $this->EntityName . '/delete/[i:id]/', $MethodName, 'GET');

        $Options = $Options === false ? [] : $Options;

        $Options[FIELD_NAME_DOMAIN_ID] = $this->getSelfId();

        $AppObject->$MethodName = function (...$Params) use ($AppObject, $Options) {
            $this->CRUDServiceClient->delete($Params[1]['id'], $Options[FIELD_NAME_DOMAIN_ID]);

            $AppObject->redirectTo('../../list/');
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
    protected function getCompiledForm(string $Type = 'creation', int $id = 0): array
    {
        // get fields
        $Data = $this->CRUDServiceClient->getRemoteCreationFormFields();

        // construct $FieldsAlgorithms
        $FieldsAlgorithms = new \Mezon\GUI\FieldsAlgorithms(\Mezon\Functional::getField($Data, 'fields'), $this->EntityName);

        // create form builder object
        $FormBuilder = new \Mezon\GUI\FormBuilder($FieldsAlgorithms, false, $this->EntityName, \Mezon\Functional::getField($Data, 'layout'));

        // compile form
        if ($Type == 'creation') {
            $Result = [
                'main' => $FormBuilder->creationForm()
            ];
        } else {
            $Result = [
                'main' => $FormBuilder->updatingForm($this->CRUDServiceClient->getSessionId(), $this->CRUDServiceClient->getById($id, $this->getSelfId()))
            ];
        }

        return ($Result);
    }

    /**
     * Method gets create record controller for the remote service
     *
     * @param \Mezon\CommonApplication $AppObject
     *            CommonApplication object
     * @param array $Options
     *            Options
     */
    protected function addCreateRecordMethod(\Mezon\CommonApplication &$AppObject, array $Options)
    {
        $MethodName = $this->SafeEntityName . 'CreateRecord';

        $AppObject->$MethodName = function () use ($AppObject, $Options) {
            if (count($_POST) > 0) {
                $_POST[FIELD_NAME_DOMAIN_ID] = $this->getSelfId();

                $Data = $this->pretransformData(array_merge($_POST, $_FILES));

                $this->CRUDServiceClient->create($Data);

                $AppObject->redirectTo('../list/');
            } else {
                return ($this->addPageParts($this->getCompiledForm(), $AppObject, $Options));
            }
        };
    }

    /**
     * Method adds end-point for creating record to the application object
     *
     * @param \Mezon\CommonApplication $AppObject
     *            CommonApplication object
     * @param array $Options
     *            Options
     */
    public function attachCreateRecord(\Mezon\CommonApplication &$AppObject, array $Options)
    {
        $this->CreateButton = true;

        $Options = $Options === false ? [] : $Options;

        $Route = isset($Options['create-page-endpoint']) ? $Options['create-page-endpoint'] : $this->EntityName . '/create/';

        $this->loadRoute($AppObject, $Route, $this->SafeEntityName . 'CreateRecord', [
            'POST',
            'GET'
        ]);

        $this->addCreateRecordMethod($AppObject, $Options);
    }

    /**
     * Method gets update record controller for the remote service.
     *
     * @param \Mezon\CommonApplication $AppObject
     *            CommonApplication object
     * @param array $Options
     *            Options
     */
    protected function addUpdateRecordMethod(\Mezon\CommonApplication &$AppObject, array $Options)
    {
        $MethodName = $this->SafeEntityName . 'UpdateRecord';

        $AppObject->$MethodName = function (...$Params) use ($AppObject, $Options) {
            if (count($_POST) > 0) {
                $_POST[FIELD_NAME_DOMAIN_ID] = $this->getSelfId();

                $this->postRequest('/update/' . $Params[1]['id'] . '/', $_POST);

                $this->CRUDServiceClient->update($Params[1]['id'], $_POST, $_POST[FIELD_NAME_DOMAIN_ID]);

                $AppObject->redirectTo('../../list/');
            } else {
                return ($this->addPageParts($this->getCompiledForm('updating', $Params[1]['id']), $AppObject, $Options));
            }
        };
    }

    /**
     * Method adds end-point for updating record to the application object
     *
     * @param \Mezon\CommonApplication $AppObject
     *            CommonApplication object
     * @param array $Options
     *            Options
     */
    public function attachUpdateRecord(\Mezon\CommonApplication &$AppObject, array $Options)
    {
        $this->UpdateButton = true;

        $Options = $Options === false ? [] : $Options;

        $Route = isset($Options['update-record-endpoint']) ? $Options['update-record-endpoint'] : $this->EntityName . '/update/[i:id]/';

        $this->loadRoute($AppObject, $Route, $this->SafeEntityName . 'UpdateRecord', [
            'POST',
            'GET'
        ]);

        $this->addUpdateRecordMethod($AppObject, $Options);
    }
}

?>