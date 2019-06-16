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
 * A SQL extension for the generic Column Definition Builder implementation.
 */
class SQLColumnDefinitionBuilder extends ColumnDefinitionBuilder {
    /**
     * @var string|null
     */
    protected $foreignTable;
    
    /**
     * @var string|null
     */
    protected $foreignKey;
    
    /**
     * @var int|null
     */
    protected $foreignFetchMode;
    
    /**
     * Set the foreign key for this column.
     * @param string|null  $foreignTable
     * @param string|null  $foreignKey
     * @return SQLColumnDefinitionBuilder
     */
    function setForeignKey(?string $foreignTable, ?string $foreignKey): SQLColumnDefinitionBuilder {
        $this->foreignTable = $foreignTable;
        $this->foreignKey = $foreignKey;
        return $this;
    }
    
    /**
     * Get the foreign fetch mode. See the constants.
     * @param int|null  $foreignFetchMode
     * @return SQLColumnDefinitionBuilder
     */
    function setForeignFetchMode(?int $foreignFetchMode): SQLColumnDefinitionBuilder {
        $this->foreignFetchMode = $foreignFetchMode;
        return $this;
    }
    
    /**
     * Build the definition.
     * @return \Plasma\Schemas\SQLColumnDefinition
     */
    function getDefinition(): \Plasma\Schemas\ColumnDefinition {
        $coldef = new \Plasma\Schemas\SQLColumnDefinition(
            $this->database,
            $this->table,
            $this->name,
            $this->type,
            $this->charset,
            $this->length,
            $this->flags,
            $this->decimals,
            $this->nullable,
            $this->autoIncrement,
            $this->primary,
            $this->unique,
            $this->composite,
            $this->unsigned,
            $this->zerofilled
        );
        
        if($this->foreignKey !== null) {
            $coldef->setForeignKey($this->foreignTable, $this->foreignKey);
        }
        
        if($this->foreignFetchMode !== null) {
            $coldef->setForeignFetchMode($this->foreignFetchMode);
        }
        
        return $coldef;
    }
}
