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
     * @var string
     */
    protected $table;
    
    /**
     * @var array
     */
    protected static $schemaFieldsMapper = array();
    
    /**
     * Constructor.
     * @param \Plasma\Schemas\Repository         $repo
     * @param \Plasma\QueryResultInterface|null  $result  This argument must not be null when invoking for the first time.
     * @param array                              $row
     * @throws \Plasma\Exception
     */
    function __construct(\Plasma\Schemas\Repository $repo, ?\Plasma\QueryResultInterface $result, array $row) {
        $table = $this->getTableIdentifier($result);
        
        if(!isset(static::$schemaFieldsMapper[$table])) {
            if($result === null) {
                throw new \Plasma\Exception('The query result must not be null when invoking for the first time');
            }
            
            static::$schemaFieldsMapper[$table] = array();
            
            $fields = $result->getFieldDefinitions();
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
            if(!isset(static::$schemaFieldsMapper[$this->table][$uniq])) {
                throw new \Plasma\Exception('Field "'.$uniq.'" for identifier column does not exist');
            } elseif(!\property_exists($this, static::$schemaFieldsMapper[$this->table][$uniq])) {
                throw new \Plasma\Exception(
                    'Property "'.static::$schemaFieldsMapper[$this->table][$uniq].'" for identifier column "'.$uniq.'" does not exist'
                );
            }
        }
        
        $this->repo = $repo;
        $this->table = $table;
        
        $this->mapData($row);
    }
    
    /**
     * Builds a schema instance.
     * @param \Plasma\Schemas\Repository         $repository
     * @param \Plasma\QueryResultInterface|null  $result      This argument must not be null when invoking for the first time.
     * @param array                              $row
     * @return self
     * @throws \Plasma\Exception
     */
    static function build(\Plasma\Schemas\Repository $repository, ?\Plasma\QueryResultInterface $result, array $row) {
        return (new static($repository, $result, $row));
    }
    
    /**
     * Returns the name of the identifier column (primary or unique).
     * @return string
     */
    abstract function getIdentifierColumn(): string;
    
    /**
     * Updates the row with the new data. Resolves with a `QueryResultInterface` instance.
     * @return \React\Promise\PromiseInterface
     */
    function update(array $data): \React\Promise\PromiseInterface {
        $keys = array();
        
        foreach($data as $key => $value) {
            $name = static::$schemaFieldsMapper[$this->table][$key];
            $keys[] = $this->repo->quote($name, \Plasma\DriverInterface::QUOTE_TYPE_IDENTIFIER).' = ?';
        }
        
        $uniqcol = $this->getIdentifierColumn();
        $uniqname = static::$schemaFieldsMapper[$this->table][$uniqcol];
        $uniq = $this->repo->quote($uniqcol, \Plasma\DriverInterface::QUOTE_TYPE_IDENTIFIER).' = ?';
        
        $data = \array_values($data);
        $data[] = $this->$uniqname;
        
        return $this->repo->execute(
            'UPDATE '.static::$schemaName.' SET '.\implode(', ', $keys).' WHERE '.$uniq,
            $data
        );
    }
    
    /**
     * Deletes the row. Resolves with a `QueryResultInterface` instance.
     * @return \React\Promise\PromiseInterface
     */
    function delete(): \React\Promise\PromiseInterface {
        $uniqcol = $this->getIdentifierColumn();
        $uniqname = static::$schemaFieldsMapper[$this->table][$uniqcol];
        $uniq = $this->repo->quote($uniqcol, \Plasma\DriverInterface::QUOTE_TYPE_IDENTIFIER).' = ?';
        
        return $this->repo->execute('DELETE FROM '.static::$schemaName.' WHERE '.$uniq, array($this->$uniqname));
    }
    
    /**
     * Maps data to the properties.
     * @param array  $row
     * @return void
     */
    protected function mapData(array $row): void {
        foreach($row as $key => $value) {
            $name = static::$schemaFieldsMapper[$this->table][$colname];
            $this->$name = $value;
        }
    }
    
    /**
     * Get the table identifier (databaseName_tableName)
     * @param \Plasma\QueryResultInterface  $result
     * @return string
     */
    protected function getTableIdentifier(\Plasma\QueryResultInterface $result): string {
        $field = $result->getFieldDefinitions()[0];
        return $field->getDatabaseName().'_'.$field->getTableName();
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
        
        return \str_replace('_', '', \ucwords($name, '_'));
    }
}
