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
 * Schema Collections hold schemas and the associated query result together.
 */
class SchemaCollection {
    /**
     * @var \Plasma\Schemas\SchemaInterface[]
     */
    protected $schemas;
    
    /**
     * @var \Plasma\QueryResultInterface
     */
    protected $result;
    
    /**
     * Constructor.
     * @param \Plasma\Schemas\SchemaInterface[]  $schemas
     * @param \Plasma\QueryResultInterface       $result
     */
    function __construct(array $schemas, \Plasma\QueryResultInterface $result) {
        $this->schemas = $schemas;
        $this->result = $result;
    }
    
    /**
     * Get the stored schemas.
     * @return \Plasma\Schemas\SchemaInterface[]
     */
    function getSchemas(): array {
        return $this->schemas;
    }
    
    /**
     * Get the query result.
     * @return \Plasma\QueryResultInterface
     */
    function getResult(): \Plasma\QueryResultInterface {
        return $this->result;
    }
}
