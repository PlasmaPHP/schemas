<?php
/**
 * Plasma Schemas component
 * Copyright 2018-2019 PlasmaPHP, All Rights Reserved
 *
 * Website: https://github.com/PlasmaPHP
 * License: https://github.com/PlasmaPHP/schemas/blob/master/LICENSE
*/

namespace Plasma\Schemas;

use Plasma\Exception;
use Plasma\QueryResultInterface;

/**
 * This is an abstract Directory implementation for reuse in complete Directories.
 */
abstract class AbstractDirectory implements DirectoryInterface {
    /**
     * @var Repository
     */
    protected $repo;
    
    /**
     * @var SchemaInterface
     */
    protected $schema;
    
    /**
     * Constructor.
     * @param string  $schema   The class name of the schema to build for.
     * @throws Exception
     */
    function __construct(string $schema) {
        if(!\class_exists($schema, true)) {
            throw new Exception('AbstractSchema class does not exist');
        } elseif(!\in_array(SchemaInterface::class, \class_implements($schema), true)) {
            throw new Exception('AbstractSchema class does not implement AbstractSchema Interface');
        }
    
        /** @var SchemaInterface $schema */
        $this->schema = $schema;
    }
    
    /**
     * Gets the repository.
     * @return Repository
     */
    function getRepository(): Repository {
        return $this->repo;
    }
    
    /**
     * Sets the repository to use.
     * @param Repository  $repository
     * @return void
     */
    function setRepository(Repository $repository): void {
        $this->repo = $repository;
    }
    
    /**
     * Builds schemas for the given SELECT query result.
     * @param QueryResultInterface  $result
     * @return SchemaCollection
     * @throws Exception
     */
    function buildSchemas(QueryResultInterface $result): SchemaCollection {
        $schemas = array();
        
        $rows = (array) $result->getRows();
        foreach($rows as $row) {
            $schemas[] = $this->schema::build($this->repo, $row);
        }
        
        return (new SchemaCollection($schemas, $result));
    }
}
