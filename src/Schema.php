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
abstract class Schema implements SchemaInterface {
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
            static::$schemaFieldsMapper[$table] = array();
            
            $fields = $this->getDefinition();
            foreach($fields as $field) {
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
        
        foreach($row as $colname => $value) {
            if(!isset(static::$schemaFieldsMapper[$table][$colname])) {
                throw new \Plasma\Exception('Unknown column "'.$colname.'"');
            }
            
            $name = static::$schemaFieldsMapper[$table][$colname];
            $this->$name = $value;
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
     * Returns the schema definition.
     * @return \Plasma\ColumnDefinitionInterface[]
     */
    abstract static function getDefinition(): array;
    
    /**
     * Returns the name of the database (or any other equivalent).
     * @return string
     */
    abstract static function getDatabaseName(): string;
    
    /**
     * Returns the name of the table (or any other equivalent).
     * @return string
     */
    abstract static function getTableName(): string;
    
    /**
     * Returns the name of the identifier column (primary or unique), or null.
     * @return string|null
     */
    abstract static function getIdentifierColumn(): ?string;
    
    /**
     * Inserts the schema.
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    function insert(): \React\Promise\PromiseInterface {
        $values = array();
        
        foreach(static::$schemaFieldsMapper[static::getTableName()] as $name) {
            if(\property_exists($this, $name)) {
                $values[$name] = $this->$name;
            }
        }
    
        return $this->repo->getDirectory(static::getTableName())
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
            throw new \Plasma\Exception('Schema has no unique or primary column');
        }
        
        $uniq = static::$schemaFieldsMapper[static::getTableName()][$uniqcol];
        
        $values = array();
        foreach($data as $name => $val) {
            if(!\property_exists($this, $name)) {
                throw new \Plasma\Exception('Unknown field given "'.$name.'", make sure you use the property name');
            }
            
            $values[static::$schemaFieldsMapper[static::getTableName()][$name]] = $val;
        }
        
        return $this->repo->getDirectory(static::getTableName())
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
            throw new \Plasma\Exception('Schema has no unique or primary column');
        }
        
        $table = $this->getTableName();
        
        $uniqname = static::$schemaFieldsMapper[$table][$uniqcol];
        $uniq = $this->repo->quote($uniqcol, \Plasma\DriverInterface::QUOTE_TYPE_IDENTIFIER).' = ?';
        
        $table = $this->repo->quote($table, \Plasma\DriverInterface::QUOTE_TYPE_IDENTIFIER);
        
        return $this->repo->execute('DELETE FROM '.$table.' WHERE '.$uniq, array($this->$uniqname))->then(array($this, 'handleQueryResult'));
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
