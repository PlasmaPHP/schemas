<?php
/**
 * Plasma Schemas component
 * Copyright 2018-2019 PlasmaPHP, All Rights Reserved
 *
 * Website: https://github.com/PlasmaPHP
 * License: https://github.com/PlasmaPHP/schemas/blob/master/LICENSE
*/

namespace Plasma\Schemas\Tests;

class SQLSchemaBuilderTest extends TestCase {
    function testConstructor() {
        $schema = (new class() extends \Plasma\Schemas\Schema {
            public $help;
            
            function __construct() {
                
            }
            
            static function getDefinition(): array {
                return array();
            }
            
            static function getTableName(): string {
                return 'test_schemabuilder';
            }
            
            static function getIdentifierColumn(): ?string {
                return null;
            }
        });
        
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $builder = new \Plasma\Schemas\SQLSchemaBuilder(\get_class($schema), (new \Plasma\SQL\Grammar\MySQL()));
        $builder->setRepository($repo);
        
        $this->assertSame($repo, $builder->getRepository());
    }
    
    function testConstructorUnknownClass() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $this->expectException(\Plasma\Exception::class);
        $this->expectExceptionMessage('Schema class does not exist');
        
        $builder = new \Plasma\Schemas\SQLSchemaBuilder('a', (new \Plasma\SQL\Grammar\MySQL()));
    }
    
    function testConstructorInvalidClass() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $this->expectException(\Plasma\Exception::class);
        $this->expectExceptionMessage('Schema class does not implement Schema Interface');
        
        $builder = new \Plasma\Schemas\SQLSchemaBuilder(\stdClass::class, (new \Plasma\SQL\Grammar\MySQL()));
    }
    
    function testFetchAll() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class($repo, array('help' => 5)) extends \Plasma\Schemas\Schema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test_schemabuilder_fetchall', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_schemabuilder_fetchall';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $query = 'SELECT * FROM `test_schemabuilder_fetchall`';
        
        $client
            ->expects($this->any())
            ->method('quote')
            ->will($this->returnCallback(function ($a) {
                return '`'.$a.'`';
            }));
        
        $client
            ->expects($this->once())
            ->method('execute')
            ->with($query, array())
            ->will($this->returnValue((new \React\Promise\Promise(function () {}))));
        
        $builder = new \Plasma\Schemas\SQLSchemaBuilder(\get_class($schema), (new \Plasma\SQL\Grammar\MySQL()));
        $builder->setRepository($repo);
        
        $promise = $builder->fetchAll();
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
    }
    
    function testFetch() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class($repo, array('help' => 5)) extends \Plasma\Schemas\Schema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test_schemabuilder2', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_schemabuilder2';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $query = 'SELECT * FROM `test_schemabuilder2` WHERE `help` = ?';
        
        $client
            ->expects($this->any())
            ->method('quote')
            ->will($this->returnCallback(function ($a) {
                return '`'.$a.'`';
            }));
        
        $client
            ->expects($this->once())
            ->method('execute')
            ->with($query, array(5))
            ->will($this->returnValue((new \React\Promise\Promise(function () {}))));
        
        $builder = new \Plasma\Schemas\SQLSchemaBuilder(\get_class($schema), (new \Plasma\SQL\Grammar\MySQL()));
        $builder->setRepository($repo);
        
        $promise = $builder->fetch(5);
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
    }
    
    function testFetchNoUnique() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class($repo, array('help' => 5)) extends \Plasma\Schemas\Schema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test_schemabuilder8', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_schemabuilder8';
            }
            
            static function getIdentifierColumn(): ?string {
                return null;
            }
        });
        
        $builder = new \Plasma\Schemas\SQLSchemaBuilder(\get_class($schema), (new \Plasma\SQL\Grammar\MySQL()));
        $builder->setRepository($repo);
        
        $this->expectException(\Plasma\Exception::class);
        $this->expectExceptionMessage('Schema has no unique or primary column');
        
        $builder->fetch(5);
    }
    
    function testInsert() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class() extends \Plasma\Schemas\Schema {
            public $help;
            public $help2;
            
            // Let Schemabuilder::insert create the mapper
            function __construct() {
                if(\func_num_args() > 0) {
                    parent::__construct(...\func_get_args());
                }
            }
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test_schemabuilder3', 'help', 'BIGINT', '', 20, 0, null)),
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test_schemabuilder3', 'help2', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_schemabuilder3';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $query = 'INSERT INTO `test_schemabuilder3` (`help2`) VALUES (?)';
        $result = new \Plasma\QueryResult(1, 0, 1, $schema::getDefinition(), null);
        
        $client
            ->expects($this->any())
            ->method('quote')
            ->will($this->returnCallback(function ($a) {
                return '`'.$a.'`';
            }));
        
        $client
            ->expects($this->once())
            ->method('execute')
            ->with($query, array(5))
            ->will($this->returnValue(\React\Promise\resolve($result)));
        
        $name = \get_class($schema);
        
        $builder = new \Plasma\Schemas\SQLSchemaBuilder($name, (new \Plasma\SQL\Grammar\MySQL()));
        $builder->setRepository($repo);
        
        $promise = $builder->insert(array('help2' => 5));
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        $this->assertInstanceOf(\Plasma\Schemas\SchemaCollection::class, $res);
        
        $expected = new $name($repo, array('help' => 1, 'help2' => 5));
        $this->assertEquals($expected, $res->getSchemas()[0]);
    }
    
    function testInsertNotAllFieldsGiven() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class() extends \Plasma\Schemas\Schema {
            public $help;
            public $help2;
            public $help3;
            
            // Let Schemabuilder::insert create the mapper
            function __construct() {
                if(\func_num_args() > 0) {
                    parent::__construct(...\func_get_args());
                }
            }
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test_schemabuilder9', 'help', 'BIGINT', '', 20, 0, null)),
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test_schemabuilder9', 'help2', 'BIGINT', '', 20, 0, null)),
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test_schemabuilder9', 'help3', 'BIGINT', '', 20, 0, null)),
                );
            }
            
            static function getTableName(): string {
                return 'test_schemabuilder9';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $query = 'INSERT INTO `test_schemabuilder9` (`help2`) VALUES (?)';
        $result = new \Plasma\QueryResult(1, 0, 1, null, null);
        
        $query2 = 'SELECT * FROM `test_schemabuilder9` WHERE `help` = ?';
        $result2 = new \Plasma\QueryResult(0, 0, 0, $schema::getDefinition(), array(array('help' => 1, 'help2' => 5, 'help3' => 0)));
        
        $client
            ->expects($this->any())
            ->method('quote')
            ->will($this->returnCallback(function ($a) {
                return '`'.$a.'`';
            }));
        
        $client
            ->expects($this->exactly(2))
            ->method('execute')
            ->withConsecutive(array($query, array(5)), array($query2, array(1)))
            ->willReturnOnConsecutiveCalls($this->returnValue(\React\Promise\resolve($result)), $this->returnValue(\React\Promise\resolve($result2)));
        
        $name = \get_class($schema);
        
        $builder = new \Plasma\Schemas\SQLSchemaBuilder($name, (new \Plasma\SQL\Grammar\MySQL()));
        $repo->registerSchemaBuilder('test_schemabuilder9', $builder);
        
        $promise = $builder->insert(array('help2' => 5));
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        $this->assertInstanceOf(\Plasma\Schemas\SchemaCollection::class, $res);
        
        $expected = new $name($repo, array('help' => 1, 'help2' => 5, 'help3' => 0));
        $this->assertEquals($expected, $res->getSchemas()[0]);
    }
    
    function testInsertNoUnique() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class() extends \Plasma\Schemas\Schema {
            public $help;
            public $help2;
            public $help3;
            
            // Let Schemabuilder::insert create the mapper
            function __construct() {
                if(\func_num_args() > 0) {
                    parent::__construct(...\func_get_args());
                }
            }
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test_schemabuilder10', 'help', 'BIGINT', '', 20, 0, null)),
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test_schemabuilder10', 'help2', 'BIGINT', '', 20, 0, null)),
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test_schemabuilder10', 'help3', 'BIGINT', '', 20, 0, null)),
                );
            }
            
            static function getTableName(): string {
                return 'test_schemabuilder10';
            }
            
            static function getIdentifierColumn(): ?string {
                return null;
            }
        });
        
        $query = 'INSERT INTO `test_schemabuilder10` (`help2`) VALUES (?)';
        $result = new \Plasma\QueryResult(1, 0, 1, $schema::getDefinition(), null);
        
        $client
            ->expects($this->any())
            ->method('quote')
            ->will($this->returnCallback(function ($a) {
                return '`'.$a.'`';
            }));
        
        $client
            ->expects($this->once())
            ->method('execute')
            ->with($query, array(5))
            ->will($this->returnValue(\React\Promise\resolve($result)));
        
        $name = \get_class($schema);
        
        $builder = new \Plasma\Schemas\SQLSchemaBuilder($name, (new \Plasma\SQL\Grammar\MySQL()));
        $builder->setRepository($repo);
        
        $promise = $builder->insert(array('help2' => 5));
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        $this->assertInstanceOf(\Plasma\Schemas\SchemaCollection::class, $res);
    }
    
    function testInsertEmptySet() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class($repo, array('help' => 5)) extends \Plasma\Schemas\Schema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test_schemabuilder4', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_schemabuilder4';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $builder = new \Plasma\Schemas\SQLSchemaBuilder(\get_class($schema), (new \Plasma\SQL\Grammar\MySQL()));
        $builder->setRepository($repo);
        
        $this->expectException(\Plasma\Exception::class);
        $this->expectExceptionMessage('Nothing to insert, empty data set');
        
        $builder->insert(array());
    }
    
    function testInsertUnknownField() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class($repo, array('help' => 5)) extends \Plasma\Schemas\Schema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test_schemabuilder5', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_schemabuilder5';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $builder = new \Plasma\Schemas\SQLSchemaBuilder(\get_class($schema), (new \Plasma\SQL\Grammar\MySQL()));
        $builder->setRepository($repo);
        
        $this->expectException(\Plasma\Exception::class);
        $this->expectExceptionMessage('Unknown field "helpMe"');
        
        $builder->insert(array('helpMe' => 50));
    }
    
    function testInsertAll() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
    
        $schema = (new class() extends \Plasma\Schemas\Schema {
            public $help;
            public $help2;
            public $help3;
            
            // Let Schemabuilder::insert create the mapper
            function __construct() {
                if(\func_num_args() > 0) {
                    parent::__construct(...\func_get_args());
                }
            }
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test_schemabuilder101', 'help', 'BIGINT', '', 20, 0, null)),
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test_schemabuilder101', 'help2', 'BIGINT', '', 20, 0, null)),
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test_schemabuilder101', 'help3', 'BIGINT', '', 20, 0, null)),
                );
            }
            
            static function getTableName(): string {
                return 'test_schemabuilder101';
            }
            
            static function getIdentifierColumn(): ?string {
                return null;
            }
        });
        
        $query = 'INSERT INTO `test_schemabuilder101` (`help2`) VALUES (?)';
        $result = new \Plasma\QueryResult(1, 0, 1, $schema::getDefinition(), null);
        
        $transaction = $this->getMockBuilder(\Plasma\TransactionInterface::class)
            ->getMock();
        
        $statement = $this->getMockBuilder(\Plasma\StatementInterface::class)
            ->getMock();
        
        $transaction
            ->expects($this->once())
            ->method('prepare')
            ->with($query)
            ->will($this->returnValue(\React\Promise\resolve($statement)));
        
        $transaction
            ->expects($this->once())
            ->method('commit')
            ->will($this->returnValue(\React\Promise\resolve()));
        
        $statement
            ->expects($this->exactly(2))
            ->method('execute')
            ->will($this->returnValue(\React\Promise\resolve($result)));
        
        $client
            ->expects($this->once())
            ->method('beginTransaction')
            ->will($this->returnValue(\React\Promise\resolve($transaction)));
        
        $client
            ->expects($this->any())
            ->method('quote')
            ->will($this->returnCallback(function ($a) {
                return '`'.$a.'`';
            }));
    
        $name = \get_class($schema);
    
        $builder = new \Plasma\Schemas\SQLSchemaBuilder($name, (new \Plasma\SQL\Grammar\MySQL()));
        $builder->setRepository($repo);
        
        $promise = $builder->insertAll(array(
            array('help2' => 5),
            array('help2' => 250)
         ));
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        $this->assertInstanceOf(\Plasma\Schemas\SchemaCollection::class, $res);
        
        $this->assertSame(2, \count($res->getSchemas()));
        $this->assertSame(5, $res->getSchemas()[0]->help2);
        $this->assertSame(250, $res->getSchemas()[1]->help2);
    }
    
    function testUpdate() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class($repo, array('help' => 5)) extends \Plasma\Schemas\Schema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test_schemabuilder2112', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_schemabuilder2112';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $query = 'UPDATE `test_schemabuilder2112` SET `help` = ? WHERE `help` = ?';
        
        $client
            ->expects($this->any())
            ->method('quote')
            ->will($this->returnCallback(function ($a) {
                return '`'.$a.'`';
            }));
        
        $client
            ->expects($this->once())
            ->method('execute')
            ->with($query, array(5, 50))
            ->will($this->returnValue((new \React\Promise\Promise(function () {}))));
        
        $builder = new \Plasma\Schemas\SQLSchemaBuilder(\get_class($schema), (new \Plasma\SQL\Grammar\MySQL()));
        $builder->setRepository($repo);
        
        $promise = $builder->update(array('help' => 5), 'help', 50);
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
    }
    
    function testBuildSchemas() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class($repo, array('help' => 5)) extends \Plasma\Schemas\Schema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test_schemabuilder7', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_schemabuilder7';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $builder = new \Plasma\Schemas\SQLSchemaBuilder(\get_class($schema), (new \Plasma\SQL\Grammar\MySQL()));
        $builder->setRepository($repo);
        
        $rows = array(
            array('help' => 5),
            array('help' => 7)
        );
        
        $result = new \Plasma\QueryResult(0, 0, 0, $schema->getDefinition(), $rows);
        
        $collection = $builder->buildSchemas($result);
        $this->assertInstanceOf(\Plasma\Schemas\SchemaCollection::class, $collection);
        
        $expectedSchemas = array(
            $schema::build($repo, $rows[0]),
            $schema::build($repo, $rows[1])
        );
        
        $this->assertEquals($expectedSchemas, $collection->getSchemas());
        $this->assertSame($result, $collection->getResult());
    }
}
