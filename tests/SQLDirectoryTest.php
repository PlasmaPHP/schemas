<?php
/**
 * Plasma Schemas component
 * Copyright 2018-2019 PlasmaPHP, All Rights Reserved
 *
 * Website: https://github.com/PlasmaPHP
 * License: https://github.com/PlasmaPHP/schemas/blob/master/LICENSE
 * @noinspection PhpUnhandledExceptionInspection *//**
 * @noinspection SqlResolve
*/

namespace Plasma\Schemas\Tests;

class SQLDirectoryTest extends TestCase {
    function testFetchAll() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class($repo, array('help' => 5)) extends \Plasma\Schemas\AbstractSchema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory_fetchall', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory_fetchall';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $query = 'SELECT * FROM `test_Directory_fetchall` AS t0';
        
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
        
        $builder = new \Plasma\Schemas\SQLDirectory(\get_class($schema), (new \Plasma\SQL\Grammar\MySQL()));
        $builder->setRepository($repo);
        
        $promise = $builder->fetchAll();
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
    }
    
    function testFetch() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class($repo, array('help' => 5)) extends \Plasma\Schemas\AbstractSchema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory2', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory2';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $query = 'SELECT * FROM `test_Directory2` AS t0 WHERE `help` = ?';
        
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
        
        $builder = new \Plasma\Schemas\SQLDirectory(\get_class($schema), (new \Plasma\SQL\Grammar\MySQL()));
        $builder->setRepository($repo);
        
        $promise = $builder->fetch(5);
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
    }
    
    function testFetchNoUnique() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class($repo, array('help' => 5)) extends \Plasma\Schemas\AbstractSchema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory8', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory8';
            }
            
            static function getIdentifierColumn(): ?string {
                return null;
            }
        });
        
        $builder = new \Plasma\Schemas\SQLDirectory(\get_class($schema), (new \Plasma\SQL\Grammar\MySQL()));
        $builder->setRepository($repo);
        
        $this->expectException(\Plasma\Exception::class);
        $this->expectExceptionMessage('AbstractSchema has no unique or primary column');
        
        $builder->fetch(5);
    }
    
    function testInsert() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class() extends \Plasma\Schemas\AbstractSchema {
            public $help;
            public $help2;
            
            // Let Directory::insert create the mapper
            function __construct() {
                if(\func_num_args() > 0) {
                    parent::__construct(...\func_get_args());
                }
            }
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory3', 'help', 'BIGINT', '', 20, 0, null)),
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory3', 'help2', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory3';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $query = 'INSERT INTO `test_Directory3` (`help2`) VALUES (?)';
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
        
        $builder = new \Plasma\Schemas\SQLDirectory($name, (new \Plasma\SQL\Grammar\MySQL()));
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
        
        $schema = (new class() extends \Plasma\Schemas\AbstractSchema {
            public $help;
            public $help2;
            public $help3;
            
            // Let Directory::insert create the mapper
            function __construct() {
                if(\func_num_args() > 0) {
                    parent::__construct(...\func_get_args());
                }
            }
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory9', 'help', 'BIGINT', '', 20, 0, null)),
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory9', 'help2', 'BIGINT', '', 20, 0, null)),
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory9', 'help3', 'BIGINT', '', 20, 0, null)),
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory9';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $query = 'INSERT INTO `test_Directory9` (`help2`) VALUES (?)';
        $result = new \Plasma\QueryResult(1, 0, 1, null, null);
        
        $query2 = 'SELECT * FROM `test_Directory9` AS t0 WHERE `help` = ?';
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
        
        $builder = new \Plasma\Schemas\SQLDirectory($name, (new \Plasma\SQL\Grammar\MySQL()));
        $repo->registerDirectory('test_Directory9', $builder);
        
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
        
        $schema = (new class() extends \Plasma\Schemas\AbstractSchema {
            public $help;
            public $help2;
            public $help3;
            
            // Let Directory::insert create the mapper
            function __construct() {
                if(\func_num_args() > 0) {
                    parent::__construct(...\func_get_args());
                }
            }
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory10', 'help', 'BIGINT', '', 20, 0, null)),
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory10', 'help2', 'BIGINT', '', 20, 0, null)),
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory10', 'help3', 'BIGINT', '', 20, 0, null)),
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory10';
            }
            
            static function getIdentifierColumn(): ?string {
                return null;
            }
        });
        
        $query = 'INSERT INTO `test_Directory10` (`help2`) VALUES (?)';
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
        
        $builder = new \Plasma\Schemas\SQLDirectory($name, (new \Plasma\SQL\Grammar\MySQL()));
        $builder->setRepository($repo);
        
        $promise = $builder->insert(array('help2' => 5));
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        $this->assertInstanceOf(\Plasma\Schemas\SchemaCollection::class, $res);
    }
    
    function testInsertEmptySet() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class($repo, array('help' => 5)) extends \Plasma\Schemas\AbstractSchema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory4', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory4';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $builder = new \Plasma\Schemas\SQLDirectory(\get_class($schema), (new \Plasma\SQL\Grammar\MySQL()));
        $builder->setRepository($repo);
        
        $this->expectException(\Plasma\Exception::class);
        $this->expectExceptionMessage('Nothing to insert, empty data set');
        
        $builder->insert(array());
    }
    
    function testInsertUnknownField() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class($repo, array('help' => 5)) extends \Plasma\Schemas\AbstractSchema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory5', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory5';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $builder = new \Plasma\Schemas\SQLDirectory(\get_class($schema), (new \Plasma\SQL\Grammar\MySQL()));
        $builder->setRepository($repo);
        
        $this->expectException(\Plasma\Exception::class);
        $this->expectExceptionMessage('Unknown field "helpMe"');
        
        $builder->insert(array('helpMe' => 50));
    }
    
    function testInsertAll() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
    
        $schema = (new class() extends \Plasma\Schemas\AbstractSchema {
            public $help;
            public $help2;
            public $help3;
            
            // Let Directory::insert create the mapper
            function __construct() {
                if(\func_num_args() > 0) {
                    parent::__construct(...\func_get_args());
                }
            }
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory101', 'help', 'BIGINT', '', 20, 0, null)),
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory101', 'help2', 'BIGINT', '', 20, 0, null)),
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory101', 'help3', 'BIGINT', '', 20, 0, null)),
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory101';
            }
            
            static function getIdentifierColumn(): ?string {
                return null;
            }
        });
        
        $query = 'INSERT INTO `test_Directory101` (`help2`) VALUES (?)';
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
    
        $builder = new \Plasma\Schemas\SQLDirectory($name, (new \Plasma\SQL\Grammar\MySQL()));
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
        
        $schema = (new class($repo, array('help' => 5)) extends \Plasma\Schemas\AbstractSchema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory2112', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory2112';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $query = 'UPDATE `test_Directory2112` SET `help` = ? WHERE `help` = ?';
        
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
        
        $builder = new \Plasma\Schemas\SQLDirectory(\get_class($schema), (new \Plasma\SQL\Grammar\MySQL()));
        $builder->setRepository($repo);
        
        $promise = $builder->update(array('help' => 5), 'help', 50);
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
    }
    
    function testBuildSchemas() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class($repo, array('help' => 5)) extends \Plasma\Schemas\AbstractSchema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory7', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory7';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $builder = new \Plasma\Schemas\SQLDirectory(\get_class($schema), (new \Plasma\SQL\Grammar\MySQL()));
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
    
    function testPreloads() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class($repo, array('help' => 5, 'rescueID' => 51)) extends \Plasma\Schemas\AbstractSchema {
            public $help;
            public $rescueID;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory71_preloads', 'help', 'BIGINT', '', 20, 0, null)),
                    static::getColDefBuilder()
                        ->name('rescueID')
                        ->type('BIGINT')
                        ->length(20)
                        ->foreignKey('test_Directory71_preloads2', 'rescue')
                        ->foreignFetchMode(\Plasma\Schemas\PreloadInterface::FETCH_MODE_ALWAYS)
                        ->getDefinition()
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory71_preloads';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $builder = new \Plasma\Schemas\SQLDirectory(\get_class($schema), (new \Plasma\SQL\Grammar\MySQL()));
        $repo->registerDirectory($schema->getTableName(), $builder);
        
        $schema2 = (new class($repo, array('rescue' => 51)) extends \Plasma\Schemas\AbstractSchema {
            public $rescue;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory71_preloads_2', 'rescue', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory71_preloads2';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'rescue';
            }
        });
        
        $builder2 = new \Plasma\Schemas\SQLDirectory(\get_class($schema2), (new \Plasma\SQL\Grammar\MySQL()));
        $repo->registerDirectory($schema2->getTableName(), $builder2);
        
        $queryResult = new \Plasma\QueryResult(
            1,
            0,
            null,
            array(
                (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory71_preloads', 'help', 'BIGINT', null, 20, 0, null)),
                (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory71_preloads', 'rescueID', 'BIGINT', null, 20, 0, null)),
                (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory71_preloads2', 'rescue', 'BIGINT', null, 20, 0, null))
            ),
            array(
                array(
                    'help' => 5,
                    'rescueID' => 51,
                    'rescue' => 51
                )
            )
        );
        
        $client
            ->expects($this->once())
            ->method('execute')
            ->with(
                'SELECT * FROM `test_Directory71_preloads` AS t0 LEFT JOIN `test_Directory71_preloads2` AS t1 ON t0.rescueID = t1.rescue WHERE `help` = ?',
                array(5)
            )
            ->will($this->returnValue(\React\Promise\resolve($queryResult)));
        
        $promise = $builder->fetch(5);
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
        
        $value = $this->await($promise);
        $this->assertInstanceOf(\Plasma\Schemas\SchemaCollection::class, $value);
        
        $result = $value->getSchemas()[0];
        $this->assertInstanceOf(\get_class($schema), $result);
        $this->assertInstanceOf(\get_class($schema2), $result->rescueID);
        
        $this->assertSame(5, $result->help);
        $this->assertSame(51, $result->rescueID->rescue);
    }
    
    function testPreloadsWithNull() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class($repo, array('help' => 5, 'rescueID' => 51)) extends \Plasma\Schemas\AbstractSchema {
            public $help;
            public $rescueID;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory71_preloads', 'help', 'BIGINT', '', 20, 0, null)),
                    static::getColDefBuilder()
                        ->name('rescueID')
                        ->type('BIGINT')
                        ->length(20)
                        ->foreignKey('test_Directory71_preloads2', 'rescue')
                        ->foreignFetchMode(\Plasma\Schemas\PreloadInterface::FETCH_MODE_ALWAYS)
                        ->getDefinition()
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory71_preloads';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $builder = new \Plasma\Schemas\SQLDirectory(\get_class($schema), (new \Plasma\SQL\Grammar\MySQL()));
        $repo->registerDirectory($schema->getTableName(), $builder);
        
        $schema2 = (new class($repo, array('rescue' => 51)) extends \Plasma\Schemas\AbstractSchema {
            public $rescue;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory71_preloads2', 'rescue', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory71_preloads2';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'rescue';
            }
        });
        
        $builder2 = new \Plasma\Schemas\SQLDirectory(\get_class($schema2), (new \Plasma\SQL\Grammar\MySQL()));
        $repo->registerDirectory($schema2->getTableName(), $builder2);
        
        $queryResult = new \Plasma\QueryResult(
            1,
            0,
            null,
            array(
                (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory71_preloads', 'help', 'BIGINT', null, 20, 0, null)),
                (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory71_preloads', 'rescueID', 'BIGINT', null, 20, 0, null)),
                (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory71_preloads2', 'rescue', 'BIGINT', null, 20, 0, null))
            ),
            array(
                array(
                    'help' => 5,
                    'rescueID' => null,
                    'rescue' => null
                )
            )
        );
        
        $client
            ->expects($this->once())
            ->method('execute')
            ->with(
                'SELECT * FROM `test_Directory71_preloads` AS t0 LEFT JOIN `test_Directory71_preloads2` AS t1 ON t0.rescueID = t1.rescue WHERE `help` = ?',
                array(5)
            )
            ->will($this->returnValue(\React\Promise\resolve($queryResult)));
        
        $promise = $builder->fetch(5);
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
        
        $value = $this->await($promise);
        $this->assertInstanceOf(\Plasma\Schemas\SchemaCollection::class, $value);
        
        $result = $value->getSchemas()[0];
        $this->assertInstanceOf(\get_class($schema), $result);
        
        $this->assertNull($result->rescueID);
        $this->assertSame(5, $result->help);
    }
    
    function testResolveForeignTargets() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class($repo, array('help' => 5, 'rescueID' => 51)) extends \Plasma\Schemas\AbstractSchema {
            public $help;
            public $rescueID;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory72_preloads', 'help', 'BIGINT', '', 20, 0, null)),
                    static::getColDefBuilder()
                        ->name('rescueID')
                        ->type('BIGINT')
                        ->length(20)
                        ->foreignKey('test_Directory72_preloads2', 'rescue')
                        ->foreignFetchMode(\Plasma\Schemas\PreloadInterface::FETCH_MODE_LAZY)
                        ->getDefinition()
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory72_preloads';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $builder = new \Plasma\Schemas\SQLDirectory(\get_class($schema), (new \Plasma\SQL\Grammar\MySQL()));
        $repo->registerDirectory($schema->getTableName(), $builder);
        
        $schema2 = (new class($repo, array('rescue' => 51)) extends \Plasma\Schemas\AbstractSchema {
            public $rescue;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory72_preloads2', 'rescue', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory72_preloads2';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'rescue';
            }
        });
        
        $builder2 = new \Plasma\Schemas\SQLDirectory(\get_class($schema2), (new \Plasma\SQL\Grammar\MySQL()));
        $repo->registerDirectory($schema2->getTableName(), $builder2);
        
        $queryResult = new \Plasma\QueryResult(
            1,
            0,
            null,
            array(
                (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory72_preloads', 'help', 'BIGINT', null, 20, 0, null)),
                (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory72_preloads', 'rescueID', 'BIGINT', null, 20, 0, null))
            ),
            array(
                array(
                    'help' => 5,
                    'rescueID' => 51
                )
            )
        );
        
        $client
            ->expects($this->at(0))
            ->method('execute')
            ->with(
                'SELECT * FROM `test_Directory72_preloads` AS t0 WHERE `help` = ?',
                array(5)
            )
            ->will($this->returnValue(\React\Promise\resolve($queryResult)));
    
        $queryResult2 = new \Plasma\QueryResult(
            1,
            0,
            null,
            array(
                (new \Plasma\Schemas\Tests\ColumnDefinition('test_Directory72_preloads2', 'rescue', 'BIGINT', null, 20, 0, null))
            ),
            array(
                array(
                    'rescue' => 51
                )
            )
        );
    
        $client
            ->expects($this->at(1))
            ->method('execute')
            ->with(
                'SELECT * FROM `test_Directory72_preloads2` AS t0 WHERE `rescue` = ?',
                array(51)
            )
            ->will($this->returnValue(\React\Promise\resolve($queryResult2)));
        
        $promise = $builder->fetch(5);
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
        
        $value = $this->await($promise);
        $this->assertInstanceOf(\Plasma\Schemas\SchemaCollection::class, $value);
        
        $result = $value->getSchemas()[0];
        $this->assertInstanceOf(\get_class($schema), $result);
        
        /** @var \Plasma\Schemas\SchemaInterface  $result */
        
        $this->assertSame(51, $result->rescueID);
        $this->assertSame(5, $result->help);
        
        $promise2 = $result->resolveForeignTargets();
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise2);
        
        $value2 = $this->await($promise2);
        $this->assertInstanceOf(\get_class($result), $value2);
        
        $this->assertInstanceOf(\get_class($schema2), $value2->rescueID);
        $this->assertSame(51, $value2->rescueID->rescue);
    }
}
