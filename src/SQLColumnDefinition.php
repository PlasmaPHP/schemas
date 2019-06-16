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
 * A SQL extension for the generic Column Definition implementation.
 */
class SQLColumnDefinition extends ColumnDefinition implements SQLColumnDefinitionInterface {
    /**
     * @var string|null
     */
    protected $foreignTable;
    
    /**
     * @var string|null
     */
    protected $foreignKey;
    
    /**
     * @var int
     */
    protected $foreignFetchMode = \Plasma\Schemas\SQLColumnDefinitionInterface::FETCH_MODE_LAZY;
    
    /**
     * Get the foreign table for this column, or null.
     * @return string|null
     */
    function getForeignTable(): ?string {
        return $this->foreignTable;
    }
    
    /**
     * Get the foreign key for this column, or null.
     * @return string|null
     */
    function getForeignKey(): ?string {
        return $this->foreignKey;
    }
    
    /**
     * Set the foreign key for this column.
     * @param string|null  $foreignTable
     * @param string|null  $foreignKey
     * @return $this
     * @internal
     */
    function setForeignKey(?string $foreignTable, ?string $foreignKey): self {
        $this->foreignTable = $foreignTable;
        $this->foreignKey = $foreignKey;
        return $this;
    }
    
    /**
     * Get the foreign fetch mode. See the constants.
     * @return int
     */
    function getForeignFetchMode(): int {
        return $this->foreignFetchMode;
    }
    
    /**
     * Sets the foreign fetch mode. See the constants.
     * @param int  $foreignFetchMode
     * @return $this
     * @internal
     */
    function setForeignFetchMode(int $foreignFetchMode): self {
        $this->foreignFetchMode = $foreignFetchMode;
        return $this;
    }
}
