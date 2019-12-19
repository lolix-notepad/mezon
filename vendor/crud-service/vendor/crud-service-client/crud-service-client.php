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
require_once (__DIR__ . '/../../../cache/cache.php');
require_once (__DIR__ . '/../../../service/vendor/service-client/service-client.php');

// TODO add camel-case
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
    public function get_compiled_filter($Filter, $Amp = true)
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
    protected function get_compiled_order($Order)
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
    public function get_records_by($Filter, $CrossDomain = 0)
    {
        $Filter = $this->get_compiled_filter($Filter);

        return ($this->get_request('/list/?cross_domain=' . $CrossDomain . $Filter));
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
    public function get_by_id($id, $CrossDomain = 0)
    {
        return ($this->get_request("/exact/$id/?cross_domain=$CrossDomain"));
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
    public function get_by_ids_array($ids, $CrossDomain = 0)
    {
        $Cache = \Mezon\Cache::get_instance();

        $Key = $this->Service . '/get_by_ids_array/' . ($CrossDomain ? 1 : 0) . implode('.', $ids);

        if ($Cache->exists($Key)) {
            return ($Cache->get($Key));
        }

        if (count($ids) === 0) {
            return ([]);
        }

        $Result = $this->get_request('/exact/list/' . implode(',', $ids) . "/?cross_domain=$CrossDomain");

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
        $Data = $this->pretransform_data($Data);

        return ($this->post_request('/create/', $Data));
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
        return ($this->post_request('/update/' . $id . '/?cross_domain=' . $CrossDomain, $Data));
    }

    /**
     * Method returns creation form's fields in JSON format
     *
     * @codeCoverageIgnore
     */
    public function fields()
    {
        $Result = $this->get_request('/fields/');

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
    public function new_records_since($Date)
    {
        return ($this->get_request('/new/from/' . $Date . '/'));
    }

    /**
     * Method returns count of records
     *
     * @return array List of records created since $Date
     * @codeCoverageIgnore
     */
    public function records_count()
    {
        return ($this->get_request('/records/count/'));
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
    public function last_records($Count, $Filter)
    {
        $Filter = $this->get_compiled_filter($Filter, false);

        return ($this->get_request('/last/' . $Count . '/?' . $Filter));
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
        return ($this->get_request('/delete/' . $id . '/?cross_domain=' . $CrossDomain));
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
    public function records_count_by_field(string $Field, $Filter = false): array
    {
        $Cache = \Mezon\Cache::get_instance();

        $Filter = $this->get_compiled_filter($Filter);

        $Key = $this->Service . '/records_count_by_field/' . $Field . '/' . $Filter;

        if ($Cache->exists($Key)) {
            return ($Cache->get($Key));
        }

        $Return = $this->get_request('/records/count/' . $Field . '/?' . $Filter);

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
    public function delete_filtered($CrossDomain = 0, $Filter = false)
    {
        $Filter = $this->get_compiled_filter($Filter);

        $this->post_request('/delete/?cross_domain=' . $CrossDomain . $Filter, []);
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

        $Connection->set_token($Token);

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
    public function get_list(int $From = 0, int $Limit = 1000000000, $CrossDomain = 0, $Filter = false, $Order = false): array
    {
        $Filter = $this->get_compiled_filter($Filter);

        $Order = $this->get_compiled_order($Order);

        return ($this->get_request('/list/?from=' . $From . '&limit=' . $Limit . '&cross_domain=' . $CrossDomain . $Filter . $Order));
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
    protected function create_file_field(string $Path, string $Name): array
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
    protected function is_file($Value)
    {
        if ((is_array($Value) || is_object($Value)) === false) {
            // it is not a file, it is a scalar
            return (false);
        }

        if (\Mezon\Functional::get_field($Value, 'name') !== null && \Mezon\Functional::get_field($Value, 'size') !== null && \Mezon\Functional::get_field($Value, 'type') !== null && \Mezon\Functional::get_field($Value, 'tmp_name') !== null) {
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
    protected function pretransform_data($Data)
    {
        foreach ($Data as $Key => $Value) {
            if ($this->is_file($Value)) {
                $TmpName = $Value['tmp_name'];
                // looks like we have to upload file
                if (is_array($Value['name'])) {
                    $Data[$Key] = array();

                    // even several files!
                    foreach (array_keys($Value['name']) as $i) {
                        $Data[$Key][] = $this->create_file_field($TmpName[$i], $Value['name'][$i]);
                    }
                } else {
                    // only single file
                    $Data[$Key] = $this->create_file_field($TmpName, $Value['name']);
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
    public function get_fields(): array
    {
        return ($this->get_request('/fields/'));
    }
}

?>