<?php
/**
 * Plasma Schemas component
 * Copyright 2018-2019 PlasmaPHP, All Rights Reserved
 *
 * Website: https://github.com/PlasmaPHP
 * License: https://github.com/PlasmaPHP/schemas/blob/master/LICENSE
*/

namespace Plasma\Schemas;

use Plasma\QueryResultInterface;

/**
 * AbstractSchema Collections hold schemas and the associated query result together.
 */
class SchemaCollection {
    /**
     * @var SchemaInterface[]
     */
    protected $schemas;
    
    /**
     * @var QueryResultInterface
     */
    protected $result;
    
    /**
     * Constructor.
     * @param SchemaInterface[]     $schemas
     * @param QueryResultInterface  $result
     */
    function __construct(array $schemas, QueryResultInterface $result) {
        $this->schemas = $schemas;
        $this->result = $result;
    }
    
    /**
     * Get the stored schemas.
     * @return SchemaInterface[]
     */
    function getSchemas(): array {
        return $this->schemas;
    }
    
    /**
     * Get the query result.
     * @return QueryResultInterface
     */
    function getResult(): QueryResultInterface {
        return $this->result;
    }
}
