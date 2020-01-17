<?php
namespace Mezon\CrudService;

/**
 * Class CrudServiceCollection
 *
 * @package CrudService
 * @subpackage CrudServiceCollection
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/13)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Collection of the crud service's records
 *
 * @author Dodonov A.A.
 */
class CrudServiceCollection
{

    /**
     * Collection of records
     *
     * @var array
     */
    protected $сollection = [];

    /**
     * Connection to the Crud service
     *
     * @var \Mezon\CrudService\CrudServiceClient
     */
    protected $сonnector;

    /**
     * Constructor
     *
     * @param string $service
     * @param string $token
     */
    public function __construct(string $service = '', string $token = '')
    {
        if ($service !== '') {
            $this->сonnector = $this->constructClient($service, $token);
        }
    }

    /**
     * Method constructs connector
     *
     * @param string $service
     *            Service title
     * @param string $token
     *            Acccess token
     * @return \Mezon\CrudService\CrudServiceClient Connector to the service
     */
    protected function constructClient(string $service, string $token): \Mezon\CrudService\CrudServiceClient
    {
        // TODO pass \Mezon\CrudService\CrudServiceClient object instead of $service
        $client = new \Mezon\CrudService\CrudServiceClient($service);

        $client->setToken($token);

        return $client;
    }

    /**
     * Method sets new connector
     *
     * @param \Mezon\CrudService\CrudServiceClient $newConnector
     *            New connector
     */
    public function setConnector($newConnector): void
    {
        $this->сonnector = $newConnector;
    }

    /**
     * Method returns connector to service
     *
     * @return \Mezon\CrudService\CrudServiceClient
     */
    public function getConnector(): \Mezon\CrudService\CrudServiceClient
    {
        return $this->сonnector;
    }

    /**
     * Method fetches scripts, wich were created since $dateTime
     *
     * @param string $dateTime
     */
    public function newRecordsSince(string $dateTime): void
    {
        $this->сollection = $this->сonnector->newRecordsSince($dateTime);
    }

    /**
     * Fetching top $count records sorted by field
     *
     * @param int $count
     *            Count of records to be fetched
     * @param string $field
     *            Sorting field
     * @param string $order
     *            Sorting order
     */
    public function topByField(int $count, string $field, string $order = 'DESC'): void
    {
        $this->сollection = $this->сonnector->getList(0, $count, 0, false, [
            'field' => $field,
            'order' => $order
        ]);
    }

    /**
     * Method returns previosly fetched collection
     *
     * @return array previosly fetched collection
     */
    public function getCollection(): array
    {
        return $this->сollection;
    }
}
