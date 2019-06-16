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
 * A SQL extension interface for the generic Column Definition implementation.
 */
interface SQLColumnDefinitionInterface extends \Plasma\ColumnDefinitionInterface {
    /**
     * Foreign keys are never automatically resolved in schemas.
     * Foreign keys need to be explicitely resolved.
     * Default mode.
     * @var int
     * @source
     */
    const FETCH_MODE_LAZY = 0x1;
    
    /**
     * Foreign keys are always automatically resolved in schemas.
     * However this will lead to more data fetching and memory growth.
     * This may be unnecessary in certain situations.
     * @var int
     * @source
     */
    const FETCH_MODE_ALWAYS = 0x5;
    
    /**
     * Get the foreign table for this column, or null.
     * @return string|null
     */
    function getForeignTable(): ?string;
    
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
