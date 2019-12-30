<?php
namespace Mezon\Gui\ListBuilder;

/**
 * Class CrudServiceClientAdapter
 *
 * @package ListBuilder
 * @subpackage CrudServiceClientAdapter
 * @author Dodonov A.A.
 * @version v.1.0 (2019/09/11)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Logic adapter for list builder
 */
class CrudServiceClientAdapter implements ListBuilderAdapter
{

    /**
     * Crud Service Client object
     *
     * @var \Mezon\CrudService\CrudServiceClient
     */
    var $CrudServiceClient = null;

    /**
     * Service name
     *
     * @var string
     */
    var $Service = '';

    /**
     * Login
     *
     * @var string
     */
    var $Login = '';

    /**
     * Password
     *
     * @var string
     */
    var $Password;

    /**
     * Constructor
     *
     * @param string $Service
     *            Service name
     * @param string $Login
     *            Login
     * @param string $Password
     *            Password
     */
    public function __construct(string $Service = '', string $Login = '', string $Password = '')
    {
        $this->Service = $Service;

        $this->Login = $Login;

        $this->Password = $Password;
    }

    /**
     * Method returns client to service
     *
     * @return \Mezon\CrudService\CrudServiceClient Client
     */
    protected function getClient(): \Mezon\CrudService\CrudServiceClient
    {
        if ($this->CrudServiceClient === null) {
            $this->CrudServiceClient = new \Mezon\CrudService\CrudServiceClient($this->Service, $this->Login, $this->Password);
        }

        return ($this->CrudServiceClient);
    }

    /**
     * Method sets service client
     *
     * @param \Mezon\CrudService\CrudServiceClient $CrudServiceClient
     *            Service client
     */
    public function setClient(\Mezon\CrudService\CrudServiceClient $CrudServiceClient)
    {
        $this->CrudServiceClient = $CrudServiceClient;
    }

    /**
     * Method returns all vailable records
     *
     * @return array all vailable records
     */
    public function all(): array
    {
        return ($this->getClient()->getList(0, 1000000));
    }

    /**
     * Method returns a subset from vailable records
     *
     * @param array $Order
     *            order settings
     * @param int $From
     *            the beginning of the bunch
     * @param int $Limit
     *            the size of the batch
     * @return array subset from vailable records
     */
    public function getRecords(array $Order, int $From, int $Limit): array
    {
        return ($this->getClient()->getList($From, $Limit, 0, [], $Order));
    }

    /**
     * Record preprocessor
     *
     * @param array $Record
     *            record to be preprocessed
     * @return array preprocessed record
     */
    public function preprocessListItem(array $Record): array
    {
        // in this case all transformations are done on the service's side
        return ($Record);
    }
}

?>