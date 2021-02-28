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
use Plasma\QueryResultInterface;
use Plasma\Schemas\AbstractSchema;
use Plasma\Schemas\Repository;
use Plasma\Schemas\SchemaCollection;
use Plasma\Schemas\SQLDirectory;
use Plasma\SQL\Grammar\MySQL;
use React\Promise\PromiseInterface;
use function React\Promise\resolve;

class AbstractSchemaTest extends TestCase {
    function testBuild() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        $mock = $this->getSchema();
        
        $name = \get_class($mock);
        /** @noinspection PhpUndefinedMethodInspection */
        $expected = $name::custom($repo, 50);
        
        $actual = $mock::build($repo, array(
            'help' => 50
        ));
        
        self::assertEquals($expected, $actual);
    }
    
    function testInsert() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $mock = $this->getSchema();
        $name = \get_class($mock);
        
        $builder = $this->getMockBuilder(SQLDirectory::class)
            ->setConstructorArgs(array($name, (new MySQL())))
            ->getMock();
        
        $result = new QueryResult(1, 0, null, null, null);
        $result = new SchemaCollection(array((new $name($repo, array('help' => 0)))), $result);
        
        $builder
            ->expects(self::once())
            ->method('insert')
            ->with(array(
                'help' => 5
            ))
            ->willReturn(resolve($result));
        
        $repo->registerDirectory('test5', $builder);
        
        $schema = new $name($repo, array('help' => 5));
        
        $insert = $schema->insert();
        self::assertInstanceOf(PromiseInterface::class, $insert);
        
        $res = $this->await($insert);
        self::assertSame($schema, $res);
    }
    
    function testInsertQueryResult() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $mock = $this->getSchema();
        $name = \get_class($mock);
        
        $builder = $this->getMockBuilder(SQLDirectory::class)
            ->setConstructorArgs(array($name, (new MySQL())))
            ->getMock();
        
        $result = new QueryResult(1, 0, null, null, null);
        
        $builder
            ->expects(self::once())
            ->method('insert')
            ->with(array(
                'help' => 5
            ))
            ->willReturn(resolve($result));
        
        $repo->registerDirectory('test5', $builder);
        
        $schema = new $name($repo, array('help' => 5));
        
        $insert = $schema->insert();
        self::assertInstanceOf(PromiseInterface::class, $insert);
        
        $res = $this->await($insert);
        self::assertSame($result, $res);
    }
    
    function testUpdate() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $mock = $this->getSchema();
        $name = \get_class($mock);
        
        $builder = $this->getMockBuilder(SQLDirectory::class)
            ->setConstructorArgs(array($name, (new MySQL())))
            ->getMock();
        
        $schema = $mock::build($repo, array(
            'help' => 50
        ));
        
        $result = new QueryResult(1, 0, null, null, null);
        
        $builder
            ->expects(self::once())
            ->method('update')
            ->with(array(
               'help' => 10
            ), 'help', 50)
            ->willReturn(resolve($result));
        
        $repo->registerDirectory('test5', $builder);
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::any())
            ->method('quote')
            ->willReturnCallback(
                function ($a) {
                    return '`'.$a.'`';
                }
            );
        
        $promise = $schema->update(array('help' => 10));
        self::assertInstanceOf(PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        self::assertInstanceOf(QueryResultInterface::class, $res);
        
        self::assertSame($result, $res);
    }
    
    function testUpdateNoUnique() {
        $schema = (new class() extends AbstractSchema {
            public $help;
            
            /** @noinspection PhpMissingParentConstructorInspection */
            function __construct() {
                
            }
            
            static function custom($repo, $value): self {
                $a = new static();
                $a->repo = $repo;
                $a->help = $value;
                
                return $a;
            }
            
            static function getDefinition(): array {
                return array(
                    (new ColumnDefinition('test3', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test3';
            }
            
            static function getIdentifierColumn(): ?string {
                return null;
            }
        });
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('AbstractSchema has no unique or primary column');
        
        $schema->update(array('help' => 10));
    }
    
    function testDelete() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $mock = $this->getSchema();
        $name = \get_class($mock);
        
        $builder = $this->getMockBuilder(SQLDirectory::class)
            ->setConstructorArgs(array($name, (new MySQL())))
            ->getMock();
        
        $result = new QueryResult(1, 0, null, null, null);
        
        $builder
            ->expects(self::once())
            ->method('deleteBy')
            ->with('help', 50)
            ->willReturn(resolve($result));
        
        $repo->registerDirectory('test5', $builder);
        
        $schema = $mock::build($repo, array(
            'help' => 50
        ));
        
        /** @noinspection PhpUndefinedMethodInspection */
        $client
            ->expects(self::any())
            ->method('quote')
            ->willReturnCallback(function ($a) {
                return '`'.$a.'`';
            });
        
        $promise = $schema->delete();
        self::assertInstanceOf(PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        self::assertInstanceOf(QueryResultInterface::class, $res);
        
        self::assertSame($result, $res);
    }
    
    function testDeleteNoUnique() {
        $schema = (new class() extends AbstractSchema {
            public $help;
            
            /** @noinspection PhpMissingParentConstructorInspection */
            function __construct() {
                
            }
            
            static function custom($repo, $value): self {
                $a = new static();
                $a->repo = $repo;
                $a->help = $value;
                
                return $a;
            }
            
            static function getDefinition(): array {
                return array(
                    (new ColumnDefinition('test4', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test4';
            }
            
            static function getIdentifierColumn(): ?string {
                return null;
            }
        });
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('AbstractSchema has no unique or primary column');
        
        $schema->delete();
    }
    
    function testSnakeToCamelCase() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $schema = (new class($repo, array('help_me' => \PHP_INT_MAX)) extends AbstractSchema {
            public $helpMe;
            
            static function getDefinition(): array {
                return array(
                    (new ColumnDefinition(static::getTableName(), 'help_me', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                static $name;
                
                if(!$name) {
                    $name = \bin2hex(\random_bytes(5));
                }
                
                return $name;
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help_me';
            }
        });
        
        self::assertSame(\PHP_INT_MAX, $schema->helpMe);
    }
    
    function testConstructorMissingProperty() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Property "helpMe" for column "help_me" does not exist');
        
        (new class($repo, array('help_me' => 50)) extends AbstractSchema {
            static function getDefinition(): array {
                return array(
                    (new ColumnDefinition('test7', 'help_me', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test7';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help_me';
            }
        });
    }
    
    function testConstructorMissingUniqueField() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Field "help" for identifier column does not exist');
        
        (new class($repo, array('help_me' => 50)) extends AbstractSchema {
            public $helpMe;
            
            static function getDefinition(): array {
                return array(
                    (new ColumnDefinition('test6', 'help_me', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test6';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
    }
    
    function testToArray() {
        $client = $this->getClientMock();
        $repo = new Repository($client);
        
        $schema = (new class($repo, array('help_me' => \PHP_INT_MAX)) extends AbstractSchema {
            public $helpMe;
            
            static function getDefinition(): array {
                return array(
                    (new ColumnDefinition(static::getTableName(), 'help_me', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                static $table;
                
                if(!$table) {
                    $table = \bin2hex(\random_bytes(5));
                }
                
                return $table;
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help_me';
            }
        });
        
        self::assertSame(array('helpMe' => \PHP_INT_MAX), $schema->toArray());
    }
    
    function getSchema(...$args): AbstractSchema {
        return (new class(...$args) extends AbstractSchema {
            public $help;
            
            function __construct(...$args) {
                if(\func_num_args() > 0) {
                    parent::__construct(...$args);
                }
            }
            
            static function custom($repo, $value): self {
                $a = new static();
                $a->repo = $repo;
                $a->help = $value;
                
                return $a;
            }
            
            static function getDefinition(): array {
                return array(
                    (new ColumnDefinition('test5', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test5';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
    }
}
