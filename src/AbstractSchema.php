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
 * This is a schema class which maps each rows column to a camelcase property.
 *
 * This class must be extended and the properties must be provided by the extending class (but not as private).
 * This allows validation and match expectations.
 */
abstract class AbstractSchema implements SchemaInterface {
    /**
     * @var \Plasma\Schemas\Repository
     */
    protected $repo;
    
    /**
     * @var array
     */
    protected static $schemaFieldsMapper = array();
    
    /**
     * Constructor.
     * @param \Plasma\Schemas\Repository  $repo
     * @param array                       $row
     * @throws \Plasma\Exception
     */
    function __construct(\Plasma\Schemas\Repository $repo, array $row) {
        $this->repo = $repo;
        $table = static::getTableName();
        
        if(!isset(static::$schemaFieldsMapper[$table])) {
            static::buildSchemaDefinition();
        }
        
        /** @var \Plasma\ColumnDefinitionInterface  $column */
        foreach(static::$schemaFieldsMapper[$table]['__definition__'] as $column) {
            $colname = $column->getName();
            $name = $this->convertColumnName($colname);
            
            if(!isset($row[$colname])) {
                continue;
            }
            
            $this->$name = $row[$colname];
        }
        
        $this->validateData();
    }
    
    /**
     * Child classes can override this method to implement some sort of validation.
     * This method gets invoked after assigning the properties. It has no functionality by default.
     * @return void
     */
    function validateData() {
        
    }
    
    /**
     * Lets the directory preload the foreign references on schema request.
     * Returns an array of `PreloadInterface`.
     * @return \Plasma\Schemas\PreloadInterface[]
     * @throws \Plasma\Exception
     */
    static function getPreloads(): array {
        static $columns;
        
        if($columns === null) {
            $table = static::getTableName();
            
            if(!isset(static::$schemaFieldsMapper[$table]['__definition__'])) {
                static::buildSchemaDefinition();
            }
            
            $fetchMode = \Plasma\Schemas\PreloadInterface::FETCH_MODE_ALWAYS;
            $columns = array();
            
            /** @var \Plasma\ColumnDefinitionInterface $column */
            foreach(static::$schemaFieldsMapper[$table]['__definition__'] as $column) {
                if(
                    $column instanceof \Plasma\Schemas\ColumnDefinitionInterface &&
                    $column->getForeignTarget() !== null &&
                    $column->getForeignKey() !== null &&
                    $column->getForeignFetchMode() === $fetchMode
                ) {
                    $columns[] = new \Plasma\Schemas\Preload(
                        $column->getForeignTarget(),
                        $column->getForeignKey(),
                        $column->getName()
                    );
                }
            }
        }
        
        return $columns;
    }
    
    /**
     * This is the after preload hook, which gets called with the preloads
     * which were used to create the schema. The hook is responsible for
     * creating the other schemas from the preloads and the table result.
     * @param \Plasma\QueryResultInterface        $result    This is always a query result with only a single row.
     * @param \Plasma\Schemas\PreloadInterface[]  $preloads
     * @return void
     * @throws \Plasma\Exception  Thrown when the foreign directory does not exist.
     * @throws \Plasma\Exception  Thrown when the preload local key does not exist.
     */
    function afterPreloadHook(\Plasma\QueryResultInterface $result, array $preloads): void {
        $columns = $result->getFieldDefinitions();
        $table = static::getTableName();
        
        foreach($preloads as $preload) {
            $target = $preload->getForeignTarget();
            $key = $preload->getForeignKey();
            $local = static::$schemaFieldsMapper[$table][$preload->getLocalKey()] ?? null;
            
            if($local === null) {
                throw new \Plasma\Exception('Unknown preload local key "'.$preload->getLocalKey().'"');
            }
            
            foreach($columns as $column) {
                if($column->getTableName() === $target && $column->getName() === $key) {
                    $this->$local = $this->repo
                        ->getDirectory($target)
                        ->buildSchemas($result)
                        ->getSchemas()[0];
                    break 1;
                }
            }
        }
    }
    
    /**
     * Returns the asynchronous resolver to wait for before returning the schema.
     * Resolves with a new schema, which will get used instead, or null.
     * @return \React\Promise\PromiseInterface|null
     */
    function getAsyncResolver(): ?\React\Promise\PromiseInterface {
        static $promise;
        
        if($promise !== null) {
            return $promise;
        }
        
        $table = static::getTableName();
        
        /** @var \Plasma\ColumnDefinitionInterface  $column */
        foreach(static::$schemaFieldsMapper[$table]['__definition__'] as $column) {
            $name = static::$schemaFieldsMapper[$table][$column->getName()] ?? null;
            
            if(
                $column instanceof \Plasma\Schemas\ColumnDefinitionInterface &&
                $column->getForeignTarget() !== null &&
                $column->getForeignKey() !== null &&
                ($this->$name ?? null) !== null &&
                !($this->$name instanceof \Plasma\Schemas\SchemaInterface)
            ) {
                if($promise === null) {
                    $promise = \React\Promise\resolve($this);
                }
                
                $promise = $promise->then(function (self $schema) use ($column, $name) {
                    $table = $column->getForeignTarget();
                    $key = $column->getForeignKey();
                    
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
        
        return $promise;
    }
    
    /**
     * Builds a schema instance.
     * @param \Plasma\Schemas\Repository  $repository
     * @param array                       $row
     * @return \Plasma\Schemas\SchemaInterface
     * @throws \Plasma\Exception
     */
    static function build(\Plasma\Schemas\Repository $repository, array $row): \Plasma\Schemas\SchemaInterface {
        return (new static($repository, $row));
    }
    
    /**
     * Inserts the schema.
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    function insert(): \React\Promise\PromiseInterface {
        $table = $this->getTableName();
        $values = array();
        
        foreach(static::$schemaFieldsMapper[$table] as $name) {
            if(\is_array($name)) {
                continue;
            }
            
            if(\property_exists($this, $name)) {
                $values[$name] = $this->$name;
            }
        }
    
        return $this->repo->getDirectory($table)
            ->insert($values)
            ->then(array($this, 'handleQueryResult'));
    }
    
    /**
     * Updates the row with the new data. Resolves with a `QueryResultInterface` instance.
     * @param array  $data
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    function update(array $data): \React\Promise\PromiseInterface {
        $uniqcol = $this->getIdentifierColumn();
        if($uniqcol === null) {
            throw new \Plasma\Exception('AbstractSchema has no unique or primary column');
        }
    
        $table = $this->getTableName();
        $uniq = static::$schemaFieldsMapper[$table][$uniqcol];
        
        $values = array();
        foreach($data as $name => $val) {
            if(!\property_exists($this, $name)) {
                throw new \Plasma\Exception('Unknown field given "'.$name.'", make sure you use the property name');
            }
            
            $values[static::$schemaFieldsMapper[$table][$name]] = $val;
        }
        
        return $this->repo->getDirectory($table)
            ->update($values, $uniqcol, $this->$uniq)
            ->then(array($this, 'handleQueryResult'));
    }
    
    /**
     * Deletes the row. Resolves with a `QueryResultInterface` instance.
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    function delete(): \React\Promise\PromiseInterface {
        $uniqcol = $this->getIdentifierColumn();
        if($uniqcol === null) {
            throw new \Plasma\Exception('AbstractSchema has no unique or primary column');
        }
        
        $table = $this->getTableName();
        $uniq = static::$schemaFieldsMapper[$table][$uniqcol];
        
        return $this->repo->getDirectory($table)
            ->deleteBy($uniqcol, $this->$uniq);
    }
    
    /**
     * Returns an array with all values mapped by the column name.
     * @return array
     */
    function toArray(): array {
        $table = $this->getTableName();
        $row = array();
        
        foreach($this as $key => $val) {
            if(isset(static::$schemaFieldsMapper[$table][$key])) {
                $row[$key] = $val;
            }
        }
        
        return $row;
    }
    
    /**
     * Get a new instance of a column definition builder.
     * @return \Plasma\Schemas\ColumnDefinitionBuilder
     * @see \Plasma\Schemas\ColumnDefinitionBuilder::create()
     */
    static function getColDefBuilder(): \Plasma\Schemas\ColumnDefinitionBuilder {
        return (new \Plasma\Schemas\ColumnDefinitionBuilder())
            ->table(static::getTableName());
    }
    
    /**
     * Converts a snake case column to a camelcase identifier
     * @param string  $name
     * @return string
     */
    protected static function convertColumnName(string $name): string {
        if(\strpos($name, '_') === false) {
            return $name;
        }
        
        return \lcfirst(\str_replace('_', '', \ucwords($name, '_')));
    }
    
    /**
     * Internally used.
     * @return array
     * @internal
     * @codeCoverageIgnore
     */
    static function getMapper(): array {
        return static::$schemaFieldsMapper;
    }
    
    /**
     * Handles the query result.
     * @param \Plasma\QueryResultInterface|\Plasma\Schemas\SchemaCollection  $result
     * @return \Plasma\QueryResultInterface|\Plasma\Schemas\SchemaCollection|self
     * @internal
     */
    function handleQueryResult($result) {
        if($result instanceof \Plasma\Schemas\SchemaCollection) {
            $schemas = $result->getSchemas();
            $schema = \reset($schemas);
            
            if($schema instanceof static) {
                if($schema != $this) { // Whether the two objects are not equal in terms of properties
                    $vars = \get_object_vars($schema);
                    unset($vars['repo'], $vars['schemaFieldsMapper']);
                    
                    foreach($vars as $name => $value) {
                        $this->$name = $value;
                    }
                }
                
                return $this;
            }
        }
        
        return $result;
    }
    
    /**
     * Builds the definition.
     * @return void
     * @throws \Plasma\Exception
     * @internal
     */
    protected static function buildSchemaDefinition(): void {
        $table = static::getTableName();
        
        static::$schemaFieldsMapper[$table] = array(
            '__definition__' => static::getDefinition()
        );
        
        /** @var \Plasma\ColumnDefinitionInterface $field */
        foreach(static::$schemaFieldsMapper[$table]['__definition__'] as $field) {
            $colname = $field->getName();
            $name = static::convertColumnName($colname);
            
            if(!\property_exists(\get_called_class(), $name)) {
                throw new \Plasma\Exception('Property "'.$name.'" for column "'.$colname.'" does not exist');
            }
            
            static::$schemaFieldsMapper[$table][$colname] = $name;
            static::$schemaFieldsMapper[$table][$name] = $colname;
        }
        
        $uniq = static::getIdentifierColumn();
        if($uniq !== null && !isset(static::$schemaFieldsMapper[$table][$uniq])) {
            throw new \Plasma\Exception('Field "'.$uniq.'" for identifier column does not exist');
        }
    }
}
