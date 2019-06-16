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
 * A SQL extension for the generic Schema implementation.
 */
abstract class SQLSchema extends Schema {
    /**
     * Resolves all foreign keys (if there are any). All foreign keys are replaced
     * with instances of `SchemaInterface` (as returned by the Directory).
     *
     * Column Definitions must be instance of the `SQLColumnDefinitionInterface`.
     *
     * Resolves with a new instance of the schema.
     * @param bool  $autoloading  Whether this method gets called for autoloading (not manually).
     * @return \React\Promise\PromiseInterface|null
     * @see \Plasma\Schemas\SQLColumnDefinitionInterface
     */
    function getAsyncResolver(bool $autoloading = false): ?\React\Promise\PromiseInterface {
        static $promise;
        
        if($promise !== null) {
            return $promise;
        }
        
        if($this->hasAsyncResolver($autoloading)) {
            $table = $this->getTableName();
            
            if(!isset(static::$schemaFieldsMapper[$table]['__definition__'])) {
                return null;
            }
            
            /** @var \Plasma\ColumnDefinitionInterface  $column */
            foreach(static::$schemaFieldsMapper[$table]['__definition__'] as $column) {
                if(
                    $column instanceof \Plasma\Schemas\SQLColumnDefinitionInterface &&
                    $column->getForeignTable() !== null &&
                    $column->getForeignKey() !== null
                ) {
                    if($autoloading && $column->getForeignFetchMode() !== \Plasma\Schemas\SQLColumnDefinitionInterface::FETCH_MODE_ALWAYS) {
                        continue;
                    }
                    
                    if($promise === null) {
                        $promise = \React\Promise\resolve();
                    }
                    
                    $promise = $promise->then(function (self $schema) use ($column) {
                        $table = $column->getForeignTable();
                        $key = $column->getForeignKey();
                        $name = static::$schemaFieldsMapper[$table][$column->getName()];
                        
                        return $this->repo
                            ->getDirectory($table)
                            ->fetchBy($key, $this->$name)
                            ->then(function (\Plasma\Schemas\SchemaInterface $foreign) use ($schema, $name) {
                                $schema = clone $schema;
                                $schema->$name = $foreign;
                                
                                return $schema;
                            });
                    });
                }
            }
        }
        
        return $promise;
    }
    
    /**
     * Get a new instance of a sql column definition builder.
     * @return \Plasma\Schemas\SQLColumnDefinitionBuilder
     * @see \Plasma\Schemas\ColumnDefinitionBuilder::create()
     */
    function getSQLColDefBuilder(): \Plasma\Schemas\ColumnDefinitionBuilder {
        return \Plasma\Schemas\SQLColumnDefinitionBuilder::createWithSchema($this);
    }
    
    /**
     * Whether we have an async resolver.
     * @param bool  $autoloading
     * @return bool
     */
    protected function hasAsyncResolver(bool $autoloading): bool {
        static $hasF, $hasT;
        
        $has = ($autoloading ? $hasT : $hasF);
        
        if($has === null) {
            $table = $this->getTableName();
            
            if(!isset(static::$schemaFieldsMapper[$table]['__definition__'])) {
                return false;
            }
            
            /** @var \Plasma\ColumnDefinitionInterface  $column */
            foreach(static::$schemaFieldsMapper[$table]['__definition__'] as $column) {
                $has = (
                    $column instanceof \Plasma\Schemas\SQLColumnDefinitionInterface &&
                    $column->getForeignTable() !== null &&
                    $column->getForeignKey() !== null &&
                    $column->getForeignFetchMode() === \Plasma\Schemas\SQLColumnDefinition::FETCH_MODE_LAZY
                );
                
                if($has) {
                    break;
                }
            }
        }
        
        if($autoloading) {
            $hasT = $has;
        } else {
            $hasF = $has;
        }
        
        return $has;
    }
}
