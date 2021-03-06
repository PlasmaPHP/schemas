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

use Plasma\Schemas\PreloadInterface;

class ColumnDefinitionTest extends TestCase {
    function testGetTableName() {
        $coldef = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, false, false, false, null, null, null);
        self::assertSame('test2', $coldef->getTableName());
    }
    
    function testGetName() {
        $coldef = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, false, false, false, null, null, null);
        self::assertSame('coltest', $coldef->getName());
    }
    
    function testGetType() {
        $coldef = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, false, false, false, null, null, null);
        self::assertSame('BIGINT', $coldef->getType());
    }
    
    function testGetCharset() {
        $coldef = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, false, false, false, null, null, null);
        self::assertSame('utf8mb4', $coldef->getCharset());
    }
    
    function testGetLength() {
        $coldef = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, false, false, false, null, null, null);
        self::assertSame(20, $coldef->getLength());
        
        $coldef2 = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', null, 0, null, false, false, false, false, false, false, false, null, null, null);
        self::assertNull($coldef2->getLength());
    }
    
    function testGetFlags() {
        $coldef = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, false, false, false, null, null, null);
        self::assertSame(0, $coldef->getFlags());
    }
    
    function testGetDecimals() {
        $coldef = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, false, false, false, null, null, null);
        self::assertNull($coldef->getDecimals());
        
        $coldef2 = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, 2, false, false, false, false, false, false, false, null, null, null);
        self::assertSame(2, $coldef2->getDecimals());
    }
    
    function testGetNullable() {
        $coldef = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, true, false, false, false, false, false, false, null, null, null);
        self::assertTrue($coldef->isNullable());
        
        $coldef2 = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, true, true, true, true, true, true, null, null, null);
        self::assertFalse($coldef2->isNullable());
    }
    
    function testGetAutoIncremented() {
        $coldef = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, true, false, false, false, false, false, null, null, null);
        self::assertTrue($coldef->isAutoIncrement());
        
        $coldef2 = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, true, false, true, true, true, true, true, null, null, null);
        self::assertFalse($coldef2->isAutoIncrement());
    }
    
    function testGetPrimary() {
        $coldef = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, true, false, false, false, false, null, null, null);
        self::assertTrue($coldef->isPrimaryKey());
        
        $coldef2 = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, true, true, false, true, true, true, true, null, null, null);
        self::assertFalse($coldef2->isPrimaryKey());
    }
    
    function testGetUnique() {
        $coldef = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, true, false, false, false, null, null, null);
        self::assertTrue($coldef->isUniqueKey());
        
        $coldef2 = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, true, true, true, false, true, true, true, null, null, null);
        self::assertFalse($coldef2->isUniqueKey());
    }
    
    function testGetComposite() {
        $coldef = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, true, false, false, null, null, null);
        self::assertTrue($coldef->isMultipleKey());
        
        $coldef2 = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, true, true, true, true, false, true, true, null, null, null);
        self::assertFalse($coldef2->isMultipleKey());
    }
    
    function testGetUnsigned() {
        $coldef = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, false, true, false, null, null, null);
        self::assertTrue($coldef->isUnsigned());
        
        $coldef2 = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, true, true, true, true, true, false, true, null, null, null);
        self::assertFalse($coldef2->isUnsigned());
    }
    
    function testGetZerofilled() {
        $coldef = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, false, false, true, null, null, null);
        self::assertTrue($coldef->isZerofilled());
        
        $coldef2 = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, true, true, true, true, true, true, false, null, null, null);
        self::assertFalse($coldef2->isZerofilled());
    }
    
    function testGetForeignTarget() {
        $coldef = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, false, false, false, 'hello', null, null);
        self::assertSame('hello', $coldef->getForeignTarget());
        
        $coldef2 = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, true, true, true, true, true, true, true, null, null, null);
        self::assertNull($coldef2->getForeignTarget());
    }
    
    function testGetForeignKey() {
        $coldef = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, false, false, false, null, 'hello', null);
        self::assertSame('hello', $coldef->getForeignKey());
    
        $coldef2 = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, true, true, true, true, true, true, true, null, null, null);
        self::assertNull($coldef2->getForeignKey());
    }
    
    function testGetForeignFetchMode() {
        $coldef = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, false, false, false, null, null, 5);
        self::assertSame(5, $coldef->getForeignFetchMode());
        
        $coldef2 = $this->getColDefMock('test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, true, true, true, true, true, true, true, null, null, null);
        self::assertSame(PreloadInterface::FETCH_MODE_LAZY, $coldef2->getForeignFetchMode());
    }
    
    function getColDefMock(...$args): \Plasma\Schemas\ColumnDefinition {
        return (new \Plasma\Schemas\ColumnDefinition(...$args));
    }
}
