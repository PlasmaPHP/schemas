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
 * This is a SQL Schema Builder implementation.
 */
class SQLSchemaBuilder implements SchemaBuilderInterface {
    /**
     * @var \Plasma\Schemas\Repository
     */
    protected $repo;
    
    /**
     * @var \Plasma\Schemas\SchemaInterface
     */
    protected $schema;
    
    /**
     * Constructor.
     * @param string  $schema  The class name of the schema to build for.
     * @throws \Plasma\Exception
     */
    function __construct(string $schema) {
        if(!\class_exists($schema, true)) {
            throw new \Plasma\Exception('Schema class does not exist');
        } elseif(!\in_array(\Plasma\Schemas\SchemaInterface::class, \class_implements($schema))) {
            throw new \Plasma\Exception('Schema class does not implement Schema Interface');
        }
        
        $this->schema = $schema;
    }
    
    /**
     * Gets the repository.
     * @return \Plasma\Schemas\Repository
     */
    function getRepository(): \Plasma\Schemas\Repository {
        return $this->repo;
    }
    
    /**
     * Sets the repository to use.
     * @param \Plasma\Schemas\Repository  $repository
     * @return void
     */
    function setRepository(\Plasma\Schemas\Repository $repository): void {
        $this->repo = $repository;
    }
    
    /**
     * Fetch a row by the unique identifier. Resolves with an instance of `SchemaCollection`.
     * @param mixed  $value
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    function fetch($value): \React\Promise\PromiseInterface {
        $schema = $this->schema;
        $column = $schema::getIdentifierColumn();
        
        if($column === null) {
            throw new \Plasma\Exception('Schema has no unique or primary column');
        }
        
        return $this->fetchBy($column, $value);
    }
    
    /**
     * Fetch a row by the specified column. Resolves with an instance of `SchemaCollection`.
     * @param string  $name
     * @param mixed   $value
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    function fetchBy(string $name, $value): \React\Promise\PromiseInterface {
        $schema = $this->schema;
        
        $table = $this->repo->quote($schema::getTableName(), \Plasma\DriverInterface::QUOTE_TYPE_IDENTIFIER);
        $uniq = $this->repo->quote($name, \Plasma\DriverInterface::QUOTE_TYPE_IDENTIFIER);
        
        return $this->repo->execute(
            'SELECT * FROM '.$table.' WHERE '.$uniq.' = ?',
            array($value)
        );
    }
    
    /**
     * Inserts a row. Resolves with an instance of `SchemaCollection`, if there is a primary column. Otherwise resolves with the query result.
     * @param array  $data
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    function insert(array $data): \React\Promise\PromiseInterface {
        if(empty($data)) {
            throw new \Plasma\Exception('Nothing to insert, empty data set');
        }
        
        $schema = $this->schema;
        
        $table = $schema::getTableName();
        $mapper = \Plasma\Schemas\Schema::getMapper()[$table] ?? null;
        
        if($mapper === null) {
            $schema::build($this->repo, $data); // Create a schema, so the mapper gets created
            $mapper = \Plasma\Schemas\Schema::getMapper()[$table] ?? array();
        }
        
        $realValues = array();
        $fields = array();
        $values = array();
        
        foreach($data as $colname => $value) {
            if(empty($mapper[$colname])) {
                throw new \Plasma\Exception('Unknown field "'.$colname.'"');
            }
            
            $realValues[$mapper[$colname]] = $value;
            $fields[] = $this->repo->quote($mapper[$colname], \Plasma\DriverInterface::QUOTE_TYPE_IDENTIFIER);
            $values[] = '?';
        }
        
        $table = $this->repo->quote($table, \Plasma\DriverInterface::QUOTE_TYPE_IDENTIFIER);
        
        if($schema::getIdentifierColumn() === null) {
            $callback = function (\Plasma\QueryResultInterface $result) {
                return $result;
            };
        } elseif(\count($data) === \count($schema::getDefinition()) - 1) {
            $callback = function (\Plasma\QueryResultInterface $result) use ($realValues, $schema) {
                if($result->getInsertID() !== null) {
                    $realValues[$schema::getIdentifierColumn()] = $result->getInsertID();
                }
                
                return (new \Plasma\Schemas\SchemaCollection(array($schema::build($this->repo, $realValues)), $result));
            };
        } else {
            // If not all columns were filled by the user, we fetch the row from the DB instead
            $callback = function (\Plasma\QueryResultInterface $result) {
                if($result->getInsertID() !== null) {
                    return $this->fetch($result->getInsertID());
                }
                
                return $result;
            };
        }
        
        $realValues = null;
        
        return $this->repo->execute(
            'INSERT INTO '.$table.' ('.\implode(', ', $fields).') VALUES ('.\implode(', ', $values).')',
            \array_values($data)
        )->then($callback);
    }
    
    /**
     * Builds schemas for the given SELECT query result.
     * @param \Plasma\QueryResult  $result
     * @return \Plasma\Schemas\SchemaCollection
     */
    function buildSchemas(\Plasma\QueryResult $result): \Plasma\Schemas\SchemaCollection {
        $schemas = array();
        $schema = $this->schema;
        
        $rows = (array) $result->getRows();
        foreach($rows as $row) {
            $schemas[] = $schema::build($this->repo, $row);
        }
        
        return (new \Plasma\Schemas\SchemaCollection($schemas, $result));
    }
}
