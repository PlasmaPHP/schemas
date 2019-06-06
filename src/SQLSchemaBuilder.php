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
     * @var \Plasma\SQL\GrammarInterface
     */
    protected $grammar;
    
    /**
     * Constructor.
     * @param string                             $schema   The class name of the schema to build for.
     * @param \Plasma\SQL\GrammarInterface|null  $grammar  The SQL grammar to use.
     * @throws \Plasma\Exception
     */
    function __construct(string $schema, ?\Plasma\SQL\GrammarInterface $grammar = null) {
        if(!\class_exists($schema, true)) {
            throw new \Plasma\Exception('Schema class does not exist');
        } elseif(!\in_array(\Plasma\Schemas\SchemaInterface::class, \class_implements($schema))) {
            throw new \Plasma\Exception('Schema class does not implement Schema Interface');
        }
        
        $this->schema = $schema;
        $this->grammar = $grammar;
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
     * Fetches all rows. Resolves with an instance of `SchemaCollection`.
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    function fetchAll(): \React\Promise\PromiseInterface {
        $schema = $this->schema;
        $table = $this->repo->quote($schema::getTableName(), \Plasma\DriverInterface::QUOTE_TYPE_IDENTIFIER);
        
        return $this->repo->execute('SELECT * FROM '.$table, array());
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
            if(empty($mapper[$colname]) && !\array_search($colname, $mapper)) {
                throw new \Plasma\Exception('Unknown field "'.$colname.'"');
            }
            
            $realValues[($mapper[$colname] ?? $colname)] = $value;
        }
        
        if($schema::getIdentifierColumn() === null) {
            $callback = function (\Plasma\QueryResultInterface $result) use ($realValues, $schema) {
                return (new \Plasma\Schemas\SchemaCollection(array($schema::build($this->repo, $realValues)), $result));
            };
        } elseif(\count($data) >= \count($schema::getDefinition()) - 1) {
            $callback = function (\Plasma\QueryResultInterface $result) use ($realValues, $schema) {
                if($result->getInsertID() !== null) {
                    $realValues[$schema::getIdentifierColumn()] = $result->getInsertID();
                }
                
                return (new \Plasma\Schemas\SchemaCollection(array($schema::build($this->repo, $realValues)), $result));
            };
        } else {
            // If not all columns were filled by the user, we fetch the row from the DB instead
            // but only if it has an inserted ID, otherwise we just build the schema as is
            $callback = function (\Plasma\QueryResultInterface $result) use ($realValues, $schema) {
                if($result->getInsertID() !== null) {
                    return $this->fetch($result->getInsertID());
                }
                
                return (new \Plasma\Schemas\SchemaCollection(array($schema::build($this->repo, $realValues)), $result));
            };
        }
        
        $query = \Plasma\SQL\QueryBuilder::create()
             ->into($table)
             ->insert($realValues);
        
        if($this->grammar !== null) {
            $query = $query->withGrammar($this->grammar);
        }
        
        $realValues = null;
        
        return $this->repo->execute(
            $query->getQuery(),
            $query->getParameters()
        )->then($callback);
    }
    
    /**
     * Inserts a list of rows. Resolves with an instance of `SchemaCollection`.
     * All inserts get wrapped into a transaction.
     *
     * Options is an optional array, which supports these options:
     * ```
     * array(
     *     'ignoreConflict' => bool, (whether duplicate key conflicts get ignored, defaults to false)
     *     'conflictResolution' => \Plasma\Schema\OnConflict, (a conflict resolution strategy, this option takes precedence)
     *     'transactionIsolation' => int, (a transaction isolation level from the TransactionInterface constants, defaults to ISOLATION_COMMITTED)
     * )
     * ```
     *
     * This method has a dependency on the `plasma/sql-common` package.
     *
     * @param array  $data
     * @param array  $options
     * @return \React\Promise\PromiseInterface
     * @throws \InvalidArgumentException
     * @throws \Plasma\Exception
     */
    function insertAll(array $data, array $options = array()): \React\Promise\PromiseInterface {
        $schema = $this->schema;
        $table = $schema::getTableName();
        
        $params = array();
        $columns = \array_reduce($data, function ($carry, $item) {
            $c = \count($item);
            $d = \count($carry);
            
            if($c > $d) {
                return $item;
            }
            
            return $carry;
        }, array());
        
        foreach($columns as $column => $_) {
            $params[$column] = new \Plasma\SQL\QueryExpressions\Parameter();
        }
        
        if(!empty($options['conflictResolution'])) {
            $onConflict = $options['conflictResolution'];
        } elseif(($options['ignoreConflict'] ?? false) === true) {
            $onConflict = new \Plasma\SQL\OnConflict(\Plasma\SQL\OnConflict::RESOLUTION_DO_NOTHING);
        } else {
            $onConflict = null;
        }
        
        if(!isset($options['transactionIsolation'])) {
            $options['transactionIsolation'] = \Plasma\TransactionInterface::ISOLATION_COMMITTED;
        }
        
        $query = \Plasma\SQL\QueryBuilder::create()
            ->into($table)
            ->insert($params, array(
                'onConflict' => $onConflict
            ));
        
        if($this->grammar !== null) {
            $query = $query->withGrammar($this->grammar);
        }
        
        return $this->repo->getClient()
            ->beginTransaction($options['transactionIsolation'])
            ->then(function (\Plasma\TransactionInterface $transaction) use ($data, $params, $query) {
                return $transaction
                    ->prepare($query->getQuery())
                    ->then(function (\Plasma\StatementInterface $stmt) use ($data, $params, $query) {
                        return $this->executeNextRowInsert($query, $stmt, $data, $params, array(), null);
                    })
                    ->then(function ($result) use ($transaction) {
                        return $transaction->commit()->then(function () use ($result) {
                            return $result;
                        });
                    }, function (\Throwable $error) use ($transaction) {
                        $transaction->rollback();
                        throw $error;
                    });
            });
    }
    
    /**
     * Builds schemas for the given SELECT query result.
     * @param \Plasma\QueryResultInterface  $result
     * @return \Plasma\Schemas\SchemaCollection
     * @throws \Plasma\Exception
     */
    function buildSchemas(\Plasma\QueryResultInterface $result): \Plasma\Schemas\SchemaCollection {
        $schemas = array();
        $schema = $this->schema;
        
        $rows = (array) $result->getRows();
        foreach($rows as $row) {
            $schemas[] = $schema::build($this->repo, $row);
        }
        
        return (new \Plasma\Schemas\SchemaCollection($schemas, $result));
    }
    
    /**
     * Executes the next set of the rows to be inserted.
     * @param \Plasma\SQL\QueryBuilder                  $query
     * @param \Plasma\StatementInterface                $stmt
     * @param array                                     $rows
     * @param \Plasma\SQL\QueryExpressions\Parameter[]  $params
     * @param array                                     $insertedRows
     * @param \Plasma\QueryResultInterface|null         $result
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    protected function executeNextRowInsert(
        \Plasma\SQL\QueryBuilder $query,
        \Plasma\StatementInterface $stmt,
        array $rows,
        array $params,
        array $insertedRows,
        ?\Plasma\QueryResultInterface $result
    ): \React\Promise\PromiseInterface {
        $schema = $this->schema;
        $data = \array_shift($rows);
        
        if($data === null) {
            $result = new \Plasma\QueryResult(\count($insertedRows), 0, null, $result->getFieldDefinitions(), null);
            return \React\Promise\resolve((new \Plasma\Schemas\SchemaCollection($insertedRows, $result)));
        }
        
        foreach($params as $par) {
            $par->setValue(null);
        }
        
        foreach($data as $col => $val) {
            $params[$col]->setValue($val);
        }
        
        if($schema::getIdentifierColumn() === null) {
            $callback = function (\Plasma\QueryResultInterface $result) use ($data, $schema) {
                return (new \Plasma\Schemas\SchemaCollection(array($schema::build($this->repo, $data)), $result));
            };
        } elseif(\count($data) >= \count($schema::getDefinition()) - 1) {
            $callback = function (\Plasma\QueryResultInterface $result) use ($data, $schema) {
                if($result->getInsertID() !== null) {
                    $data[$schema::getIdentifierColumn()] = $result->getInsertID();
                }
                
                return (new \Plasma\Schemas\SchemaCollection(array($schema::build($this->repo, $data)), $result));
            };
        } else {
            // If not all columns were filled by the user, we fetch the row from the DB instead
            // but only if it has an inserted ID, otherwise we just build the schema as is
            $callback = function (\Plasma\QueryResultInterface $result) use ($data, $schema) {
                if($result->getInsertID() !== null) {
                    return $this->fetch($result->getInsertID());
                }
                
                return (new \Plasma\Schemas\SchemaCollection(array($schema::build($this->repo, $data)), $result));
            };
        }
        
        return $stmt->execute($query->getParameters())
            ->then($callback)
            ->then(function (\Plasma\Schemas\SchemaCollection $result) use ($query, $stmt, $rows, $params, $insertedRows) {
                $qr = $result->getResult();
                $irows = $result->getSchemas();
                
                return $this->executeNextRowInsert($query, $stmt, $rows, $params, \array_merge($insertedRows, $irows), $qr);
            });
    }
}
