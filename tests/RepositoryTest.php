<?php
/**
 * Plasma Schemas component
 * Copyright 2018-2019 PlasmaPHP, All Rights Reserved
 *
 * Website: https://github.com/PlasmaPHP
 * License: https://github.com/PlasmaPHP/schemas/blob/master/LICENSE
*/

namespace Plasma\Schemas\Tests;

class RepositoryTest extends TestCase {
    function testGetClient() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $client = $repo->getClient();
        $this->assertInstanceOf(\Plasma\ClientInterface::class, $client);
    }
    
    function testGetBuilder() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $builder = new \Plasma\Schemas\SQLSchemaBuilder(\get_class($this->getSchema($repo)));
        $this->assertSame($repo, $repo->registerSchemaBuilder('test', $builder));
        
        $this->assertSame($builder, $repo->getSchemaBuilder('test'));
    }
    
    function testGetBuilderUnregistered() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $this->expectException(\Plasma\Exception::class);
        $this->expectExceptionMessage('The schema is not registered');
        
        $repo->getSchemaBuilder('test');
    }
    
    function testRegisterBuilderAlreadyRegistered() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $builder = new \Plasma\Schemas\SQLSchemaBuilder(\get_class($this->getSchema($repo)));
        $this->assertSame($repo, $repo->registerSchemaBuilder('test', $builder));
        
        $this->expectException(\Plasma\Exception::class);
        $this->expectExceptionMessage('The schema is already registered');
        
        $repo->registerSchemaBuilder('test', $builder);
    }
    
    function testUnregisterBuilder() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $builder = new \Plasma\Schemas\SQLSchemaBuilder(\get_class($this->getSchema($repo)));
        $this->assertSame($repo, $repo->registerSchemaBuilder('test', $builder));
        
        $this->assertSame($repo, $repo->unregisterSchemaBuilder('test'));
    }
    
    function testQueryInvocation() {
        $client = $this->getClientMock();
        $repo = (new class($client) extends \Plasma\Schemas\Repository {
            function handleQueryResult(\Plasma\QueryResultInterface $result) {
                return true;
            }
        });
        
        $result = new \Plasma\QueryResult(0, 0, null, null, null);
        
        $client
            ->expects($this->once())
            ->method('query')
            ->will($this->returnValue(\React\Promise\resolve($result)));
        
        $promise = $repo->query('help');
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        $this->assertTrue($res);
    }
    
    function testExecuteInvocation() {
        $client = $this->getClientMock();
        $repo = (new class($client) extends \Plasma\Schemas\Repository {
            function handleQueryResult(\Plasma\QueryResultInterface $result) {
                return true;
            }
        });
        
        $result = new \Plasma\QueryResult(0, 0, null, null, null);
        
        $client
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue(\React\Promise\resolve($result)));
        
        $promise = $repo->execute('help');
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        $this->assertTrue($res);
    }
    
    function testPrepareInvocation() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $statement = $this->getMockBuilder(\Plasma\StatementInterface::class)
            ->setMethods(array(
                'getID',
                'getQuery',
                'isClosed',
                'close',
                'execute'
            ))
            ->getMock();
        
        $client
            ->expects($this->once())
            ->method('prepare')
            ->will($this->returnValue(\React\Promise\resolve($statement)));
        
        $promise = $repo->prepare('help');
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        $this->assertInstanceOf(\Plasma\Schemas\Statement::class, $res);
    }
    
    function testHandleQueryReuslt() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = $this->getSchema($repo);
        $builder = new \Plasma\Schemas\SQLSchemaBuilder(\get_class($schema));
        $repo->registerSchemaBuilder($schema->getTableName(), $builder);
        
        $result = new \Plasma\QueryResult(0, 0, null, $schema->getDefinition(), array(
            array('help' => 50),
            array('help' => 70)
        ));
        
        $res = $repo->handleQueryResult($result);
        $this->assertInstanceOf(\Plasma\Schemas\SchemaCollection::class, $res);
    }
    
    function testHandleQueryResultStream() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $stream = new \Plasma\StreamQueryResult($this->getDriverMock(), $this->getCommandMock(), 0, 0, null, null);
        
        $promise = $repo->handleQueryResult($stream);
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
        
        $stream->emit('close', array());
        
        $res = $this->await($promise);
        $this->assertInstanceOf(\Plasma\Schemas\SchemaCollection::class, $res);
    }
    
    /**
     * @dataProvider providerInherited
     */
    function testInherited($method, $args, $returnValue) {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $client
            ->expects($this->once())
            ->method($method)
            ->with(...$args)
            ->will($this->returnValue($returnValue));
        
        $repo->$method(...$args);
    }
    
    function providerInherited() {
        $driver = $this->getDriverMock();
        $command = $this->getCommandMock();
        $query = $this->getMockBuilder(\Plasma\QuerybuilderInterface::class)
            ->setMethods(array(
                'create',
                'getQuery',
                'getParameters'
            ))
            ->getMock();
        
        return array(
            array('getConnectionCount', array(), 0),
            array('checkinConnection', array($driver), null),
            array('beginTransaction', array(), (new \React\Promise\Promise(function () {}))),
            array('close', array(), (new \React\Promise\Promise(function () {}))),
            array('quit', array(), null),
            array('runCommand', array($command), (new \React\Promise\Promise(function () {}))),
            array('runQuery', array($query), (new \React\Promise\Promise(function () {}))),
            array('quote', array('help'), '`help`'),
            array('on', array('data', function () {}), null),
            array('once', array('data', function () {}), null),
            array('removeListener', array('data', function () {}), null),
            array('removeAllListeners', array('data'), null),
            array('listeners', array('data'), array()),
            array('emit', array('data', array()), null)
        );
    }
    
    function getDriverMock(): \Plasma\DriverInterface {
        return $driver = $this->getMockBuilder(\Plasma\DriverInterface::class)
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
    
    function getCommandMock(): \Plasma\CommandInterface {
        return $this->getMockBuilder(\Plasma\CommandInterface::class)
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
    
    function getSchema(\Plasma\Schemas\Repository $repo): \Plasma\Schemas\SchemaInterface {
        return (new class($repo, array('help' => 5)) extends \Plasma\Schemas\Schema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test_repository', 'help', 'BIGINT', '', 20, 0, null))
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
