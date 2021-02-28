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

use Plasma\Exception;
use Plasma\QueryResult;
use Plasma\Schemas\AbstractSchema;
use Plasma\Schemas\PreloadInterface;
use Plasma\Schemas\Repository;
use Plasma\Schemas\SchemaCollection;
use Plasma\Schemas\SchemaInterface;
use Plasma\Schemas\SQLDirectory;
use Plasma\SQL\Grammar\MySQL;
use Plasma\StatementInterface;
use Plasma\TransactionInterface;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use function React\Promise\resolve;

class SQLDirectoryTest extends TestCase {
    function testFetchAll() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $schema = (new class($repo, array('help' => 5)) extends AbstractSchema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new ColumnDefinition('test_Directory_fetchall', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory_fetchall';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $query = 'SELECT * FROM `test_Directory_fetchall`';
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::any())
            ->method('quote')
            ->willReturnCallback(
                function ($a) {
                    return '`'.$a.'`';
                }
            );
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::once())
            ->method('execute')
            ->with($query, array())
            ->willReturn((new Promise(
                static function () {
                }
            )));
        
        $builder = new SQLDirectory(\get_class($schema), (new MySQL()));
        $builder->setRepository($repo);
        
        $promise = $builder->fetchAll();
        self::assertInstanceOf(PromiseInterface::class, $promise);
    }
    
    function testFetch() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $schema = (new class($repo, array('help' => 5)) extends AbstractSchema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new ColumnDefinition('test_Directory2', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory2';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $query = 'SELECT * FROM `test_Directory2` WHERE `help` = ?';
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::any())
            ->method('quote')
            ->willReturnCallback(function ($a) {
                return '`'.$a.'`';
            });
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::once())
            ->method('execute')
            ->with($query, array(5))
            ->willReturn((new Promise(static function () {})));
        
        $builder = new SQLDirectory(\get_class($schema), (new MySQL()));
        $builder->setRepository($repo);
        
        $promise = $builder->fetch(5);
        self::assertInstanceOf(PromiseInterface::class, $promise);
    }
    
    function testFetchNoUnique() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $schema = (new class($repo, array('help' => 5)) extends AbstractSchema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new ColumnDefinition('test_Directory8', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory8';
            }
            
            static function getIdentifierColumn(): ?string {
                return null;
            }
        });
        
        $builder = new SQLDirectory(\get_class($schema), (new MySQL()));
        $builder->setRepository($repo);
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('AbstractSchema has no unique or primary column');
        
        $builder->fetch(5);
    }
    
    function testInsert() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $schema = (new class() extends AbstractSchema {
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
                    (new ColumnDefinition('test_Directory3', 'help', 'BIGINT', '', 20, 0, null)),
                    (new ColumnDefinition('test_Directory3', 'help2', 'BIGINT', '', 20, 0, null))
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
        $result = new QueryResult(1, 0, 1, $schema::getDefinition(), null);
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::any())
            ->method('quote')
            ->willReturnCallback(function ($a) {
                return '`'.$a.'`';
            });
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::once())
            ->method('execute')
            ->with($query, array(5))
            ->willReturn(resolve($result));
        
        $name = \get_class($schema);
        
        $builder = new SQLDirectory($name, (new MySQL()));
        $builder->setRepository($repo);
        
        $promise = $builder->insert(array('help2' => 5));
        self::assertInstanceOf(PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        self::assertInstanceOf(SchemaCollection::class, $res);
        
        $expected = new $name($repo, array('help' => 1, 'help2' => 5));
        self::assertEquals($expected, $res->getSchemas()[0]);
    }
    
    function testInsertNotAllFieldsGiven() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $schema = (new class() extends AbstractSchema {
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
                    (new ColumnDefinition('test_Directory9', 'help', 'BIGINT', '', 20, 0, null)),
                    (new ColumnDefinition('test_Directory9', 'help2', 'BIGINT', '', 20, 0, null)),
                    (new ColumnDefinition('test_Directory9', 'help3', 'BIGINT', '', 20, 0, null)),
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
        $result = new QueryResult(1, 0, 1, null, null);
        
        $query2 = 'SELECT * FROM `test_Directory9` WHERE `help` = ?';
        $result2 = new QueryResult(0, 0, 0, $schema::getDefinition(), array(array('help' => 1, 'help2' => 5, 'help3' => 0)));
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::any())
            ->method('quote')
            ->willReturnCallback(function ($a) {
                return '`'.$a.'`';
            });
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::exactly(2))
            ->method('execute')
            ->withConsecutive(array($query, array(5)), array($query2, array(1)))
            ->willReturnOnConsecutiveCalls(self::returnValue(resolve($result)), self::returnValue(resolve($result2)));
        
        $name = \get_class($schema);
        
        $builder = new SQLDirectory($name, (new MySQL()));
        $repo->registerDirectory('test_Directory9', $builder);
        
        $promise = $builder->insert(array('help2' => 5));
        self::assertInstanceOf(PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        self::assertInstanceOf(SchemaCollection::class, $res);
        
        $expected = new $name($repo, array('help' => 1, 'help2' => 5, 'help3' => 0));
        self::assertEquals($expected, $res->getSchemas()[0]);
    }
    
    function testInsertNoUnique() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $schema = (new class() extends AbstractSchema {
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
                    (new ColumnDefinition('test_Directory10', 'help', 'BIGINT', '', 20, 0, null)),
                    (new ColumnDefinition('test_Directory10', 'help2', 'BIGINT', '', 20, 0, null)),
                    (new ColumnDefinition('test_Directory10', 'help3', 'BIGINT', '', 20, 0, null)),
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
        $result = new QueryResult(1, 0, 1, $schema::getDefinition(), null);
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::any())
            ->method('quote')
            ->willReturnCallback(function ($a) {
                return '`'.$a.'`';
            });
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::once())
            ->method('execute')
            ->with($query, array(5))
            ->willReturn(resolve($result));
        
        $name = \get_class($schema);
        
        $builder = new SQLDirectory($name, (new MySQL()));
        $builder->setRepository($repo);
        
        $promise = $builder->insert(array('help2' => 5));
        self::assertInstanceOf(PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        self::assertInstanceOf(SchemaCollection::class, $res);
    }
    
    function testInsertEmptySet() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $schema = (new class($repo, array('help' => 5)) extends AbstractSchema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new ColumnDefinition('test_Directory4', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory4';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $builder = new SQLDirectory(\get_class($schema), (new MySQL()));
        $builder->setRepository($repo);
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Nothing to insert, empty data set');
        
        $builder->insert(array());
    }
    
    function testInsertUnknownField() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $schema = (new class($repo, array('help' => 5)) extends AbstractSchema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new ColumnDefinition('test_Directory5', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory5';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $builder = new SQLDirectory(\get_class($schema), (new MySQL()));
        $builder->setRepository($repo);
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unknown field "helpMe"');
        
        $builder->insert(array('helpMe' => 50));
    }
    
    function testInsertAll() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
    
        $schema = (new class() extends AbstractSchema {
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
                    (new ColumnDefinition('test_Directory101', 'help', 'BIGINT', '', 20, 0, null)),
                    (new ColumnDefinition('test_Directory101', 'help2', 'BIGINT', '', 20, 0, null)),
                    (new ColumnDefinition('test_Directory101', 'help3', 'BIGINT', '', 20, 0, null)),
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
        $result = new QueryResult(1, 0, 1, $schema::getDefinition(), null);
        
        $transaction = $this->getMockBuilder(TransactionInterface::class)
            ->getMock();
        
        $statement = $this->getMockBuilder(StatementInterface::class)
            ->getMock();
        
        $transaction
            ->expects(self::once())
            ->method('prepare')
            ->with($query)
            ->willReturn(resolve($statement));
        
        $transaction
            ->expects(self::once())
            ->method('commit')
            ->willReturn(resolve());
        
        $statement
            ->expects(self::exactly(2))
            ->method('execute')
            ->willReturn(resolve($result));
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::once())
            ->method('beginTransaction')
            ->willReturn(resolve($transaction));
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::any())
            ->method('quote')
            ->willReturnCallback(function ($a) {
                return '`'.$a.'`';
            });
    
        $name = \get_class($schema);
    
        $builder = new SQLDirectory($name, (new MySQL()));
        $builder->setRepository($repo);
        
        $promise = $builder->insertAll(array(
            array('help2' => 5),
            array('help2' => 250)
         ));
        self::assertInstanceOf(PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        self::assertInstanceOf(SchemaCollection::class, $res);
        
        self::assertCount(2, $res->getSchemas());
        self::assertSame(5, $res->getSchemas()[0]->help2);
        self::assertSame(250, $res->getSchemas()[1]->help2);
    }
    
    function testUpdate() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $schema = (new class($repo, array('help' => 5)) extends AbstractSchema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new ColumnDefinition('test_Directory2112', 'help', 'BIGINT', '', 20, 0, null))
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
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::any())
            ->method('quote')
            ->willReturnCallback(function ($a) {
                return '`'.$a.'`';
            });
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::once())
            ->method('execute')
            ->with($query, array(5, 50))
            ->willReturn((new Promise(static function () {})));
        
        $builder = new SQLDirectory(\get_class($schema), (new MySQL()));
        $builder->setRepository($repo);
        
        $promise = $builder->update(array('help' => 5), 'help', 50);
        self::assertInstanceOf(PromiseInterface::class, $promise);
    }
    
    function testBuildSchemas() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $schema = (new class($repo, array('help' => 5)) extends AbstractSchema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new ColumnDefinition('test_Directory7', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory7';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $builder = new SQLDirectory(\get_class($schema), (new MySQL()));
        $builder->setRepository($repo);
        
        $rows = array(
            array('help' => 5),
            array('help' => 7)
        );
        
        $result = new QueryResult(0, 0, 0, $schema::getDefinition(), $rows);
        
        $collection = $builder->buildSchemas($result);
        self::assertInstanceOf(SchemaCollection::class, $collection);
        
        $expectedSchemas = array(
            $schema::build($repo, $rows[0]),
            $schema::build($repo, $rows[1])
        );
        
        self::assertEquals($expectedSchemas, $collection->getSchemas());
        self::assertSame($result, $collection->getResult());
    }
    
    function testPreloads() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $schema = (new class($repo, array('help' => 5, 'rescueID' => 51)) extends AbstractSchema {
            public $help;
            public $rescueID;
            
            static function getDefinition(): array {
                return array(
                    (new ColumnDefinition('test_Directory71_preloads', 'help', 'BIGINT', '', 20, 0, null)),
                    static::getColDefBuilder()
                        ->name('rescueID')
                        ->type('BIGINT')
                        ->length(20)
                        ->foreignKey('test_Directory71_preloads2', 'rescue')
                        ->foreignFetchMode(PreloadInterface::FETCH_MODE_ALWAYS)
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
        
        $builder = new SQLDirectory(\get_class($schema), (new MySQL()));
        $repo->registerDirectory($schema::getTableName(), $builder);
        
        $schema2 = (new class($repo, array('rescue' => 51)) extends AbstractSchema {
            public $rescue;
            
            static function getDefinition(): array {
                return array(
                    (new ColumnDefinition('test_Directory71_preloads_2', 'rescue', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory71_preloads2';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'rescue';
            }
        });
        
        $builder2 = new SQLDirectory(\get_class($schema2), (new MySQL()));
        $repo->registerDirectory($schema2::getTableName(), $builder2);
        
        $queryResult = new QueryResult(
            1,
            0,
            null,
            array(
                (new ColumnDefinition('test_Directory71_preloads', 'help', 'BIGINT', null, 20, 0, null)),
                (new ColumnDefinition('test_Directory71_preloads', 'rescueID', 'BIGINT', null, 20, 0, null)),
                (new ColumnDefinition('test_Directory71_preloads2', 'rescue', 'BIGINT', null, 20, 0, null))
            ),
            array(
                array(
                    'help' => 5,
                    'rescueID' => 51,
                    'rescue' => 51
                )
            )
        );
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::once())
            ->method('execute')
            ->with(
                'SELECT * FROM `test_Directory71_preloads` LEFT JOIN `test_Directory71_preloads2` ON test_Directory71_preloads.rescueID = test_Directory71_preloads2.rescue WHERE `help` = ?',
                array(5)
            )
            ->willReturn(resolve($queryResult));
        
        $promise = $builder->fetch(5);
        self::assertInstanceOf(PromiseInterface::class, $promise);
        
        $value = $this->await($promise);
        self::assertInstanceOf(SchemaCollection::class, $value);
        
        $result = $value->getSchemas()[0];
        self::assertInstanceOf(\get_class($schema), $result);
        self::assertInstanceOf(\get_class($schema2), $result->rescueID);
        
        self::assertSame(5, $result->help);
        self::assertSame(51, $result->rescueID->rescue);
    }
    
    function testPreloadsWithNull() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $schema = (new class($repo, array('help' => 5, 'rescueID' => 51)) extends AbstractSchema {
            public $help;
            public $rescueID;
            
            static function getDefinition(): array {
                return array(
                    (new ColumnDefinition('test_Directory71_preloads', 'help', 'BIGINT', '', 20, 0, null)),
                    static::getColDefBuilder()
                        ->name('rescueID')
                        ->type('BIGINT')
                        ->length(20)
                        ->foreignKey('test_Directory71_preloads2', 'rescue')
                        ->foreignFetchMode(PreloadInterface::FETCH_MODE_ALWAYS)
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
        
        $builder = new SQLDirectory(\get_class($schema), (new MySQL()));
        $repo->registerDirectory($schema::getTableName(), $builder);
        
        $schema2 = (new class($repo, array('rescue' => 51)) extends AbstractSchema {
            public $rescue;
            
            static function getDefinition(): array {
                return array(
                    (new ColumnDefinition('test_Directory71_preloads2', 'rescue', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory71_preloads2';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'rescue';
            }
        });
        
        $builder2 = new SQLDirectory(\get_class($schema2), (new MySQL()));
        $repo->registerDirectory($schema2::getTableName(), $builder2);
        
        $queryResult = new QueryResult(
            1,
            0,
            null,
            array(
                (new ColumnDefinition('test_Directory71_preloads', 'help', 'BIGINT', null, 20, 0, null)),
                (new ColumnDefinition('test_Directory71_preloads', 'rescueID', 'BIGINT', null, 20, 0, null)),
                (new ColumnDefinition('test_Directory71_preloads2', 'rescue', 'BIGINT', null, 20, 0, null))
            ),
            array(
                array(
                    'help' => 5,
                    'rescueID' => null,
                    'rescue' => null
                )
            )
        );
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::once())
            ->method('execute')
            ->with(
                'SELECT * FROM `test_Directory71_preloads` LEFT JOIN `test_Directory71_preloads2` ON test_Directory71_preloads.rescueID = test_Directory71_preloads2.rescue WHERE `help` = ?',
                array(5)
            )
            ->willReturn(resolve($queryResult));
        
        $promise = $builder->fetch(5);
        self::assertInstanceOf(PromiseInterface::class, $promise);
        
        $value = $this->await($promise);
        self::assertInstanceOf(SchemaCollection::class, $value);
        
        $result = $value->getSchemas()[0];
        self::assertInstanceOf(\get_class($schema), $result);
        
        self::assertNull($result->rescueID);
        self::assertSame(5, $result->help);
    }
    
    function testResolveForeignTargets() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $schema = (new class($repo, array('help' => 5, 'rescueID' => 51)) extends AbstractSchema {
            public $help;
            public $rescueID;
            
            static function getDefinition(): array {
                return array(
                    (new ColumnDefinition('test_Directory72_preloads', 'help', 'BIGINT', '', 20, 0, null)),
                    static::getColDefBuilder()
                        ->name('rescueID')
                        ->type('BIGINT')
                        ->length(20)
                        ->foreignKey('test_Directory72_preloads2', 'rescue')
                        ->foreignFetchMode(PreloadInterface::FETCH_MODE_LAZY)
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
        
        $builder = new SQLDirectory(\get_class($schema), (new MySQL()));
        $repo->registerDirectory($schema::getTableName(), $builder);
        
        $schema2 = (new class($repo, array('rescue' => 51)) extends AbstractSchema {
            public $rescue;
            
            static function getDefinition(): array {
                return array(
                    (new ColumnDefinition('test_Directory72_preloads2', 'rescue', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_Directory72_preloads2';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'rescue';
            }
        });
        
        $builder2 = new SQLDirectory(\get_class($schema2), (new MySQL()));
        $repo->registerDirectory($schema2::getTableName(), $builder2);
        
        $queryResult = new QueryResult(
            1,
            0,
            null,
            array(
                (new ColumnDefinition('test_Directory72_preloads', 'help', 'BIGINT', null, 20, 0, null)),
                (new ColumnDefinition('test_Directory72_preloads', 'rescueID', 'BIGINT', null, 20, 0, null))
            ),
            array(
                array(
                    'help' => 5,
                    'rescueID' => 51
                )
            )
        );
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::at(0))
            ->method('execute')
            ->with(
                'SELECT * FROM `test_Directory72_preloads` WHERE `help` = ?',
                array(5)
            )
            ->willReturn(resolve($queryResult));
    
        $queryResult2 = new QueryResult(
            1,
            0,
            null,
            array(
                (new ColumnDefinition('test_Directory72_preloads2', 'rescue', 'BIGINT', null, 20, 0, null))
            ),
            array(
                array(
                    'rescue' => 51
                )
            )
        );
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::at(1))
            ->method('execute')
            ->with(
                'SELECT * FROM `test_Directory72_preloads2` WHERE `rescue` = ?',
                array(51)
            )
            ->willReturn(resolve($queryResult2));
        
        $promise = $builder->fetch(5);
        self::assertInstanceOf(PromiseInterface::class, $promise);
        
        $value = $this->await($promise);
        self::assertInstanceOf(SchemaCollection::class, $value);
        
        $result = $value->getSchemas()[0];
        self::assertInstanceOf(\get_class($schema), $result);
        
        /** @var SchemaInterface  $result */
        
        /** @noinspection PhpUndefinedFieldInspection */
        self::assertSame(51, $result->rescueID);
        self::assertSame(5, $result->help);
        
        $promise2 = $result->resolveForeignTargets();
        self::assertInstanceOf(PromiseInterface::class, $promise2);
        
        $value2 = $this->await($promise2);
        self::assertInstanceOf(\get_class($result), $value2);
        
        self::assertInstanceOf(\get_class($schema2), $value2->rescueID);
        self::assertSame(51, $value2->rescueID->rescue);
    }
}
