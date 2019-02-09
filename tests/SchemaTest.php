<?php
/**
 * Plasma Schemas component
 * Copyright 2018-2019 PlasmaPHP, All Rights Reserved
 *
 * Website: https://github.com/PlasmaPHP
 * License: https://github.com/PlasmaPHP/schemas/blob/master/LICENSE
*/

namespace Plasma\Schemas\Tests;

class SchemaTest extends TestCase {
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
        
        $builder = $this->getMockBuilder(\Plasma\Schemas\SQLSchemaBuilder::class)
            ->setConstructorArgs(array($name))
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
        
        $repo->registerSchemaBuilder('test5', $builder);
        
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
        
        $builder = $this->getMockBuilder(\Plasma\Schemas\SQLSchemaBuilder::class)
            ->setConstructorArgs(array($name))
            ->getMock();
        
        $result = new \Plasma\QueryResult(1, 0, null, null, null);
        
        $builder
            ->expects($this->once())
            ->method('insert')
            ->with(array(
                'help' => 5
            ))
            ->will($this->returnValue(\React\Promise\resolve($result)));
        
        $repo->registerSchemaBuilder('test5', $builder);
        
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
        
        $schema = $mock->build($repo, array(
            'help' => 50
        ));
        
        $query = 'UPDATE `test5` SET `help` = ? WHERE `help` = ?';
        $result = new \Plasma\QueryResult(1, 0, null, null, null);
        
        $client
            ->expects($this->any())
            ->method('quote')
            ->will($this->returnCallback(function ($a) {
                return '`'.$a.'`';
            }));
        
        $client
            ->expects($this->once())
            ->method('execute')
            ->with($query, array(10, 50))
            ->will($this->returnValue(\React\Promise\resolve($result)));
        
        $promise = $schema->update(array('help' => 10));
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        $this->assertInstanceOf(\Plasma\QueryResultInterface::class, $res);
        
        $this->assertSame($result, $res);
    }
    
    function testUpdateNoUnique() {
        $schema = (new class() extends \Plasma\Schemas\Schema {
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
                    (new \Plasma\ColumnDefinition('test', 'test3', 'help', 'BIGINT', '', 20, false, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test3';
            }
            
            static function getIdentifierColumn(): ?string {
                return null;
            }
        });
        
        $this->expectException(\Plasma\Exception::class);
        $this->expectExceptionMessage('Schema has no unique or primary column');
        
        $schema->update(array('help' => 10));
    }
    
    function testDelete() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        $mock = $this->getSchema();
        
        $schema = $mock->build($repo, array(
            'help' => 50
        ));
        
        $query = 'DELETE FROM `test5` WHERE `help` = ?';
        $result = new \Plasma\QueryResult(1, 0, null, null, null);
        
        $client
            ->expects($this->any())
            ->method('quote')
            ->will($this->returnCallback(function ($a) {
                return '`'.$a.'`';
            }));
        
        $client
            ->expects($this->once())
            ->method('execute')
            ->with($query, array(50))
            ->will($this->returnValue(\React\Promise\resolve($result)));
        
        $promise = $schema->delete();
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
        
        $res = $this->await($promise);
        $this->assertInstanceOf(\Plasma\QueryResultInterface::class, $res);
        
        $this->assertSame($result, $res);
    }
    
    function testDeleteNoUnique() {
        $schema = (new class() extends \Plasma\Schemas\Schema {
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
                    (new \Plasma\ColumnDefinition('test', 'test4', 'help', 'BIGINT', '', 20, false, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test4';
            }
            
            static function getIdentifierColumn(): ?string {
                return null;
            }
        });
        
        $this->expectException(\Plasma\Exception::class);
        $this->expectExceptionMessage('Schema has no unique or primary column');
        
        $schema->delete();
    }
    
    function testSnakeToCamelCase() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class($repo, array('help_me' => \PHP_INT_MAX)) extends \Plasma\Schemas\Schema {
            public $helpMe;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\ColumnDefinition('test', static::getTableName(), 'help_me', 'BIGINT', '', 20, false, 0, null))
                );
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
        
        $schema = (new class($repo, array('help_me' => 50)) extends \Plasma\Schemas\Schema {
            static function getDefinition(): array {
                return array(
                    (new \Plasma\ColumnDefinition('test', 'test7', 'help_me', 'BIGINT', '', 20, false, 0, null))
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
    
    function testConstructorUnknownField() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $this->expectException(\Plasma\Exception::class);
        $this->expectExceptionMessage('Unknown column "help"');
        
        $schema = (new class($repo, array('help' => 50)) extends \Plasma\Schemas\Schema {
            static function getDefinition(): array {
                return array(
                    (new \Plasma\ColumnDefinition('test', 'test8', 'help_me', 'BIGINT', '', 20, false, 0, null))
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
        $repo = new \Plasma\Schemas\Repository($client);
        
        $this->expectException(\Plasma\Exception::class);
        $this->expectExceptionMessage('Field "help" for identifier column does not exist');
        
        $schema = (new class($repo, array('help_me' => 50)) extends \Plasma\Schemas\Schema {
            protected $helpMe;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\ColumnDefinition('test', 'test6', 'help_me', 'BIGINT', '', 20, false, 0, null))
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
    
    function getSchema(...$args): \Plasma\Schemas\Schema {
        return (new class(...$args) extends \Plasma\Schemas\Schema {
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
                    (new \Plasma\ColumnDefinition('test', 'test5', 'help', 'BIGINT', '', 20, false, 0, null))
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
