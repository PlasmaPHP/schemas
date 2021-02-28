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
 * A Column Definition Builder implementation.
 */
class ColumnDefinitionBuilder {
    /**
     * @var string
     */
    protected $table = '';
    
    /**
     * @var string
     */
    protected $name = '';
    
    /**
     * @var string
     */
    protected $type = '';
    
    /**
     * @var string|null
     */
    protected $charset;
    
    /**
     * @var int|null
     */
    protected $length;
    
    /**
     * @var int
     */
    protected $flags = 0;
    
    /**
     * @var int|null
     */
    protected $decimals;
    
    /**
     * @var bool
     */
    protected $nullable = false;
    
    /**
     * @var bool
     */
    protected $autoIncrement = false;
    
    /**
     * @var bool
     */
    protected $primary = false;
    
    /**
     * @var bool
     */
    protected $unique = false;
    
    /**
     * @var bool
     */
    protected $composite = false;
    
    /**
     * @var bool
     */
    protected $unsigned = false;
    
    /**
     * @var bool
     */
    protected $zerofilled = false;
    
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
     * Builds a new builder instance and pre-sets the table name.
     * @param SchemaInterface  $schema
     * @return ColumnDefinitionBuilder
     */
    static function createFromSchema(SchemaInterface $schema): self {
        $builder = new static();
        $builder->table = $schema->getTableName();
        
        return $builder;
    }
    
    /**
     * Set the table name.
     * @param string  $table
     * @return $this
     */
    function table(string $table): self {
        $this->table = $table;
        return $this;
    }
    
    /**
     * Set the column name.
     * @param string  $name
     * @return $this
     */
    function name(string $name): self {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Set the type.
     * @param string  $type
     * @return $this
     */
    function type(string $type): self {
        $this->type = $type;
        return $this;
    }
    
    /**
     * Set the column charset.
     * @param string|null  $charset
     * @return $this
     */
    function charset(?string $charset): self {
        $this->charset = $charset;
        return $this;
    }
    
    /**
     * Set the column length.
     * @param int|null  $length
     * @return $this
     */
    function length(?int $length): self {
        $this->length = $length;
        return $this;
    }
    
    /**
     * Set the flags. Typically driver dependent.
     * @param int  $flags
     * @return $this
     */
    function flags(int $flags): self {
        $this->flags = $flags;
        return $this;
    }
    
    /**
     * Set the decimals.
     * @param int|null  $decimals
     * @return $this
     */
    function decimals(?int $decimals): self {
        $this->decimals = $decimals;
        return $this;
    }
    
    /**
     * Set the column as nullable.
     * @param bool  $nullable
     * @return $this
     */
    function nullable(bool $nullable = true): self {
        $this->nullable = $nullable;
        return $this;
    }
    
    /**
     * Set the column as auto incremented.
     * @param bool  $autoIncrement
     * @return $this
     */
    function autoIncrement(bool $autoIncrement = true): self {
        $this->autoIncrement = $autoIncrement;
        return $this;
    }
    
    /**
     * Set the column as primary.
     * @param bool  $primary
     * @return $this
     */
    function primary(bool $primary = true): self {
        $this->primary = $primary;
        return $this;
    }
    
    /**
     * Set the column as unique.
     * @param bool  $unique
     * @return $this
     */
    function unique(bool $unique = true): self {
        $this->unique = $unique;
        return $this;
    }
    
    /**
     * Set the column as composite.
     * @param bool  $composite
     * @return $this
     */
    function composite(bool $composite = true): self {
        $this->composite = $composite;
        return $this;
    }
    
    /**
     * Set the column as unsigned.
     * @param bool  $unsigned
     * @return $this
     */
    function unsigned(bool $unsigned = true): self {
        $this->unsigned = $unsigned;
        return $this;
    }
    
    /**
     * Set the column as zerofilled.
     * @param bool  $zerofilled
     * @return $this
     */
    function zerofilled(bool $zerofilled = true): self {
        $this->zerofilled = $zerofilled;
        return $this;
    }
    
    /**
     * Set the foreign key for this column.
     * @param string|null  $foreignTable
     * @param string|null  $foreignKey
     * @return $this
     */
    function foreignKey(?string $foreignTable, ?string $foreignKey): self {
        $this->foreignTable = $foreignTable;
        $this->foreignKey = $foreignKey;
        return $this;
    }
    
    /**
     * Get the foreign fetch mode. See the constants.
     * @param int|null  $foreignFetchMode
     * @return $this
     */
    function foreignFetchMode(?int $foreignFetchMode): self {
        $this->foreignFetchMode = $foreignFetchMode;
        return $this;
    }
    
    /**
     * Build the definition.
     * @return ColumnDefinition
     */
    function getDefinition(): ColumnDefinition {
        return (new ColumnDefinition(
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
            $this->zerofilled,
            $this->foreignTable,
            $this->foreignKey,
            $this->foreignFetchMode
        ));
    }
}
