<?php
/**
 * Plasma Schemas component
 * Copyright 2018-2019 PlasmaPHP, All Rights Reserved
 *
 * Website: https://github.com/PlasmaPHP
 * License: https://github.com/PlasmaPHP/schemas/blob/master/LICENSE
*/

namespace Plasma\Schemas\Tests;

class ColumnDefinition extends \Plasma\ColumnDefinition {
    /**
     * Whether the column is nullable (not `NOT NULL`).
     * @return bool
     */
    function isNullable(): bool {
        return false;
    }
    
    /**
     * Whether the column is auto incremented.
     * @return bool
     */
    function isAutoIncrement(): bool {
        return false;
    }
    
    /**
     * Whether the column is the primary key.
     * @return bool
     */
    function isPrimaryKey(): bool {
        return false;
    }
    
    /**
     * Whether the column is the unique key.
     * @return bool
     */
    function isUniqueKey(): bool {
        return false;
    }
    
    /**
     * Whether the column is part of a multiple/composite key.
     * @return bool
     */
    function isMultipleKey(): bool {
        return false;
    }
    
    /**
     * Whether the column is unsigned (only makes sense for numeric types).
     * @return bool
     */
    function isUnsigned(): bool {
        return false;
    }
    
    /**
     * Whether the column gets zerofilled to the length.
     * @return bool
     */
    function isZerofilled(): bool {
        return false;
    }
}
