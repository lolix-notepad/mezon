<?php
namespace Mezon\CRUDService;

/**
 * Class CRUDServiceCollection
 *
 * @package CRUDService
 * @subpackage CRUDServiceCollection
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/13)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Collection of the crud service's records
 *
 * @author Dodonov A.A.
 */
class CRUDServiceCollection
{

    /**
     * Collection of records
     *
     * @var array
     */
    var $Collection = [];

    /**
     * Connection to the CRUD service
     *
     * @var CRUDServiceClient
     */
    var $Connector;

    /**
     * Constructor
     *
     * @param string $Service
     * @param string $Token
     */
    public function __construct(string $Service = '', string $Token = '')
    {
        if ($Service !== '') {
            $this->Connector = $this->constructClient($Service, $Token);
        }
    }

    /**
     * Method constructs connector
     *
     * @param string $Service
     *            Service title
     * @param string $Token
     *            Acccess token
     * @return CRUDServiceClient Connector to the service
     */
    protected function constructClient(string $Service, string $Token)
    {
        $Client = new CRUDServiceClient($Service);

        $Client->setToken($Token);

        return ($Client);
    }

    /**
     * Method sets new connector
     *
     * @param CRUDServiceClient $NewConnector
     *            New connector
     */
    public function setConnector($NewConnector)
    {
        $this->Connector = $NewConnector;
    }

    /**
     * Method fetches scripts, wich were created since $DateTime
     *
     * @param string $DateTime
     */
    public function newRecordsSince(string $DateTime)
    {
        $this->Collection = $this->Connector->newRecordsSince($DateTime);
    }

    /**
     * Fetching top $Count records sorted by field
     *
     * @param int $Count
     *            Count of records to be fetched
     * @param string $Field
     *            Sorting field
     * @param string $Order
     *            Sorting order
     */
    public function topByField(int $Count, string $Field, string $Order = 'DESC')
    {
        $this->Collection = $this->Connector->getList(0, $Count, 0, false, [
            'field' => $Field,
            'order' => $Order
        ]);
    }
}

?>