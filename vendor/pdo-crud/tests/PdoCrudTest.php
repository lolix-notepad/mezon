<?php
require_once (__DIR__ . '/../pdo-crud.php');

class ResultMock
{

    public function rowCount(): int
    {
        return (0);
    }
}

class ProCrudTest extends PHPUnit\Framework\TestCase
{

    /**
     * Method returns not setup mock
     *
     * @return object PdoCrud not setup mock
     */
    protected function get_unsetup_pdo_mock(): object
    {
        $Mock = $this->getMockBuilder('\Mezon\PdoCrud')
            ->setMethods([
            'query',
            'process_query_error',
            'last_insert_id'
        ])
            ->setConstructorArgs([])
            ->getMock();

        return ($Mock);
    }

    /**
     * Method returns mock
     *
     * @return object PdoCrud mock
     */
    protected function get_pdo_mock(): object
    {
        $Mock = $this->get_unsetup_pdo_mock();

        $Mock->expects($this->once())
            ->method('query');

        $Mock->expects($this->once())
            ->method('process_query_error');

        return ($Mock);
    }

    /**
     * Testing multiple insertion method
     */
    public function test_insert_multyple(): void
    {
        $Mock = $this->get_pdo_mock();

        $Mock->insert_multyple('records', [
            [
                'id' => 1
            ],
            [
                'id' => 2
            ]
        ]);
    }

    /**
     * Testing insertion method
     */
    public function test_insert(): void
    {
        // setup
        $Mock = $this->get_pdo_mock();

        $Mock->expects($this->once())
            ->method('last_insert_id')
            ->willReturn(1);

        // test body and assertions
        $Mock->insert('records', [
            'id' => 1
        ]);
    }

    /**
     * Testing rollback method
     */
    public function test_rollback(): void
    {
        // setup
        $Mock = $this->get_pdo_mock();

        $Mock->expects($this->once())
            ->method('query')
            ->willReturn(true);

        // test body and assertions
        $Mock->rollback();
    }

    /**
     * Testing commit method
     */
    public function test_commit(): void
    {
        // setup
        $Mock = $this->get_unsetup_pdo_mock();

        $Mock->expects($this->exactly(2))
            ->method('query')
            ->willReturn(true);

        // test body and assertions
        $Mock->commit();
    }

    /**
     * Testing start_transaction method
     */
    public function test_start_transaction(): void
    {
        // setup
        $Mock = $this->get_unsetup_pdo_mock();

        $Mock->expects($this->exactly(2))
            ->method('query')
            ->willReturn(true);

        // test body and assertions
        $Mock->start_transaction();
    }

    /**
     * Testing unlock method
     */
    public function test_unlock(): void
    {
        // setup
        $Mock = $this->get_pdo_mock();

        // test body and assertions
        $Mock->unlock();
    }

    /**
     * Testing lock method
     */
    public function test_lock(): void
    {
        // setup
        $Mock = $this->get_pdo_mock();

        // test body and assertions
        $Mock->lock([
            'records'
        ], [
            'WRITE'
        ]);
    }

    /**
     * Testing delete method
     */
    public function test_delete(): void
    {
        // setup
        $Mock = $this->get_unsetup_pdo_mock();

        $Mock->expects($this->exactly(1))
            ->method('query')
            ->willReturn(new ResultMock());

        // test body and assertions
        $Mock->delete('records', 'id=1');
    }

    /**
     * Testing update method
     */
    public function test_update(): void
    {
        // setup
        $Mock = $this->get_unsetup_pdo_mock();
        $Mock->expects($this->exactly(1))
            ->method('query')
            ->willReturn(new ResultMock());

        // test body and assertions
        $Mock->update('som-record', [], '1=1');
    }
}

?>