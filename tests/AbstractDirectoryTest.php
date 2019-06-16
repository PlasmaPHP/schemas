<?php
/**
 * Plasma Schemas component
 * Copyright 2018-2019 PlasmaPHP, All Rights Reserved
 *
 * Website: https://github.com/PlasmaPHP
 * License: https://github.com/PlasmaPHP/schemas/blob/master/LICENSE
*/

namespace Plasma\Schemas\Tests;

class AbstractDirectoryTest extends TestCase {
    function testConstructor() {
        $schema = (new class() extends \Plasma\Schemas\AbstractSchema {
            public $help;
            
            function __construct() {
                
            }
            
            static function getDefinition(): array {
                return array();
            }
    
            static function getDatabaseName(): string {
                return \bin2hex(\random_bytes(5));
            }
            
            static function getTableName(): string {
                return 'test_Directory';
            }
            
            static function getIdentifierColumn(): ?string {
                return null;
            }
        });
        
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        /** @var \Plasma\Schemas\AbstractDirectory  $builder */
        $builder = $this->getMockBuilder(\Plasma\Schemas\AbstractDirectory::class)
            ->setConstructorArgs(array(\get_class($schema)))
            ->getMockForAbstractClass();
        
        $builder->setRepository($repo);
        $this->assertSame($repo, $builder->getRepository());
    }
    
    function testConstructorUnknownClass() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $this->expectException(\Plasma\Exception::class);
        $this->expectExceptionMessage('AbstractSchema class does not exist');
    
        /** @var \Plasma\Schemas\AbstractDirectory  $builder */
        $builder = $this->getMockBuilder(\Plasma\Schemas\AbstractDirectory::class)
            ->setConstructorArgs(array('a'))
            ->getMockForAbstractClass();
    }
    
    function testConstructorInvalidClass() {
        $client = $this->getClientMock();
        $repo = new \Plasma\Schemas\Repository($client);
        
        $this->expectException(\Plasma\Exception::class);
        $this->expectExceptionMessage('AbstractSchema class does not implement AbstractSchema Interface');
    
        /** @var \Plasma\Schemas\AbstractDirectory  $builder */
        $builder = $this->getMockBuilder(\Plasma\Schemas\AbstractDirectory::class)
            ->setConstructorArgs(array(\stdClass::class))
            ->getMockForAbstractClass();
    }
}
