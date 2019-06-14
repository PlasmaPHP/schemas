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
    protected $database = '';
    
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
     * @var string
     */
    protected $charset = '';
    
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
     * Builds a new builder instance and pre-sets the database and table name.
     * @param \Plasma\Schemas\SchemaInterface  $schema
     * @return \Plasma\Schemas\ColumnDefinitionBuilder
     */
    static function createWithSchema(\Plasma\Schemas\SchemaInterface $schema): self {
        $builder = new static();
        $builder->database = $schema->getDatabaseName();
        $builder->table = $schema->getTableName();
        
        return $builder;
    }
    
    /**
     * Set the database name.
     * @param string  $database
     * @return $this
     */
    function database(string $database): self {
        $this->database = $database;
        return $this;
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
     * @param string  $charset
     * @return $this
     */
    function charset(string $charset): self {
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
     * Build the definition.
     * @return \Plasma\Schemas\ColumnDefinition
     */
    function getDefinition(): \Plasma\Schemas\ColumnDefinition {
        return (new \Plasma\Schemas\ColumnDefinition(
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
        ));
    }
}
