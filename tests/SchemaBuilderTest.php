<?php
/**
 * Plasma Schemas component
 * Copyright 2018-2019 PlasmaPHP, All Rights Reserved
 *
 * Website: https://github.com/PlasmaPHP
 * License: https://github.com/PlasmaPHP/schemas/blob/master/LICENSE
*/

namespace Plasma\Schemas\Tests;

class SchemaBuilderTest extends TestCase {
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
        
        $builder = new \Plasma\Schemas\SchemaBuilder(\get_class($schema));
        $builder->setRepository($repo);
        
        $this->assertSame($repo, $builder->getRepository());
    }
    
    function testConstructorUnknownClass() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $this->expectException(\Plasma\Exception::class);
        $this->expectExceptionMessage('Schema class does not exist');
        
        $builder = new \Plasma\Schemas\SchemaBuilder('a');
    }
    
    function testConstructorInvalidClass() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $this->expectException(\Plasma\Exception::class);
        $this->expectExceptionMessage('Schema class does not implement Schema Interface');
        
        $builder = new \Plasma\Schemas\SchemaBuilder(\stdClass::class);
    }
    
    function testFetch() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class($repo, array('help' => 5)) extends \Plasma\Schemas\Schema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\ColumnDefinition('test', 'test_schemabuilder2', 'help', 'BIGINT', '', 20, false, 0, null))
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
        
        $builder = new \Plasma\Schemas\SchemaBuilder(\get_class($schema));
        $builder->setRepository($repo);
        
        $promise = $builder->fetch(5);
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
    }
    
    function testInsert() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class() extends \Plasma\Schemas\Schema {
            public $help;
            
            // Let Schemabuilder::insert create the mapper
            function __construct() {
                if(\func_num_args() > 0) {
                    parent::__construct(...\func_get_args());
                }
            }
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\ColumnDefinition('test', 'test_schemabuilder3', 'help', 'BIGINT', '', 20, false, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_schemabuilder3';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $query = 'INSERT INTO `test_schemabuilder3` (`help`) VALUES (?)';
        
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
        
        $builder = new \Plasma\Schemas\SchemaBuilder(\get_class($schema));
        $builder->setRepository($repo);
        
        $promise = $builder->insert(array('help' => 5));
        $this->assertInstanceOf(\React\Promise\PromiseInterface::class, $promise);
    }
    
    function testInsertEmptySet() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class($repo, array('help' => 5)) extends \Plasma\Schemas\Schema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\ColumnDefinition('test', 'test_schemabuilder4', 'help', 'BIGINT', '', 20, false, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_schemabuilder4';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $builder = new \Plasma\Schemas\SchemaBuilder(\get_class($schema));
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
                    (new \Plasma\ColumnDefinition('test', 'test_schemabuilder5', 'help', 'BIGINT', '', 20, false, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_schemabuilder5';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $builder = new \Plasma\Schemas\SchemaBuilder(\get_class($schema));
        $builder->setRepository($repo);
        
        $this->expectException(\Plasma\Exception::class);
        $this->expectExceptionMessage('Unknown field "helpMe"');
        
        $builder->insert(array('helpMe' => 50));
    }
    
    function testBuildSchemas() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $schema = (new class($repo, array('help' => 5)) extends \Plasma\Schemas\Schema {
            public $help;
            
            static function getDefinition(): array {
                return array(
                    (new \Plasma\ColumnDefinition('test', 'test_schemabuilder7', 'help', 'BIGINT', '', 20, false, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test_schemabuilder7';
            }
            
            static function getIdentifierColumn(): ?string {
                return 'help';
            }
        });
        
        $builder = new \Plasma\Schemas\SchemaBuilder(\get_class($schema));
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
