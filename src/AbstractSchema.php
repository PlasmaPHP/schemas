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
        $table = $this->getTableName();
        
        if(!isset(static::$schemaFieldsMapper[$table])) {
            static::$schemaFieldsMapper[$table] = array(
                '__definition__' => $this->getDefinition()
            );
            
            /** @var \Plasma\ColumnDefinitionInterface  $field */
            foreach(static::$schemaFieldsMapper[$table]['__definition__'] as $field) {
                $colname = $field->getName();
                $name = $this->convertColumnName($colname);
                
                if(!\property_exists($this, $name)) {
                    throw new \Plasma\Exception('Property "'.$name.'" for column "'.$colname.'" does not exist');
                }
                
                static::$schemaFieldsMapper[$table][$colname] = $name;
                static::$schemaFieldsMapper[$table][$name] = $colname;
            }
            
            $uniq = $this->getIdentifierColumn();
            if($uniq !== null && !isset(static::$schemaFieldsMapper[$table][$uniq])) {
                throw new \Plasma\Exception('Field "'.$uniq.'" for identifier column does not exist');
            }
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
        
        if(!$this->hasAsyncResolver(true)) {
            $this->validateData();
        }
    }
    
    /**
     * Child classes can override this method to implement some sort of validation.
     * This method gets invoked after assigning the properties. It has no functionality by default.
     * @return void
     */
    function validateData() {
        
    }
    
    /**
     * Returns the asynchronous resolver to wait for before returning the schema.
     * May resolve with a new schema, which will get used instead.
     * @param bool  $autoloading  Whether this method gets called for autoloading (not manually).
     * @return \React\Promise\PromiseInterface|null
     */
    function getAsyncResolver(bool $autoloading = false): ?\React\Promise\PromiseInterface {
        return null;
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
    function getColDefBuilder(): \Plasma\Schemas\ColumnDefinitionBuilder {
        return \Plasma\Schemas\ColumnDefinitionBuilder::createWithSchema($this);
    }
    
    /**
     * Converts a snake case column to a camelcase identifier
     * @param string  $name
     * @return string
     */
    protected function convertColumnName(string $name): string {
        if(\strpos($name, '_') === false) {
            return $name;
        }
        
        return \lcfirst(\str_replace('_', '', \ucwords($name, '_')));
    }
    
    /**
     * Whether we have an async resolver.
     * @param bool  $autoloading
     * @return bool
     */
    protected function hasAsyncResolver(bool $autoloading): bool {
        return false;
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
}
