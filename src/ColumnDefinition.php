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
 * A generic Column Definition implementation.
 *
 * It takes input and outputs it without any changes.
 */
class ColumnDefinition extends \Plasma\ColumnDefinition {
    /**
     * @var bool
     */
    protected $nullable;
    
    /**
     * @var bool
     */
    protected $autoIncremented;
    
    /**
     * @var bool
     */
    protected $primary;
    
    /**
     * @var bool
     */
    protected $unique;
    
    /**
     * @var bool
     */
    protected $composite;
    
    /**
     * @var bool
     */
    protected $unsigned;
    
    /**
     * @var bool
     */
    protected $zerofilled;
    
    /**
     * Constructor.
     * @param string       $database
     * @param string       $table
     * @param string       $name
     * @param string       $type
     * @param string       $charset
     * @param int|null     $length
     * @param int          $flags
     * @param int|null     $decimals
     * @param bool         $nullable
     * @param bool         $autoIncremented
     * @param bool         $primary
     * @param bool         $unique
     * @param bool         $composite
     * @param bool         $unsigned
     * @param bool         $zerofilled
     * @internal
     */
    function __construct(
        string $database,
        string $table,
        string $name,
        string $type,
        string $charset,
        ?int $length,
        int $flags,
        ?int $decimals,
        bool $nullable,
        bool $autoIncremented,
        bool $primary,
        bool $unique,
        bool $composite,
        bool $unsigned,
        bool $zerofilled
    ) {
        parent::__construct($database, $table, $name, $type, $charset, $length, $flags, $decimals);
        
        $this->nullable = $nullable;
        $this->autoIncremented = $autoIncremented;
        $this->primary = $primary;
        $this->unique = $unique;
        $this->composite = $composite;
        $this->unsigned = $unsigned;
        $this->zerofilled = $zerofilled;
    }
    
    /**
     * Whether the column is nullable (not `NOT NULL`).
     * @return bool
     */
    function isNullable(): bool {
        return $this->nullable;
    }
    
    /**
     * Whether the column is auto incremented.
     * @return bool
     */
    function isAutoIncrement(): bool {
        return $this->autoIncremented;
    }
    
    /**
     * Whether the column is the primary key.
     * @return bool
     */
    function isPrimaryKey(): bool {
        return $this->primary;
    }
    
    /**
     * Whether the column is the unique key.
     * @return bool
     */
    function isUniqueKey(): bool {
        return $this->unique;
    }
    
    /**
     * Whether the column is part of a multiple/composite key.
     * @return bool
     */
    function isMultipleKey(): bool {
        return $this->composite;
    }
    
    /**
     * Whether the column is unsigned (only makes sense for numeric types).
     * @return bool
     */
    function isUnsigned(): bool {
        return $this->unsigned;
    }
    
    /**
     * Whether the column gets zerofilled to the length.
     * @return bool
     */
    function isZerofilled(): bool {
        return $this->zerofilled;
    }
}
