<?php
/**
 * Plasma Schemas component
 * Copyright 2018-2019 PlasmaPHP, All Rights Reserved
 *
 * Website: https://github.com/PlasmaPHP
 * License: https://github.com/PlasmaPHP/schemas/blob/master/LICENSE
*/

namespace Plasma\Schemas;

/**
 * This is an abstract Directory implementation for reuse in complete Directories.
 */
abstract class AbstractDirectory implements DirectoryInterface {
    /**
     * @var \Plasma\Schemas\Repository
     */
    protected $repo;
    
    /**
     * @var \Plasma\Schemas\SchemaInterface
     */
    protected $schema;
    
    /**
     * Constructor.
     * @param string  $schema   The class name of the schema to build for.
     * @throws \Plasma\Exception
     */
    function __construct(string $schema) {
        if(!\class_exists($schema, true)) {
            throw new \Plasma\Exception('AbstractSchema class does not exist');
        } elseif(!\in_array(\Plasma\Schemas\SchemaInterface::class, \class_implements($schema))) {
            throw new \Plasma\Exception('AbstractSchema class does not implement AbstractSchema Interface');
        }
    
        /** @var \Plasma\Schemas\SchemaInterface  $schema */
        $this->schema = $schema;
    }
    
    /**
     * Gets the repository.
     * @return \Plasma\Schemas\Repository
     */
    function getRepository(): \Plasma\Schemas\Repository {
        return $this->repo;
    }
    
    /**
     * Sets the repository to use.
     * @param \Plasma\Schemas\Repository  $repository
     * @return void
     */
    function setRepository(\Plasma\Schemas\Repository $repository): void {
        $this->repo = $repository;
    }
    
    /**
     * Builds schemas for the given SELECT query result.
     * @param \Plasma\QueryResultInterface  $result
     * @return \Plasma\Schemas\SchemaCollection
     * @throws \Plasma\Exception
     */
    function buildSchemas(\Plasma\QueryResultInterface $result): \Plasma\Schemas\SchemaCollection {
        $schemas = array();
        
        $rows = (array) $result->getRows();
        foreach($rows as $row) {
            $schemas[] = $this->schema::build($this->repo, $row);
        }
        
        return (new \Plasma\Schemas\SchemaCollection($schemas, $result));
    }
}
