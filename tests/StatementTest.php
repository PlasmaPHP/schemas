<?php
/**
 * Plasma Schemas component
 * Copyright 2018-2019 PlasmaPHP, All Rights Reserved
 *
 * Website: https://github.com/PlasmaPHP
 * License: https://github.com/PlasmaPHP/schemas/blob/master/LICENSE
*/

namespace Plasma\Schemas\Tests;

class StatementTest extends TestCase {
    function testGetID() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $mock = $this->getMock();
        $statement = new \Plasma\Schemas\Statement($repo, $mock);
        
        $mock
            ->expects($this->once())
            ->method('getID')
            ->will($this->returnValue(1));
        
        $statement->getID();
    }
    
    function testGetQuery() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $mock = $this->getMock();
        $statement = new \Plasma\Schemas\Statement($repo, $mock);
        
        $mock
            ->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue('SELECT 1'));
        
        $statement->getQuery();
    }
    
    function testIsClosed() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $mock = $this->getMock();
        $statement = new \Plasma\Schemas\Statement($repo, $mock);
        
        $mock
            ->expects($this->once())
            ->method('isClosed')
            ->will($this->returnValue(false));
        
        $statement->isClosed();
    }
    
    function testClose() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $mock = $this->getMock();
        $statement = new \Plasma\Schemas\Statement($repo, $mock);
        
        $mock
            ->expects($this->once())
            ->method('close')
            ->will($this->returnValue(\React\Promise\resolve()));
        
        $statement->close();
    }
    
    function testExecute() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $mock = $this->getMock();
        $statement = new \Plasma\Schemas\Statement($repo, $mock);
        
        $result = new \Plasma\QueryResult(0, 0, null, array(), array());
        
        $mock
            ->expects($this->once())
            ->method('execute')
            ->with(array())
            ->will($this->returnValue(\React\Promise\resolve($result)));
        
        $promise = $statement->execute();
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        $this->assertInstanceOf(\Plasma\Schemas\SchemaCollection::class, $res);
    }
    
    function testRunQuery() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $mock = $this->getMock();
        $statement = new \Plasma\Schemas\Statement($repo, $mock);
        
        $result = new \Plasma\QueryResult(0, 0, null, array(), array());
        
        $qb = (new class() implements \Plasma\SQLQueryBuilderInterface {
            static function create(): \Plasma\QueryBuilderInterface {}
            
            function getQuery() {
                return 'SELECT 1';
            }
            
            function getParameters(): array {
                return array();
            }
        });
        
        $mock
            ->expects($this->once())
            ->method('runQuery')
            ->with($qb)
            ->will($this->returnValue(\React\Promise\resolve($result)));
        
        $promise = $statement->runQuery($qb);
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        $this->assertInstanceOf(\Plasma\Schemas\SchemaCollection::class, $res);
    }
    
    function getMock(): \Plasma\StatementInterface {
        return $this->getMockBuilder(\Plasma\StatementInterface::class)
            ->setMethods(array(
                'getID',
                'getQuery',
                'isClosed',
                'close',
                'execute',
                'runQuery'
            ))
            ->getMock();
    }
}
