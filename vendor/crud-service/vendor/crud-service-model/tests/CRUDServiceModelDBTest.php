<?php
require_once (__DIR__ . '/../../../../conf/conf.php');
require_once (__DIR__ . '/../../../../functional/functional.php');
require_once (__DIR__ . '/../../../../pdo-crud/pdo-crud.php');

require_once (__DIR__ . '/../crud-service-model.php');

class CRUDServiceModelDBTest extends PHPUnit\Framework\TestCase
{

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        add_connection_to_config('default-db-connection', 'mysql:host=localhost;dbname=record', 'root', '');
    }

    /**
     * Method returns model's mock.
     *
     * @param object $Connection
     *            - Connection to the database.
     * @return object Mock of the model.
     */
    protected function get_model_mock($Connection)
    {
        $Mock = $this->getMockBuilder('CRUDServiceModel')
            ->setMethods(array(
            'get_connection'
        ))
            ->getMock();

        $Mock->method('get_connection')->willReturn($Connection);

        $Mock->TableName = 'records';

        return ($Mock);
    }

    /**
     * Method returns connection to the database.
     *
     * @return Connection.
     */
    protected function get_connection()
    {
        $Connection = new PdoCrud();
        $Connection->connect([
            "dsn" => "mysql:host=localhost;dbname=record",
            "user" => "root",
            "password" => ""
        ]);

        return ($Connection);
    }

    /**
     * Method tests last N records returning.
     */
    public function test_last_records_all()
    {
        $Mock = $this->get_model_mock($this->get_connection());

        $this->assertEquals(count($Mock->last_records(2, [
            '1 = 1'
        ])), 2, 'Invalid amount of records was returned (all)');
    }

    /**
     * Method tests last N records returning.
     */
    public function test_last_records_limited()
    {
        $Mock = $this->get_model_mock($this->get_connection());

        $this->assertEquals(count($Mock->last_records(1, [
            '1 = 1'
        ])), 1, 'Invalid amount of records was returned (limited)');
    }

    /**
     * Method tests last N records returning.
     */
    public function test_last_records_query()
    {
        $Mock = $this->get_model_mock($this->get_connection());

        $Result = $Mock->last_records(2, [
            'id > 1'
        ]);

        $this->assertEquals('field', $Result[0]['name'], 'Invalid amount of records was returned');
    }

    /**
     * Method tests record insertion.
     */
    public function test_insert_basic_fields()
    {
        $Mock = $this->get_model_mock($this->get_connection());

        $Record = $Mock->insert_basic_fields([
            'name' => 'new name'
        ]);

        $this->assertTrue(isset($Record['id']), 'Record was not created');
    }

    /**
     * Method tests record deletion.
     */
    public function test_delete_filtered()
    {
        $Mock = $this->get_model_mock($this->get_connection());

        $Mock->delete_filtered(false, [
            'name LIKE "new name"'
        ]);

        $this->addToAssertionCount(1);
    }

    /**
     * Testing records fetching.
     */
    public function test_get_simple_records()
    {
        $Model = new CRUDServiceModel('id', 'records');
        $Records = $Model->get_simple_records(false, 0, 1, [], [
            'field' => 'id',
            'order' => 'asc'
        ]);
        $this->assertEquals(1, count($Records), 'get_simple_records have returned nothing');
    }
}

?>