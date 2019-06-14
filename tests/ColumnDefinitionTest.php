<?php
/**
 * Plasma Schemas component
 * Copyright 2018-2019 PlasmaPHP, All Rights Reserved
 *
 * Website: https://github.com/PlasmaPHP
 * License: https://github.com/PlasmaPHP/schemas/blob/master/LICENSE
 */

namespace Plasma\Schemas\Tests;

class ColumnDefinitionTest extends TestCase {
    function testGetDatabaseName() {
        $coldef = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, false, false, false);
        $this->assertSame('test', $coldef->getDatabaseName());
    }
    
    function testGetTableName() {
        $coldef = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, false, false, false);
        $this->assertSame('test2', $coldef->getTableName());
    }
    
    function testGetName() {
        $coldef = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, false, false, false);
        $this->assertSame('coltest', $coldef->getName());
    }
    
    function testGetType() {
        $coldef = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, false, false, false);
        $this->assertSame('BIGINT', $coldef->getType());
    }
    
    function testGetCharset() {
        $coldef = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, false, false, false);
        $this->assertSame('utf8mb4', $coldef->getCharset());
    }
    
    function testGetLength() {
        $coldef = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, false, false, false);
        $this->assertSame(20, $coldef->getLength());
        
        $coldef2 = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', null, 0, null, false, false, false, false, false, false, false);
        $this->assertNull($coldef2->getLength());
    }
    
    function testGetFlags() {
        $coldef = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, false, false, false);
        $this->assertSame(0, $coldef->getFlags());
    }
    
    function testGetDecimals() {
        $coldef = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, false, false, false);
        $this->assertNull($coldef->getDecimals());
        
        $coldef2 = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, 2, false, false, false, false, false, false, false);
        $this->assertSame(2, $coldef2->getDecimals());
    }
    
    function testGetNullable() {
        $coldef = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, true, false, false, false, false, false, false);
        $this->assertTrue($coldef->isNullable());
        
        $coldef2 = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, true, true, true, true, true, true);
        $this->assertFalse($coldef2->isNullable());
    }
    
    function testGetAutoIncremented() {
        $coldef = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, true, false, false, false, false, false);
        $this->assertTrue($coldef->isAutoIncrement());
        
        $coldef2 = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, true, false, true, true, true, true, true);
        $this->assertFalse($coldef2->isAutoIncrement());
    }
    
    function testGetPrimary() {
        $coldef = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, true, false, false, false, false);
        $this->assertTrue($coldef->isPrimaryKey());
        
        $coldef2 = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, true, true, false, true, true, true, true);
        $this->assertFalse($coldef2->isPrimaryKey());
    }
    
    function testGetUnique() {
        $coldef = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, true, false, false, false);
        $this->assertTrue($coldef->isUniqueKey());
        
        $coldef2 = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, true, true, true, false, true, true, true);
        $this->assertFalse($coldef2->isUniqueKey());
    }
    
    function testGetComposite() {
        $coldef = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, true, false, false);
        $this->assertTrue($coldef->isMultipleKey());
        
        $coldef2 = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, true, true, true, true, false, true, true);
        $this->assertFalse($coldef2->isMultipleKey());
    }
    
    function testGetUnsigned() {
        $coldef = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, false, true, false);
        $this->assertTrue($coldef->isUnsigned());
        
        $coldef2 = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, true, true, true, true, true, false, true);
        $this->assertFalse($coldef2->isUnsigned());
    }
    
    function testGetZerofilled() {
        $coldef = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, false, false, false, false, false, false, true);
        $this->assertTrue($coldef->isZerofilled());
        
        $coldef2 = $this->getColDefMock('test', 'test2', 'coltest', 'BIGINT', 'utf8mb4', 20, 0, null, true, true, true, true, true, true, false);
        $this->assertFalse($coldef2->isZerofilled());
    }
    
    function getColDefMock(...$args): \Plasma\Schemas\ColumnDefinition {
        return (new \Plasma\Schemas\ColumnDefinition(...$args));
    }
}
