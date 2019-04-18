<?php
/**
 * Plasma Schemas component
 * Copyright 2018-2019 PlasmaPHP, All Rights Reserved
 *
 * Website: https://github.com/PlasmaPHP
 * License: https://github.com/PlasmaPHP/schemas/blob/master/LICENSE
 */

namespace Plasma\Schemas\Tests;

class TransactionTest extends TestCase {
    /**
     * @dataProvider providerInherited
     */
    function testInherited($method, $args, $returnValue) {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $mock = $this->getMock();
        $transaction = new \Plasma\Schemas\Transaction($repo, $mock);
        
        $mock
            ->expects($this->once())
            ->method($method)
            ->with(...$args)
            ->will($this->returnValue($returnValue));
        
        $transaction->$method(...$args);
    }
    
    function providerInherited() {
        $query = $this->getMockBuilder(\Plasma\QueryBuilderInterface::class)
            ->setMethods(array(
                'create',
                'getQuery',
                'getParameters'
            ))
            ->getMock();
        
        return array(
            array('getIsolationLevel', array(), 0),
            array('isActive', array(), true),
            array('commit', array(), (new \React\Promise\Promise(function () {}))),
            array('rollback', array(), (new \React\Promise\Promise(function () {}))),
            array('createSavepoint', array('test'), (new \React\Promise\Promise(function () {}))),
            array('rollbackTo', array('test'), (new \React\Promise\Promise(function () {}))),
            array('releaseSavepoint', array('test'), (new \React\Promise\Promise(function () {}))),
            array('query', array('SELECT 1'), (new \React\Promise\Promise(function () {}))),
            array('prepare', array('SELECT 1'), (new \React\Promise\Promise(function () {}))),
            array('execute', array('SELECT 1', array()), (new \React\Promise\Promise(function () {}))),
            array('quote', array('test'), '`test`'),
            array('runQuery', array($query), (new \React\Promise\Promise(function () {})))
        );
    }
    
    /**
     * @dataProvider providerInheritedType
     */
    function testInheritedType($method, $args, $returnValue, $expectedReturnType) {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $mock = $this->getMock();
        $transaction = new \Plasma\Schemas\Transaction($repo, $mock);
        
        $mock
            ->expects($this->once())
            ->method($method)
            ->with(...$args)
            ->will($this->returnValue($returnValue));
        
        $return = $transaction->$method(...$args);
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $return);
        
        $return = $this->await($return);
        $this->assertInstanceOf($expectedReturnType, $return);
    }
    
    function providerInheritedType() {
        $client = $this->getClientMock();
        $driver = $this->getDriverMock();
        
        $query = $this->getMockBuilder(\Plasma\QueryBuilderInterface::class)
            ->setMethods(array(
               'create',
               'getQuery',
               'getParameters'
            ))
            ->getMock();
        
        $stmt = $this->getMockBuilder(\Plasma\StatementInterface::class)
            ->setMethods(array(
                'getID',
                'getQuery',
                'isClosed',
                'close',
                'execute',
                'runQuery'
            ))
            ->getMock();
        
        $transaction = new \Plasma\Transaction($client, $driver, 0);
        
        $qr1 = new \Plasma\QueryResult(0, 0, null, array(), array());
        $qr2 = new \Plasma\QueryResult(0, 0, null, null, null);
        
        return array(
            array('query', array('SELECT 1'), \React\Promise\resolve($qr2), \Plasma\QueryResultInterface::class),
            array('query', array('SELECT 1'), \React\Promise\resolve($qr1), \Plasma\Schemas\SchemaCollection::class),
            array('prepare', array('SELECT 1'), \React\Promise\resolve($stmt), \Plasma\Schemas\Statement::class),
            array('execute', array('SELECT 1', array()), \React\Promise\resolve($qr2), \Plasma\QueryResultInterface::class),
            array('execute', array('SELECT 1', array()), \React\Promise\resolve($qr1), \Plasma\Schemas\SchemaCollection::class),
            array('runQuery', array($query), \React\Promise\resolve($qr1), \Plasma\Schemas\SchemaCollection::class),
        );
    }
    
    function getDriverMock(): \Plasma\DriverInterface {
        return $this->getMockBuilder(\Plasma\DriverInterface::class)
            ->setMethods(array(
                'getConnectionState',
                'getBusyState',
                'getBacklogLength',
                'connect',
                'pauseStreamConsumption',
                'resumeStreamConsumption',
                'isInTransaction',
                'beginTransaction',
                'endTransaction',
                'close',
                'quit',
                'runCommand',
                'runQuery',
                'query',
                'prepare',
                'execute',
                'createReadCursor',
                'quote',
                'on',
                'once',
                'removeListener',
                'removeAllListeners',
                'listeners',
                'emit'
            ))
            ->getMock();
    }
    
    function getMock(): \Plasma\TransactionInterface {
        return $this->getMockBuilder(\Plasma\TransactionInterface::class)
            ->setMethods(array(
                '__destruct',
                'getIsolationLevel',
                'isActive',
                'commit',
                'rollback',
                'createSavepoint',
                'rollbackTo',
                'releaseSavepoint',
                'query',
                'prepare',
                'execute',
                'quote',
                'runQuery'
                
            ))
            ->getMock();
    }
}