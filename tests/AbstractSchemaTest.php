<?php
/**
 * Plasma Schemas component
 * Copyright 2018-2019 PlasmaPHP, All Rights Reserved
 *
 * Website: https://github.com/PlasmaPHP
 * License: https://github.com/PlasmaPHP/schemas/blob/master/LICENSE
*/

namespace Plasma\Schemas\Tests;

class AbstractSchemaTest extends TestCase {
    function testBuild() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        $mock = $this->getSchema();
        
        $name = \get_class($mock);
        $expected = $name::custom($repo, 50);
        
        $actual = $mock->build($repo, array(
            'help' => 50
        ));
        
        $this->assertEquals($expected, $actual);
    }
    
    function testInsert() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $mock = $this->getSchema();
        $name = \get_class($mock);
        
        $builder = $this->getMockBuilder(\Plasma\Schemas\SQLDirectory::class)
            ->setConstructorArgs(array($name, (new \Plasma\SQL\Grammar\MySQL())))
            ->getMock();
        
        $result = new \Plasma\QueryResult(1, 0, null, null, null);
        $result = new \Plasma\Schemas\SchemaCollection(array((new $name($repo, array('help' => 0)))), $result);
        
        $builder
            ->expects($this->once())
            ->method('insert')
            ->with(array(
                'help' => 5
            ))
            ->will($this->returnValue(\React\Promise\resolve($result)));
        
        $repo->registerDirectory('test5', $builder);
        
        $schema = new $name($repo, array('help' => 5));
        
        $insert = $schema->insert();
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $insert);
        
        $res = $this->await($insert);
        $this->assertSame($schema, $res);
    }
    
    function testInsertQueryResult() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $mock = $this->getSchema();
        $name = \get_class($mock);
        
        $builder = $this->getMockBuilder(\Plasma\Schemas\SQLDirectory::class)
            ->setConstructorArgs(array($name, (new \Plasma\SQL\Grammar\MySQL())))
            ->getMock();
        
        $result = new \Plasma\QueryResult(1, 0, null, null, null);
        
        $builder
            ->expects($this->once())
            ->method('insert')
            ->with(array(
                'help' => 5
            ))
            ->will($this->returnValue(\React\Promise\resolve($result)));
        
        $repo->registerDirectory('test5', $builder);
        
        $schema = new $name($repo, array('help' => 5));
        
        $insert = $schema->insert();
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $insert);
        
        $res = $this->await($insert);
        $this->assertSame($result, $res);
    }
    
    function testUpdate() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $mock = $this->getSchema();
        $name = \get_class($mock);
        
        $builder = $this->getMockBuilder(\Plasma\Schemas\SQLDirectory::class)
                        ->setConstructorArgs(array($name, (new \Plasma\SQL\Grammar\MySQL())))
                        ->getMock();
        
        $schema = $mock->build($repo, array(
            'help' => 50
        ));
        
        $result = new \Plasma\QueryResult(1, 0, null, null, null);
        
        $builder
            ->expects($this->once())
            ->method('update')
            ->with(array(
               'help' => 10
            ), 'help', 50)
            ->will($this->returnValue(\React\Promise\resolve($result)));
        
        $repo->registerDirectory('test5', $builder);
        
        $client
            ->expects($this->any())
            ->method('quote')
            ->will($this->returnCallback(function ($a) {
                return '`'.$a.'`';
            }));
        
        $promise = $schema->update(array('help' => 10));
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        $this->assertInstanceOf(\Plasma\QueryResultInterface::class, $res);
        
        $this->assertSame($result, $res);
    }
    
    function testUpdateNoUnique() {
        $schema = (new class() extends \Plasma\Schemas\AbstractSchema {
            public $help;
            
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
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test3', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
    
            static function getDatabaseName(): string {
                return \bin2hex(\random_bytes(5));
            }
            
            static function getTableName(): string {
                return 'test3';
            }
            
            static function getIdentifierColumn(): ?string {
                return null;
            }
        });
        
        $this->expectException(\Plasma\Exception::class);
        $this->expectExceptionMessage('AbstractSchema has no unique or primary column');
        
        $schema->update(array('help' => 10));
    }
    
    function testDelete() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $mock = $this->getSchema();
        $name = \get_class($mock);
    
        $builder = $this->getMockBuilder(\Plasma\Schemas\SQLDirectory::class)
            ->setConstructorArgs(array($name, (new \Plasma\SQL\Grammar\MySQL())))
            ->getMock();
    
        $result = new \Plasma\QueryResult(1, 0, null, null, null);
    
        $builder
            ->expects($this->once())
            ->method('deleteBy')
            ->with('help', 50)
            ->will($this->returnValue(\React\Promise\resolve($result)));
    
        $repo->registerDirectory('test5', $builder);
        
        $schema = $mock->build($repo, array(
            'help' => 50
        ));
        
        $client
            ->expects($this->any())
            ->method('quote')
            ->will($this->returnCallback(function ($a) {
                return '`'.$a.'`';
            }));
        
        $promise = $schema->delete();
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        $this->assertInstanceOf(\Plasma\QueryResultInterface::class, $res);
        
        $this->assertSame($result, $res);
    }
    
    function testDeleteNoUnique() {
        $schema = (new class() extends \Plasma\Schemas\AbstractSchema {
            public $help;
            
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
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test4', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
    
            static function getDatabaseName(): string {
                return \bin2hex(\random_bytes(5));
            }
            
            static function getTableName(): string {
                return 'test4';
            }
            
            static function getIdentifierColumn(): ?string {
                return null;
            }
        });
        
        $this->expectException(\Plasma\Exception::class);
        $this->expectExceptionMessage('AbstractSchema has no unique or primary column');
        
        $schema->delete();
    }
    
    function testSnakeToCamelCase() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class($repo, array('help_me' => \PHP_INT_MAX)) extends \Plasma\Schemas\AbstractSchema {
            public $helpMe;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', static::getTableName(), 'help_me', 'BIGINT', '', 20, 0, null))
                );
            }
    
            static function getDatabaseName(): string {
                return \bin2hex(\random_bytes(5));
            }
            
            static function getTableName(): string {
                return \bin2hex(\random_bytes(5));
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help_me';
            }
        });
        
        $this->assertSame(\PHP_INT_MAX, $schema->helpMe);
    }
    
    function testConstructorMissingProperty() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $this->expectException(\Plasma\Exception::class);
        $this->expectExceptionMessage('Property "helpMe" for column "help_me" does not exist');
        
        $schema = (new class($repo, array('help_me' => 50)) extends \Plasma\Schemas\AbstractSchema {
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test7', 'help_me', 'BIGINT', '', 20, 0, null))
                );
            }
    
            static function getDatabaseName(): string {
                return \bin2hex(\random_bytes(5));
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
        $repo = new \Plasma\Schemas\Repository($client);
        
        $this->expectException(\Plasma\Exception::class);
        $this->expectExceptionMessage('Field "help" for identifier column does not exist');
        
        $schema = (new class($repo, array('help_me' => 50)) extends \Plasma\Schemas\AbstractSchema {
            protected $helpMe;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test6', 'help_me', 'BIGINT', '', 20, 0, null))
                );
            }
    
            static function getDatabaseName(): string {
                return \bin2hex(\random_bytes(5));
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
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class($repo, array('help_me' => \PHP_INT_MAX)) extends \Plasma\Schemas\AbstractSchema {
            public $helpMe;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', static::getTableName(), 'help_me', 'BIGINT', '', 20, 0, null))
                );
            }
    
            static function getDatabaseName(): string {
                return \bin2hex(\random_bytes(5));
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
        
        $this->assertSame(array('helpMe' => \PHP_INT_MAX), $schema->toArray());
    }
    
    function getSchema(...$args): \Plasma\Schemas\AbstractSchema {
        return (new class(...$args) extends \Plasma\Schemas\AbstractSchema {
            public $help;
            
            function __construct() {
                if(\func_num_args() > 0) {
                    parent::__construct(...\func_get_args());
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
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test5', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
    
            static function getDatabaseName(): string {
                return \bin2hex(\random_bytes(5));
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
