<?php
/**
 * Plasma Schemas component
 * Copyright 2018-2019 PlasmaPHP, All Rights Reserved
 *
 * Website: https://github.com/PlasmaPHP
 * License: https://github.com/PlasmaPHP/schemas/blob/master/LICENSE
 * @noinspection PhpUnhandledExceptionInspection
*/

namespace Plasma\Schemas\Tests;

use Plasma\QueryBuilderInterface;
use Plasma\QueryResult;
use Plasma\Schemas\Repository;
use Plasma\Schemas\SchemaCollection;
use Plasma\Schemas\Statement;
use Plasma\SQLQueryBuilderInterface;
use Plasma\StatementInterface;
use React\Promise\PromiseInterface;
use function React\Promise\resolve;

class StatementTest extends TestCase {
    function testGetID() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $mock = $this->getMock();
        $statement = new Statement($repo, $mock);
        
        /** @noinspection PhpUndefinedMethodInspection */
        $mock
            ->expects(self::once())
            ->method('getID')
            ->willReturn(1);
        
        $statement->getID();
    }
    
    function testGetQuery() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $mock = $this->getMock();
        $statement = new Statement($repo, $mock);
        
        /** @noinspection PhpUndefinedMethodInspection */
        $mock
            ->expects(self::once())
            ->method('getQuery')
            ->willReturn('SELECT 1');
        
        $statement->getQuery();
    }
    
    function testIsClosed() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $mock = $this->getMock();
        $statement = new Statement($repo, $mock);
        
        /** @noinspection PhpUndefinedMethodInspection */
        $mock
            ->expects(self::once())
            ->method('isClosed')
            ->willReturn(false);
        
        $statement->isClosed();
    }
    
    function testClose() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $mock = $this->getMock();
        $statement = new Statement($repo, $mock);
        
        /** @noinspection PhpUndefinedMethodInspection */
        $mock
            ->expects(self::once())
            ->method('close')
            ->willReturn(resolve());
        
        $statement->close();
    }
    
    function testExecute() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $mock = $this->getMock();
        $statement = new Statement($repo, $mock);
        
        $result = new QueryResult(0, 0, null, array(), array());
        
        /** @noinspection PhpUndefinedMethodInspection */
        $mock
            ->expects(self::once())
            ->method('execute')
            ->with(array())
            ->willReturn(resolve($result));
        
        $promise = $statement->execute();
        self::assertInstanceOf(PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        self::assertInstanceOf(SchemaCollection::class, $res);
    }
    
    function testRunQuery() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $mock = $this->getMock();
        $statement = new Statement($repo, $mock);
        
        $result = new QueryResult(0, 0, null, array(), array());
        
        $qb = (new class() implements SQLQueryBuilderInterface {
            /** @noinspection PhpInconsistentReturnPointsInspection */
            static function create(): QueryBuilderInterface {}
            
            function getQuery() {
                return 'SELECT 1';
            }
            
            function getParameters(): array {
                return array();
            }
        });
        
        /** @noinspection PhpUndefinedMethodInspection */
        $mock
            ->expects(self::once())
            ->method('runQuery')
            ->with($qb)
            ->willReturn(resolve($result));
        
        $promise = $statement->runQuery($qb);
        self::assertInstanceOf(PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        self::assertInstanceOf(SchemaCollection::class, $res);
    }
    
    function getMock(): StatementInterface {
        return $this->getMockBuilder(StatementInterface::class)
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
