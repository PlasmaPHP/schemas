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
use Plasma\Schemas\AbstractDirectory;
use Plasma\Schemas\AbstractSchema;
use Plasma\Schemas\Repository;

class AbstractDirectoryTest extends TestCase {
    function testConstructor() {
        $schema = (new class() extends AbstractSchema {
            public $help;
            
            /** @noinspection PhpMissingParentConstructorInspection */
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
        $repo = new Repository($client);
        
        /** @var AbstractDirectory  $builder */
        $builder = $this->getMockBuilder(AbstractDirectory::class)
            ->setConstructorArgs(array(\get_class($schema)))
            ->getMockForAbstractClass();
        
        $builder->setRepository($repo);
        self::assertSame($repo, $builder->getRepository());
    }
    
    function testConstructorUnknownClass() {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('AbstractSchema class does not exist');
    
        $this->getMockBuilder(AbstractDirectory::class)
            ->setConstructorArgs(array('a'))
            ->getMockForAbstractClass();
    }
    
    function testConstructorInvalidClass() {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('AbstractSchema class does not implement AbstractSchema Interface');
        
        $this->getMockBuilder(AbstractDirectory::class)
            ->setConstructorArgs(array(\stdClass::class))
            ->getMockForAbstractClass();
    }
}
