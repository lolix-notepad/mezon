<?php
namespace Mezon\CRUDService;

/**
 * Class CRUDServiceClient
 *
 * @package CRUDService
 * @subpackage CRUDServiceClient
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/13)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Class for basic CRUD API client.
 *
 * @author Dodonov A.A.
 */
class CRUDServiceClient extends \Mezon\Service\ServiceClient
{

    /**
     * Method returns compiled filter string
     *
     * @param array $Filter
     *            Filter
     * @param boolean $Amp
     *            Do we need &
     * @return string Compiled filter
     */
    public function getCompiledFilter($Filter, $Amp = true)
    {
        if ($Filter !== false) {
            if (isset($Filter[0]) && is_array($Filter[0])) {
                return (($Amp ? '&' : '') . http_build_query([
                    'filter' => $Filter
                ]));
            } else {
                $FilterString = [];

                foreach ($Filter as $Name => $Value) {
                    $FilterString[] = 'filter[' . $Name . ']=' . urlencode($Value);
                }

                return (($Amp ? '&' : '') . implode('&', $FilterString));
            }
        }

        return ('');
    }

    /**
     * Method compiles sorting settings
     *
     * @param array $Order
     *            Sorting settings. For example [ 'field' => 'id' , 'order' => 'ASC' ]
     * @return string Compiled sorting settings
     */
    protected function getCompiledOrder($Order)
    {
        if ($Order !== false) {
            return ('&' . http_build_query([
                'order' => $Order
            ]));
        }

        return ('');
    }

    /**
     * Method returns all records by filter
     *
     * @param array $Filter
     *            Filtering settings
     * @param integer $CrossDomain
     *            Cross domain security settings
     * @return array List of records
     * @codeCoverageIgnore
     */
    public function getRecordsBy($Filter, $CrossDomain = 0)
    {
        $Filter = $this->getCompiledFilter($Filter);

        return ($this->getRequest('/list/?cross_domain=' . $CrossDomain . $Filter));
    }

    /**
     * Method returns record by it's id
     *
     * @param integer $id
     *            Id of the fetching record
     * @param number $CrossDomain
     *            Domain id
     * @return object fetched record
     * @codeCoverageIgnore
     */
    public function getById($id, $CrossDomain = 0)
    {
        return ($this->getRequest("/exact/$id/?cross_domain=$CrossDomain"));
    }

    /**
     * Method returns records by their ids
     *
     * @param array $ids
     *            List of ids
     * @param number $CrossDomain
     *            Domain id
     * @return array Fetched records
     */
    public function getByIdsArray($ids, $CrossDomain = 0)
    {
        $Cache = \Mezon\Cache::getInstance();

        $Key = $this->Service . '/get_by_ids_array/' . ($CrossDomain ? 1 : 0) . implode('.', $ids);

        if ($Cache->exists($Key)) {
            return ($Cache->get($Key));
        }

        if (count($ids) === 0) {
            return ([]);
        }

        $Result = $this->getRequest('/exact/list/' . implode(',', $ids) . "/?cross_domain=$CrossDomain");

        $Cache->set($Key, $Result);

        return ($Result);
    }

    /**
     * Method creates new record
     *
     * @param array $Data
     *            data for creating record
     * @return integer id of the created record
     */
    public function create($Data)
    {
        $Data = $this->pretransformData($Data);

        return ($this->postRequest('/create/', $Data));
    }

    /**
     * Method updates new record
     *
     * @param integer $id
     *            Id of the updating record
     * @param array $Data
     *            Data to be posted
     * @param integer $CrossDomain
     *            Cross domain policy
     * @return mixed Result of the RPC call
     * @codeCoverageIgnore
     */
    public function update(int $id, array $Data, int $CrossDomain = 0)
    {
        return ($this->postRequest('/update/' . $id . '/?cross_domain=' . $CrossDomain, $Data));
    }

    /**
     * Method returns creation form's fields in JSON format
     *
     * @codeCoverageIgnore
     */
    public function fields()
    {
        $Result = $this->getRequest('/fields/');

        $Result = json_encode($Result);

        $Result->fields = json_encode($Result->fields);
        $Result->layout = json_encode($Result->layout);

        return ($Result);
    }

    /**
     * Method returns all records created since $Date
     *
     * @param \datetime $Date
     *            Start of the period
     * @return array List of records created since $Date
     * @codeCoverageIgnore
     */
    public function newRecordsSince($Date)
    {
        return ($this->getRequest('/new/from/' . $Date . '/'));
    }

    /**
     * Method returns count of records
     *
     * @return array List of records created since $Date
     * @codeCoverageIgnore
     */
    public function recordsCount()
    {
        return ($this->getRequest('/records/count/'));
    }

    /**
     * Method returns last $Count records
     *
     * @param integer $Count
     *            Amount of records to be fetched
     * @param array $Filter
     *            Filter data
     * @return array $Count of last created records
     * @codeCoverageIgnore
     */
    public function lastRecords($Count, $Filter)
    {
        $Filter = $this->getCompiledFilter($Filter, false);

        return ($this->getRequest('/last/' . $Count . '/?' . $Filter));
    }

    /**
     * Method deletes record with $id
     *
     * @param integer $id
     *            Id of the deleting record
     * @param int $CrossDomain
     *            Break domain's bounds or not
     * @return string Result of the deletion
     * @codeCoverageIgnore
     */
    public function delete(int $id, int $CrossDomain = 0): string
    {
        return ($this->getRequest('/delete/' . $id . '/?cross_domain=' . $CrossDomain));
    }

    /**
     * Method returns count off records
     *
     * @param string $Field
     *            Field for grouping
     * @param array $Filter
     *            Filtering settings
     * @return array List of records created since $Date
     */
    public function recordsCountByField(string $Field, $Filter = false): array
    {
        $Cache = \Mezon\Cache::getInstance();

        $Filter = $this->getCompiledFilter($Filter);

        $Key = $this->Service . '/records-count-by-field/' . $Field . '/' . $Filter;

        if ($Cache->exists($Key)) {
            return ($Cache->get($Key));
        }

        $Return = $this->getRequest('/records/count/' . $Field . '/?' . $Filter);

        $Cache->set($Key, $Return);

        return ($Return);
    }

    /**
     * Method deletes records by filter
     *
     * @param integer $CrossDomain
     *            Cross domain security settings
     * @param array $Filter
     *            Filtering settings
     * @codeCoverageIgnore
     */
    public function deleteFiltered($CrossDomain = 0, $Filter = false)
    {
        $Filter = $this->getCompiledFilter($Filter);

        $this->postRequest('/delete/?cross_domain=' . $CrossDomain . $Filter, []);
    }

    /**
     * Method creates instance if the CRUDServiceClient class
     *
     * @param string $Service
     *            Service to be connected to
     * @param string $Token
     *            Connection token
     * @return CRUDServiceClient Instance of the CRUDServiceClient class
     */
    public static function instance(string $Service, string $Token): CRUDServiceClient
    {
        $Connection = new CRUDServiceClient($Service);

        $Connection->setToken($Token);

        return ($Connection);
    }

    /**
     * Method returns some records of the user's domain
     *
     * @param integer $From
     *            The beginnig of the fetching sequence
     * @param integer $Limit
     *            Size of the fetching sequence
     * @param integer $CrossDomain
     *            Cross domain security settings
     * @param array $Filter
     *            Filtering settings
     * @param array $Order
     *            Sorting settings
     * @return array List of records
     */
    public function getList(int $From = 0, int $Limit = 1000000000, $CrossDomain = 0, $Filter = false, $Order = false): array
    {
        $Filter = $this->getCompiledFilter($Filter);

        $Order = $this->getCompiledOrder($Order);

        return ($this->getRequest('/list/?from=' . $From . '&limit=' . $Limit . '&cross_domain=' . $CrossDomain . $Filter . $Order));
    }

    /**
     * Method compiles file field
     *
     * @param string $Path
     *            Path to file
     * @param string $Name
     *            Field name
     * @return array Field data
     */
    protected function createFileField(string $Path, string $Name): array
    {
        return ([
            'file' => base64_encode(file_get_contents($Path)),
            'name' => basename($Name)
        ]);
    }

    /**
     * Checking if we are uploading a file
     *
     * @param mixed $Value
     *            Uploading data
     * @return boolean True if the $Value is the uploading file. False otherwise
     */
    protected function isFile($Value)
    {
        if ((is_array($Value) || is_object($Value)) === false) {
            // it is not a file, it is a scalar
            return (false);
        }

        if (\Mezon\Functional::getField($Value, 'name') !== null && \Mezon\Functional::getField($Value, 'size') !== null && \Mezon\Functional::getField($Value, 'type') !== null && \Mezon\Functional::getField($Value, 'tmp_name') !== null) {
            return (true);
        }

        return (false);
    }

    /**
     * Transforming data before sending to service
     *
     * @param string $Data
     *            Data to be transformed
     * @return string Transformed data
     */
    protected function pretransformData($Data)
    {
        foreach ($Data as $Key => $Value) {
            if ($this->isFile($Value)) {
                $TmpName = $Value['tmp_name'];
                // looks like we have to upload file
                if (is_array($Value['name'])) {
                    $Data[$Key] = array();

                    // even several files!
                    foreach (array_keys($Value['name']) as $i) {
                        $Data[$Key][] = $this->createFileField($TmpName[$i], $Value['name'][$i]);
                    }
                } else {
                    // only single file
                    $Data[$Key] = $this->createFileField($TmpName, $Value['name']);
                }
            }
        }

        return ($Data);
    }

    /**
     * Method returns fields and layout
     *
     * @return array Fields and layout
     */
    public function getFields(): array
    {
        return ($this->getRequest('/fields/'));
    }
}

?>