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

use Plasma\ClientInterface;
use Plasma\CommandInterface;
use Plasma\CursorInterface;
use Plasma\DriverInterface;
use Plasma\Exception;
use Plasma\QueryBuilderInterface;
use Plasma\QueryResult;
use Plasma\QueryResultInterface;
use Plasma\Schemas\AbstractSchema;
use Plasma\Schemas\Repository;
use Plasma\Schemas\SchemaCollection;
use Plasma\Schemas\SchemaInterface;
use Plasma\Schemas\SQLDirectory;
use Plasma\Schemas\Statement;
use Plasma\Schemas\Transaction;
use Plasma\SQL\Grammar\MySQL;
use Plasma\StatementInterface;
use Plasma\StreamQueryResult;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use function React\Promise\resolve;

class RepositoryTest extends TestCase {
    function testGetClient() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $client = $repo->getClient();
        self::assertInstanceOf(ClientInterface::class, $client);
    }
    
    function testGetBuilder() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $builder = new SQLDirectory(\get_class($this->getSchema($repo)), (new MySQL()));
        self::assertSame($repo, $repo->registerDirectory('test', $builder));
        
        self::assertSame($builder, $repo->getDirectory('test'));
    }
    
    function testGetBuilderUnregistered() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The schema is not registered');
        
        $repo->getDirectory('test');
    }
    
    function testRegisterBuilderAlreadyRegistered() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $builder = new SQLDirectory(\get_class($this->getSchema($repo)), (new MySQL()));
        self::assertSame($repo, $repo->registerDirectory('test', $builder));
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The schema is already registered');
        
        $repo->registerDirectory('test', $builder);
    }
    
    function testUnregisterBuilder() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $builder = new SQLDirectory(\get_class($this->getSchema($repo)), (new MySQL()));
        self::assertSame($repo, $repo->registerDirectory('test', $builder));
        
        self::assertSame($repo, $repo->unregisterDirectory('test'));
    }
    
    function testQueryInvocation() {
        $client = $this->getClientMock();
        $repo = (new class($client) extends Repository {
            function handleQueryResult(QueryResultInterface $result) {
                return true;
            }
        });
        
        $result = new QueryResult(0, 0, null, null, null);
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::once())
            ->method('query')
            ->willReturn(resolve($result));
        
        $promise = $repo->query('help');
        self::assertInstanceOf(PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        self::assertTrue($res);
    }
    
    function testExecuteInvocation() {
        $client = $this->getClientMock();
        $repo = (new class($client) extends Repository {
            function handleQueryResult(QueryResultInterface $result) {
                return true;
            }
        });
        
        $result = new QueryResult(0, 0, null, null, null);
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::once())
            ->method('execute')
            ->willReturn(resolve($result));
        
        $promise = $repo->execute('help');
        self::assertInstanceOf(PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        self::assertTrue($res);
    }
    
    function testPrepareInvocation() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $statement = $this->getMockBuilder(StatementInterface::class)
            ->setMethods(array(
                'getID',
                'getQuery',
                'isClosed',
                'close',
                'execute',
                'runQuery'
            ))
            ->getMock();
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::once())
            ->method('prepare')
            ->willReturn(resolve($statement));
        
        $promise = $repo->prepare('help');
        self::assertInstanceOf(PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        self::assertInstanceOf(Statement::class, $res);
    }
    
    function testHandleQueryReuslt() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $schema = $this->getSchema($repo);
        $builder = new SQLDirectory(\get_class($schema), (new MySQL()));
        $repo->registerDirectory($schema::getTableName(), $builder);
        
        $result = new QueryResult(0, 0, null, $schema::getDefinition(), array(
            array('help' => 50),
            array('help' => 70)
        ));
        
        $res = $repo->handleQueryResult($result);
        self::assertInstanceOf(SchemaCollection::class, $res);
    }
    
    function testHandleQueryResultStream() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $stream = new StreamQueryResult($this->getCommandMock(), 0, 0, null, null);
        
        $promise = $repo->handleQueryResult($stream);
        self::assertInstanceOf(PromiseInterface::class, $promise);
        
        $stream->emit('close', array());
        
        $res = $this->await($promise);
        self::assertInstanceOf(SchemaCollection::class, $res);
    }
    
    /**
     * @param $method
     * @param $args
     * @param $returnValue
     * @dataProvider providerInherited
     */
    function testInherited($method, $args, $returnValue) {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::once())
            ->method($method)
            ->with(...$args)
            ->willReturn($returnValue);
        
        $repo->$method(...$args);
    }
    
    function providerInherited() {
        $driver = $this->getDriverMock();
        $command = $this->getCommandMock();
        $query = $this->getMockBuilder(QueryBuilderInterface::class)
            ->setMethods(array(
                'create',
                'getQuery',
                'getParameters'
            ))
            ->getMock();
        
        return array(
            array('getConnectionCount', array(), 0),
            array('checkinConnection', array($driver), null),
            array('beginTransaction', array(), (new Promise(static function () {}))),
            array('close', array(), (new Promise(static function () {}))),
            array('quit', array(), null),
            array('runCommand', array($command), (new Promise(static function () {}))),
            array('runQuery', array($query), (new Promise(static function () {}))),
            array('createReadCursor', array('SELECT 1', array()), (new Promise(static function () {}))),
            array('quote', array('help'), '`help`'),
            array('on', array('data', static function () {}), null),
            array('once', array('data', static function () {}), null),
            array('removeListener', array('data', static function () {}), null),
            array('removeAllListeners', array('data'), null),
            array('listeners', array('data'), array()),
            array('emit', array('data', array()), null)
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
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::once())
            ->method($method)
            ->with(...$args)
            ->willReturn($returnValue);
        
        $return = $repo->$method(...$args);
        self::assertInstanceOf(PromiseInterface::class, $return);
        
        $return = $this->await($return);
        self::assertInstanceOf($expectedReturnType, $return);
    }
    
    function providerInheritedType() {
        $client = $this->getClientMock();
        $driver = $this->getDriverMock();
        
        $query = $this->getMockBuilder(QueryBuilderInterface::class)
            ->setMethods(array(
                'create',
                'getQuery',
                'getParameters'
            ))
            ->getMock();
        
        $transaction = new \Plasma\Transaction($client, $driver, 0);
        
        $qr1 = new QueryResult(0, 0, null, array(), array());
        $qr2 = new QueryResult(0, 0, null, null, null);
        
        $cursor = $this->getMockBuilder(CursorInterface::class)
            ->setMethods(array(
                'isClosed',
                'close',
                'fetch'
            ))
            ->getMock();
        
        return array(
            array('beginTransaction', array(), resolve($transaction), Transaction::class),
            array('query', array('SELECT 1'), resolve($qr2), QueryResultInterface::class),
            array('query', array('SELECT 1'), resolve($qr1), SchemaCollection::class),
            array('execute', array('SELECT 1', array()), resolve($qr2), QueryResultInterface::class),
            array('execute', array('SELECT 1', array()), resolve($qr1), SchemaCollection::class),
            array('runQuery', array($query), resolve($qr1), SchemaCollection::class),
            array('createReadCursor', array('SELECT 1', array()), resolve($cursor), CursorInterface::class)
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
    
    function getCommandMock(): CommandInterface {
        return $this->getMockBuilder(CommandInterface::class)
            ->setMethods(array(
                'getEncodedMessage',
                'onComplete',
                'onError',
                'onNext',
                'hasFinished',
                'waitForCompletion',
                'on',
                'once',
                'removeListener',
                'removeAllListeners',
                'listeners',
                'emit'
            ))
            ->getMock();
    }
    
    function getSchema(Repository $repo): SchemaInterface {
        return (new class($repo, array('help' => 5)) extends AbstractSchema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new ColumnDefinition('test_repository', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_repository';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
    }
}
