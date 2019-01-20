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
     * Returns the name of the table.
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
        
        return $this->repo->getSchemaBuilder(static::getTableName())->insert($values)->then(array($this, 'handleQueryResult'));
    }
    
    /**
     * Updates the row with the new data. Resolves with a `QueryResultInterface` instance.
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    function update(array $data): \React\Promise\PromiseInterface {
        $uniqcol = $this->getIdentifierColumn();
        if($uniqcol === null) {
            throw new \Plasma\Exception('Schema has no unique or primary column');
        }
        
        $keys = array();
        $table = $this->getTableName();
        
        foreach($data as $key => $value) {
            $name = static::$schemaFieldsMapper[$table][$key];
            $keys[] = $this->repo->quote($name, \Plasma\DriverInterface::QUOTE_TYPE_IDENTIFIER).' = ?';
        }
        
        $uniqname = static::$schemaFieldsMapper[$table][$uniqcol];
        $uniq = $this->repo->quote($uniqcol, \Plasma\DriverInterface::QUOTE_TYPE_IDENTIFIER).' = ?';
        
        $table = $this->repo->quote($table, \Plasma\DriverInterface::QUOTE_TYPE_IDENTIFIER);
        
        $data = \array_values($data);
        $data[] = $this->$uniqname;
        
        return $this->repo->execute(
            'UPDATE '.$table.' SET '.\implode(', ', $keys).' WHERE '.$uniq,
            $data
        )->then(array($this, 'handleQueryResult'));
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
     * @param \Plasma\QueryResultInterface|self  $result
     * @return self|\Plasma\QueryResultInterface
     */
    function handleQueryResult($result) {
        if($result instanceof static) {
            if($result != $this) { // Whether the two objects are not equal in terms of properties
                $vars = \get_object_vars($result);
                unset($vars['repo'], $vars['schemaFieldsMapper']);
                
                foreach($vars as $name => $value) {
                    $this->$name = $value;
                }
            }
            
            return $this;
        }
        
        return $result;
    }
}
