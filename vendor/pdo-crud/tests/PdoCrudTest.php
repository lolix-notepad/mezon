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
    protected function getUnsetupPdoMock(): object
    {
        $Mock = $this->getMockBuilder('\Mezon\PdoCrud')
            ->setMethods([
            'query',
            'processQueryError',
            'lastInsertId'
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
    protected function getPdoMock(): object
    {
        $Mock = $this->getUnsetupPdoMock();

        $Mock->expects($this->once())
            ->method('query');

        $Mock->expects($this->once())
            ->method('processQueryError');

        return ($Mock);
    }

    /**
     * Testing multiple insertion method
     */
    public function testInsertMultyple(): void
    {
        $Mock = $this->getPdoMock();

        $Mock->insertMultyple('records', [
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
    public function testInsert(): void
    {
        // setup
        $Mock = $this->getPdoMock();

        $Mock->expects($this->once())
            ->method('lastInsertId')
            ->willReturn(1);

        // test body and assertions
        $Mock->insert('records', [
            'id' => 1
        ]);
    }

    /**
     * Testing rollback method
     */
    public function testRollback(): void
    {
        // setup
        $Mock = $this->getPdoMock();

        $Mock->expects($this->once())
            ->method('query')
            ->willReturn(true);

        // test body and assertions
        $Mock->rollback();
    }

    /**
     * Testing commit method
     */
    public function testCommit(): void
    {
        // setup
        $Mock = $this->getUnsetupPdoMock();

        $Mock->expects($this->exactly(2))
            ->method('query')
            ->willReturn(true);

        // test body and assertions
        $Mock->commit();
    }

    /**
     * Testing startTransaction method
     */
    public function testStartTransaction(): void
    {
        // setup
        $Mock = $this->getUnsetupPdoMock();

        $Mock->expects($this->exactly(2))
            ->method('query')
            ->willReturn(true);

        // test body and assertions
        $Mock->startTransaction();
    }

    /**
     * Testing unlock method
     */
    public function testUnlock(): void
    {
        // setup
        $Mock = $this->getPdoMock();

        // test body and assertions
        $Mock->unlock();
    }

    /**
     * Testing lock method
     */
    public function testLock(): void
    {
        // setup
        $Mock = $this->getPdoMock();

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
    public function testDelete(): void
    {
        // setup
        $Mock = $this->getUnsetupPdoMock();

        $Mock->expects($this->exactly(1))
            ->method('query')
            ->willReturn(new ResultMock());

        // test body and assertions
        $Mock->delete('records', 'id=1');
    }

    /**
     * Testing update method
     */
    public function testUpdate(): void
    {
        // setup
        $Mock = $this->getUnsetupPdoMock();
        $Mock->expects($this->exactly(1))
            ->method('query')
            ->willReturn(new ResultMock());

        // test body and assertions
        $Mock->update('som-record', [], '1=1');
    }
}

?>