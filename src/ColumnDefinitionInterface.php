<?php
/**
 * Plasma Schemas component
 * Copyright 2018-2019 PlasmaPHP, All Rights Reserved
 *
 * Website: https://github.com/PlasmaPHP
 * License: https://github.com/PlasmaPHP/schemas/blob/master/LICENSE
 */

namespace Plasma\Schemas;

use Plasma\ColumnDefinitionInterface as BaseColumnDefinitionInterface;

/**
 * An extension interface for the generic Column Definition implementation for some features.
 */
interface ColumnDefinitionInterface extends BaseColumnDefinitionInterface {
    /**
     * Get the foreign target for this column, or null.
     * @return string|null
     */
    function getForeignTarget(): ?string;
    
    /**
     * Get the foreign key for this column, or null.
     * @return string|null
     */
    function getForeignKey(): ?string;
    
    /**
     * Get the foreign fetch mode. See the constants.
     * @return int
     */
    function getForeignFetchMode(): int;
}
