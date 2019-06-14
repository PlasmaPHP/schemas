<?php
/**
 * Plasma Schemas component
 * Copyright 2018-2019 PlasmaPHP, All Rights Reserved
 *
 * Website: https://github.com/PlasmaPHP
 * License: https://github.com/PlasmaPHP/schemas/blob/master/LICENSE
 */

namespace Plasma\Schemas\Tests;

class ColumnDefinitionBuilderTest extends \PHPUnit\Framework\TestCase {
    function testCreateWithSchema() {
        $schema = (new class() extends \Plasma\Schemas\Schema {
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
                    (new \Plasma\Schemas\Tests\ColumnDefinition('test', 'test1_cbt', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getDatabaseName(): string {
                return 'test';
            }
            
            static function getTableName(): string {
                return 'test1_cbt';
            }
            
            static function getIdentifierColumn(): ?string {
                return null;
            }
        });
        
        $builder = \Plasma\Schemas\ColumnDefinitionBuilder::createWithSchema($schema);
        $this->assertInstanceOf(\Plasma\Schemas\ColumnDefinitionBuilder::class, $builder);
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test', 'test1_cbt', '', '', '', null, 0, null,
            false, false, false, false, false, false, false
        );
        
        $coldef = $builder->getDefinition();
        $this->assertEquals($expected, $coldef);
    }
    
    function testDatabaseTableColumn() {
        $builder = new \Plasma\Schemas\ColumnDefinitionBuilder();
    
        $builder
            ->database('test')
            ->table('test1_cbt')
            ->name('cb');
    
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test', 'test1_cbt', 'cb', '', '', null, 0, null,
            false, false, false, false, false, false, false
        );
    
        $coldef = $builder->getDefinition();
        $this->assertEquals($expected, $coldef);
    }
    
    function testType() {
        $builder = $this->getCDBuilder();
    
        $builder
            ->type('VARCHAR');
    
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test', 'test1_cbt', 'cb', 'VARCHAR', '', null, 0, null,
            false, false, false, false, false, false, false
        );
    
        $coldef = $builder->getDefinition();
        $this->assertEquals($expected, $coldef);
    }
    
    function testCharset() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->charset('utf8mb4');
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test', 'test1_cbt', 'cb', '', 'utf8mb4', null, 0, null,
            false, false, false, false, false, false, false
        );
        
        $coldef = $builder->getDefinition();
        $this->assertEquals($expected, $coldef);
    }
    
    function testLength() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->length(21);
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test', 'test1_cbt', 'cb', '', '', 21, 0, null,
            false, false, false, false, false, false, false
        );
        
        $coldef = $builder->getDefinition();
        $this->assertEquals($expected, $coldef);
    }
    
    function testLengthNull() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->length(null);
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test', 'test1_cbt', 'cb', '', '', null, 0, null,
            false, false, false, false, false, false, false
        );
        
        $coldef = $builder->getDefinition();
        $this->assertEquals($expected, $coldef);
    }
    
    function testFlags() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->flags(255);
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test', 'test1_cbt', 'cb', '', '', null, 255, null,
            false, false, false, false, false, false, false
        );
        
        $coldef = $builder->getDefinition();
        $this->assertEquals($expected, $coldef);
    }
    
    function testDecimals() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->decimals(2);
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test', 'test1_cbt', 'cb', '', '', null, 0, 2,
            false, false, false, false, false, false, false
        );
        
        $coldef = $builder->getDefinition();
        $this->assertEquals($expected, $coldef);
    }
    
    function testDecimalsNull() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->decimals(null);
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test', 'test1_cbt', 'cb', '', '', null, 0, null,
            false, false, false, false, false, false, false
        );
        
        $coldef = $builder->getDefinition();
        $this->assertEquals($expected, $coldef);
    }
    
    function testNullable() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->nullable(true);
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test', 'test1_cbt', 'cb', '', '', null, 0, null,
            true, false, false, false, false, false, false
        );
        
        $coldef = $builder->getDefinition();
        $this->assertEquals($expected, $coldef);
    }
    
    function testAutoIncrement() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->autoIncrement();
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test', 'test1_cbt', 'cb', '', '', null, 0, null,
            false, true, false, false, false, false, false
        );
        
        $coldef = $builder->getDefinition();
        $this->assertEquals($expected, $coldef);
    }
    
    function testPrimary() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->primary();
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test', 'test1_cbt', 'cb', '', '', null, 0, null,
            false, false, true, false, false, false, false
        );
        
        $coldef = $builder->getDefinition();
        $this->assertEquals($expected, $coldef);
    }
    
    function testUnique() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->unique();
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test', 'test1_cbt', 'cb', '', '', null, 0, null,
            false, false, false, true, false, false, false
        );
        
        $coldef = $builder->getDefinition();
        $this->assertEquals($expected, $coldef);
    }
    
    function testComposite() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->composite();
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test', 'test1_cbt', 'cb', '', '', null, 0, null,
            false, false, false, false, true, false, false
        );
        
        $coldef = $builder->getDefinition();
        $this->assertEquals($expected, $coldef);
    }
    
    function testUnsigned() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->unsigned();
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test', 'test1_cbt', 'cb', '', '', null, 0, null,
            false, false, false, false, false, true, false
        );
        
        $coldef = $builder->getDefinition();
        $this->assertEquals($expected, $coldef);
    }
    
    function testZerofilled() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->zerofilled();
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test', 'test1_cbt', 'cb', '', '', null, 0, null,
            false, false, false, false, false, false, true
        );
        
        $coldef = $builder->getDefinition();
        $this->assertEquals($expected, $coldef);
    }
    
    function getCDBuilder(): \Plasma\Schemas\ColumnDefinitionBuilder {
        $builder = new \Plasma\Schemas\ColumnDefinitionBuilder();
        
        $builder
            ->database('test')
            ->table('test1_cbt')
            ->name('cb');
        
        return $builder;
    }
}
