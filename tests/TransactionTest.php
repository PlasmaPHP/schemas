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

use Plasma\DriverInterface;
use Plasma\QueryBuilderInterface;
use Plasma\QueryResult;
use Plasma\QueryResultInterface;
use Plasma\Schemas\Repository;
use Plasma\Schemas\SchemaCollection;
use Plasma\Schemas\Statement;
use Plasma\StatementInterface;
use Plasma\TransactionInterface;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use function React\Promise\resolve;

class TransactionTest extends TestCase {
    /**
     * @param $method
     * @param $args
     * @param $returnValue
     * @dataProvider providerInherited
     */
    function testInherited($method, $args, $returnValue) {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $mock = $this->getMock();
        $transaction = new \Plasma\Schemas\Transaction($repo, $mock);
        
        /** @noinspection PhpUndefinedMethodInspection */
        $mock
            ->expects(self::once())
            ->method($method)
            ->with(...$args)
            ->willReturn($returnValue);
        
        $transaction->$method(...$args);
    }
    
    function providerInherited() {
        $query = $this->getMockBuilder(QueryBuilderInterface::class)
            ->setMethods(array(
                'create',
                'getQuery',
                'getParameters'
            ))
            ->getMock();
        
        return array(
            array('getIsolationLevel', array(), 0),
            array('isActive', array(), true),
            array('commit', array(), (new Promise(static function () {}))),
            array('rollback', array(), (new Promise(static function () {}))),
            array('createSavepoint', array('test'), (new Promise(static function () {}))),
            array('rollbackTo', array('test'), (new Promise(static function () {}))),
            array('releaseSavepoint', array('test'), (new Promise(static function () {}))),
            array('query', array('SELECT 1'), (new Promise(static function () {}))),
            array('prepare', array('SELECT 1'), (new Promise(static function () {}))),
            array('execute', array('SELECT 1', array()), (new Promise(static function () {}))),
            array('quote', array('test'), '`test`'),
            array('runQuery', array($query), (new Promise(static function () {})))
        );
    }
    
    /**
     * @param $method
     * @param $args
     * @param $returnValue
     * @param $expectedReturnType
     * @dataProvider providerInheritedType
     */
    function testInheritedType($method, $args, $returnValue, $expectedReturnType) {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $mock = $this->getMock();
        $transaction = new \Plasma\Schemas\Transaction($repo, $mock);
        
        /** @noinspection PhpUndefinedMethodInspection */
        $mock
            ->expects(self::once())
            ->method($method)
            ->with(...$args)
            ->willReturn($returnValue);
        
        $return = $transaction->$method(...$args);
        self::assertInstanceOf(PromiseInterface::class, $return);
        
        $return = $this->await($return);
        self::assertInstanceOf($expectedReturnType, $return);
    }
    
    function providerInheritedType() {
        $query = $this->getMockBuilder(QueryBuilderInterface::class)
            ->setMethods(array(
               'create',
               'getQuery',
               'getParameters'
            ))
            ->getMock();
        
        $stmt = $this->getMockBuilder(StatementInterface::class)
            ->setMethods(array(
                'getID',
                'getQuery',
                'isClosed',
                'close',
                'execute',
                'runQuery'
            ))
            ->getMock();
        
        $qr1 = new QueryResult(0, 0, null, array(), array());
        $qr2 = new QueryResult(0, 0, null, null, null);
        
        return array(
            array('query', array('SELECT 1'), resolve($qr2), QueryResultInterface::class),
            array('query', array('SELECT 1'), resolve($qr1), SchemaCollection::class),
            array('prepare', array('SELECT 1'), resolve($stmt), Statement::class),
            array('execute', array('SELECT 1', array()), resolve($qr2), QueryResultInterface::class),
            array('execute', array('SELECT 1', array()), resolve($qr1), SchemaCollection::class),
            array('runQuery', array($query), resolve($qr1), SchemaCollection::class),
        );
    }
    
    function getDriverMock(): DriverInterface {
        return $this->getMockBuilder(DriverInterface::class)
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
    
    function getMock(): TransactionInterface {
        return $this->getMockBuilder(TransactionInterface::class)
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
