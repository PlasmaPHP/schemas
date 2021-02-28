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

use Plasma\Schemas\AbstractSchema;
use Plasma\Schemas\ColumnDefinitionBuilder;
use Plasma\Schemas\PreloadInterface;

class ColumnDefinitionBuilderTest extends \PHPUnit\Framework\TestCase {
    function testCreateFromSchema() {
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
                    (new ColumnDefinition('test1_cbt', 'help', 'BIGINT', '', 20, 0, null))
                );
            }
            
            static function getTableName(): string {
                return 'test1_cbt';
            }
            
            static function getIdentifierColumn(): ?string {
                return null;
            }
        });
        
        $builder = ColumnDefinitionBuilder::createFromSchema($schema);
        self::assertInstanceOf(ColumnDefinitionBuilder::class, $builder);
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test1_cbt', '', '', '', null, 0, null,
            false, false, false, false, false, false, false,
            null, null, null
        );
        
        $coldef = $builder->getDefinition();
        self::assertEquals($expected, $coldef);
    }
    
    function testTableColumn() {
        $builder = new ColumnDefinitionBuilder();
    
        $builder
            ->table('test1_cbt')
            ->name('cb');
    
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test1_cbt', 'cb', '', '', null, 0, null,
            false, false, false, false, false, false, false,
            null, null, null
        );
    
        $coldef = $builder->getDefinition();
        self::assertEquals($expected, $coldef);
    }
    
    function testType() {
        $builder = $this->getCDBuilder();
    
        $builder
            ->type('VARCHAR');
    
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test1_cbt', 'cb', 'VARCHAR', '', null, 0, null,
            false, false, false, false, false, false, false,
            null, null, null
        );
    
        $coldef = $builder->getDefinition();
        self::assertEquals($expected, $coldef);
    }
    
    function testCharset() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->charset('utf8mb4');
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test1_cbt', 'cb', '', 'utf8mb4', null, 0, null,
            false, false, false, false, false, false, false,
            null, null, null
        );
        
        $coldef = $builder->getDefinition();
        self::assertEquals($expected, $coldef);
    }
    
    function testCharsetNull() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->charset(null);
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test1_cbt', 'cb', '', null, null, 0, null,
            false, false, false, false, false, false, false,
            null, null, null
        );
        
        $coldef = $builder->getDefinition();
        self::assertEquals($expected, $coldef);
    }
    
    function testLength() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->length(21);
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test1_cbt', 'cb', '', '', 21, 0, null,
            false, false, false, false, false, false, false,
            null, null, null
        );
        
        $coldef = $builder->getDefinition();
        self::assertEquals($expected, $coldef);
    }
    
    function testLengthNull() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->length(null);
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test1_cbt', 'cb', '', '', null, 0, null,
            false, false, false, false, false, false, false,
            null, null, null
        );
        
        $coldef = $builder->getDefinition();
        self::assertEquals($expected, $coldef);
    }
    
    function testFlags() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->flags(255);
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test1_cbt', 'cb', '', '', null, 255, null,
            false, false, false, false, false, false, false,
            null, null, null
        );
        
        $coldef = $builder->getDefinition();
        self::assertEquals($expected, $coldef);
    }
    
    function testDecimals() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->decimals(2);
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test1_cbt', 'cb', '', '', null, 0, 2,
            false, false, false, false, false, false, false,
            null, null, null
        );
        
        $coldef = $builder->getDefinition();
        self::assertEquals($expected, $coldef);
    }
    
    function testDecimalsNull() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->decimals(null);
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test1_cbt', 'cb', '', '', null, 0, null,
            false, false, false, false, false, false, false,
            null, null, null
        );
        
        $coldef = $builder->getDefinition();
        self::assertEquals($expected, $coldef);
    }
    
    function testNullable() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->nullable(true);
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test1_cbt', 'cb', '', '', null, 0, null,
            true, false, false, false, false, false, false,
            null, null, null
        );
        
        $coldef = $builder->getDefinition();
        self::assertEquals($expected, $coldef);
    }
    
    function testAutoIncrement() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->autoIncrement();
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test1_cbt', 'cb', '', '', null, 0, null,
            false, true, false, false, false, false, false,
            null, null, null
        );
        
        $coldef = $builder->getDefinition();
        self::assertEquals($expected, $coldef);
    }
    
    function testPrimary() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->primary();
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test1_cbt', 'cb', '', '', null, 0, null,
            false, false, true, false, false, false, false,
            null, null, null
        );
        
        $coldef = $builder->getDefinition();
        self::assertEquals($expected, $coldef);
    }
    
    function testUnique() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->unique();
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test1_cbt', 'cb', '', '', null, 0, null,
            false, false, false, true, false, false, false,
            null, null, null
        );
        
        $coldef = $builder->getDefinition();
        self::assertEquals($expected, $coldef);
    }
    
    function testComposite() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->composite();
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test1_cbt', 'cb', '', '', null, 0, null,
            false, false, false, false, true, false, false,
            null, null, null
        );
        
        $coldef = $builder->getDefinition();
        self::assertEquals($expected, $coldef);
    }
    
    function testUnsigned() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->unsigned();
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test1_cbt', 'cb', '', '', null, 0, null,
            false, false, false, false, false, true, false,
            null, null, null
        );
        
        $coldef = $builder->getDefinition();
        self::assertEquals($expected, $coldef);
    }
    
    function testZerofilled() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->zerofilled();
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test1_cbt', 'cb', '', '', null, 0, null,
            false, false, false, false, false, false, true,
            null, null, null
        );
        
        $coldef = $builder->getDefinition();
        self::assertEquals($expected, $coldef);
    }
    
    function testForeignTarget() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->foreignKey('hello', 'world');
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test1_cbt', 'cb', '', '', null, 0, null,
            false, false, false, false, false, false, false,
            'hello', 'world', PreloadInterface::FETCH_MODE_LAZY
        );
        
        $coldef = $builder->getDefinition();
        self::assertEquals($expected, $coldef);
    }
    
    function testForeignFetchMode() {
        $builder = $this->getCDBuilder();
        
        $builder
            ->foreignFetchMode(5);
        
        $expected = new \Plasma\Schemas\ColumnDefinition(
            'test1_cbt', 'cb', '', '', null, 0, null,
            false, false, false, false, false, false, false,
            null, null, 5
        );
        
        $coldef = $builder->getDefinition();
        self::assertEquals($expected, $coldef);
    }
    
    function getCDBuilder(): ColumnDefinitionBuilder {
        $builder = new ColumnDefinitionBuilder();
        
        $builder
            ->table('test1_cbt')
            ->name('cb');
        
        return $builder;
    }
}
