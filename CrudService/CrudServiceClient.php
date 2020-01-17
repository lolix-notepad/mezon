<?php
namespace Mezon\CrudService;

/**
 * Class CrudServiceClient
 *
 * @package CrudService
 * @subpackage CrudServiceClient
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/13)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Class for basic Crud API client.
 *
 * @author Dodonov A.A.
 */
class CrudServiceClient extends \Mezon\Service\ServiceClient implements \Mezon\CrudService\CrudServiceClientInterface
{

    /**
     * Method returns compiled filter string
     *
     * @param array $filter
     *            Filter
     * @param bool $amp
     *            Do we need &
     * @return string Compiled filter
     */
    protected function getCompiledFilter($filter, $amp = true): string
    {
        if ($filter !== false) {
            if (isset($filter[0]) && is_array($filter[0])) {
                return ($amp ? '&' : '') . http_build_query([
                    'filter' => $filter
                ]);
            } else {
                $filterString = [];

                foreach ($filter as $name => $value) {
                    $filterString[] = 'filter[' . $name . ']=' . urlencode($value);
                }

                return ($amp ? '&' : '') . implode('&', $filterString);
            }
        }

        return '';
    }

    /**
     * Method compiles sorting settings
     *
     * @param array $order
     *            Sorting settings. For example [ 'field' => 'id' , 'order' => 'ASC' ]
     * @return string Compiled sorting settings
     */
    protected function getCompiledOrder($order)
    {
        if ($order !== false) {
            return '&' . http_build_query([
                'order' => $order
            ]);
        }

        return '';
    }

    /**
     * Method returns all records by filter
     *
     * @param array $filter
     *            Filtering settings
     * @param int $crossDomain
     *            Cross domain security settings
     * @return array List of records
     * @codeCoverageIgnore
     */
    public function getRecordsBy($filter, $crossDomain = 0)
    {
        $filter = $this->getCompiledFilter($filter);

        return $this->getRequest('/list/?cross_domain=' . $crossDomain . $filter);
    }

    /**
     * Method returns record by it's id
     *
     * @param int $id
     *            Id of the fetching record
     * @param number $crossDomain
     *            Domain id
     * @return object fetched record
     * @codeCoverageIgnore
     */
    public function getById($id, $crossDomain = 0)
    {
        return $this->getRequest("/exact/$id/?cross_domain=$crossDomain");
    }

    /**
     * Method returns records by their ids
     *
     * @param array $ids
     *            List of ids
     * @param number $crossDomain
     *            Domain id
     * @return array Fetched records
     */
    public function getByIdsArray($ids, $crossDomain = 0)
    {
        if (count($ids) === 0) {
            return [];
        }

        return $this->getRequest('/exact/list/' . implode(',', $ids) . "/?cross_domain=$crossDomain");
    }

    /**
     * Method creates new record
     *
     * @param array $data
     *            data for creating record
     * @return int id of the created record
     */
    public function create($data)
    {
        $data = $this->pretransformData($data);

        return $this->postRequest('/create/', $data);
    }

    /**
     * Method updates new record
     *
     * @param int $id
     *            Id of the updating record
     * @param array $data
     *            Data to be posted
     * @param int $crossDomain
     *            Cross domain policy
     * @return mixed Result of the RPC call
     * @codeCoverageIgnore
     */
    public function update(int $id, array $data, int $crossDomain = 0)
    {
        return $this->postRequest('/update/' . $id . '/?cross_domain=' . $crossDomain, $data);
    }

    /**
     * Method returns creation form's fields in JSON format
     *
     * @codeCoverageIgnore
     */
    public function fields()
    {
        $result = $this->getRequest('/fields/');

        $result = json_encode($result);

        $result->fields = json_encode($result->fields);
        $result->layout = json_encode($result->layout);

        return $result;
    }

    /**
     * Method returns all records created since $date
     *
     * @param \datetime $date
     *            Start of the period
     * @return array List of records created since $date
     * @codeCoverageIgnore
     */
    public function newRecordsSince($date)
    {
        return $this->getRequest('/new/from/' . $date . '/');
    }

    /**
     * Method returns count of records
     *
     * @return array List of records created since $date
     * @codeCoverageIgnore
     */
    public function recordsCount()
    {
        return $this->getRequest('/records/count/');
    }

    /**
     * Method returns last $count records
     *
     * @param int $count
     *            Amount of records to be fetched
     * @param array $filter
     *            Filter data
     * @return array $count of last created records
     * @codeCoverageIgnore
     */
    public function lastRecords($count, $filter)
    {
        $filter = $this->getCompiledFilter($filter, false);

        return $this->getRequest('/last/' . $count . '/?' . $filter);
    }

    /**
     * Method deletes record with $id
     *
     * @param int $id
     *            Id of the deleting record
     * @param int $crossDomain
     *            Break domain's bounds or not
     * @return string Result of the deletion
     * @codeCoverageIgnore
     */
    public function delete(int $id, int $crossDomain = 0): string
    {
        return $this->getRequest('/delete/' . $id . '/?cross_domain=' . $crossDomain);
    }

    /**
     * Method returns count off records
     *
     * @param string $field
     *            Field for grouping
     * @param array $filter
     *            Filtering settings
     * @return array List of records created since $date
     */
    public function recordsCountByField(string $field, $filter = false): array
    {
        $filter = $this->getCompiledFilter($filter);

        return $this->getRequest('/records/count/' . $field . '/?' . $filter);
    }

    /**
     * Method deletes records by filter
     *
     * @param int $crossDomain
     *            Cross domain security settings
     * @param array $filter
     *            Filtering settings
     * @codeCoverageIgnore
     */
    public function deleteFiltered($crossDomain = 0, $filter = false)
    {
        $filter = $this->getCompiledFilter($filter);

        $this->postRequest('/delete/?cross_domain=' . $crossDomain . $filter, []);
    }

    /**
     * Method creates instance if the CrudServiceClient class
     *
     * @param string $service
     *            Service to be connected to
     * @param string $token
     *            Connection token
     * @return CrudServiceClient Instance of the CrudServiceClient class
     */
    public static function instance(string $service, string $token): \Mezon\CrudService\CrudServiceClient
    {
        $connection = new CrudServiceClient($service);

        $connection->setToken($token);

        return $connection;
    }

    /**
     * Method returns some records of the user's domain
     *
     * @param int $from
     *            The beginnig of the fetching sequence
     * @param int $limit
     *            Size of the fetching sequence
     * @param int $crossDomain
     *            Cross domain security settings
     * @param array $filter
     *            Filtering settings
     * @param array $order
     *            Sorting settings
     * @return array List of records
     */
    public function getList(int $from = 0, int $limit = 1000000000, $crossDomain = 0, $filter = false, $order = false): array
    {
        $filter = $this->getCompiledFilter($filter);

        $order = $this->getCompiledOrder($order);

        return $this->getRequest(
            '/list/?from=' . $from . '&limit=' . $limit . '&cross_domain=' . $crossDomain . $filter . $order);
    }

    /**
     * Method compiles file field
     *
     * @param string $path
     *            Path to file
     * @param string $name
     *            Field name
     * @return array Field data
     */
    protected function createFileField(string $path, string $name): array
    {
        return [
            'file' => base64_encode(file_get_contents($path)),
            'name' => basename($name)
        ];
    }

    /**
     * Checking if we are uploading a file
     *
     * @param mixed $value
     *            Uploading data
     * @return bool True if the $value is the uploading file. False otherwise
     */
    protected function isFile($value): bool
    {
        if ((is_array($value) || is_object($value)) === false) {
            // it is not a file, it is a scalar
            return false;
        }

        if (\Mezon\Functional\Functional::getField($value, 'name') !== null &&
            \Mezon\Functional\Functional::getField($value, 'size') !== null &&
            \Mezon\Functional\Functional::getField($value, 'type') !== null &&
            \Mezon\Functional\Functional::getField($value, 'tmp_name') !== null) {
            return true;
        }

        return false;
    }

    /**
     * Transforming data before sending to service
     *
     * @param array $data
     *            Data to be transformed
     * @return string Transformed data
     */
    protected function pretransformData(array $data): array
    {
        foreach ($data as $key => $value) {
            if ($this->isFile($value)) {
                $tmpName = $value['tmp_name'];
                // looks like we have to upload file
                if (is_array($value['name'])) {
                    $data[$key] = array();

                    // even several files!
                    foreach (array_keys($value['name']) as $i) {
                        $data[$key][] = $this->createFileField($tmpName[$i], $value['name'][$i]);
                    }
                } else {
                    // only single file
                    $data[$key] = $this->createFileField($tmpName, $value['name']);
                }
            }
        }

        return $data;
    }

    /**
     * Method returns fields and layout
     *
     * @return array Fields and layout
     */
    public function getFields(): array
    {
        return $this->getRequest('/fields/');
    }
}
