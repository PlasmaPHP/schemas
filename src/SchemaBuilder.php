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
 * 
 */
class SchemaBuilder implements SchemaBuilderInterface {
    /**
     * @var \Plasma\Schemas\Repository
     */
    protected $repo;
    
    /**
     * @var \Plasma\Schemas\SchemaInterface
     */
    protected $schema;
    
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
     * @param \Plasma\QueryResult  $result
     * @return \Plasma\Schemas\SchemaInterface[]
     */
    function buildSchemas(\Plasma\QueryResult $result): array {
        $schemas = array();
        $schema = $this->schema;
        
        $rows = (array) $result->getRows();
        foreach($rows as $row) {
            $schemas[] = $schema::build($this->repo, $result, $row);
        }
        
        return $schemas;
    }
}
