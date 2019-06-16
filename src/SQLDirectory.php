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
 * This is a SQL Directory implementation.
 */
class SQLDirectory extends AbstractDirectory {
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
    function __construct(string $schema, ?\Plasma\SQL\GrammarInterface $grammar) {
        parent::__construct($schema);
        $this->grammar = $grammar;
    }
    
    /**
     * Fetch a row by the unique identifier. Resolves with an instance of `SchemaCollection`.
     * @param mixed  $value
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    function fetch($value): \React\Promise\PromiseInterface {
        $column = $this->schema::getIdentifierColumn();
        
        if($column === null) {
            throw new \Plasma\Exception('AbstractSchema has no unique or primary column');
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
        $query = \Plasma\SQL\QueryBuilder::create()
            ->select()
            ->from($this->schema::getTableName())
            ->where($name, '=', $value);
        
        if($this->grammar !== null) {
            $query = $query->withGrammar($this->grammar);
        }
        
        return $this->repo->execute(
            $query->getQuery(),
            $query->getParameters()
        );
    }
    
    /**
     * Fetches all rows. Resolves with an instance of `SchemaCollection`.
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    function fetchAll(): \React\Promise\PromiseInterface {
        $query = \Plasma\SQL\QueryBuilder::create()
            ->select()
            ->from($this->schema::getTableName());
        
        if($this->grammar !== null) {
            $query = $query->withGrammar($this->grammar);
        }
        
        return $this->repo->execute(
            $query->getQuery(),
            array()
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
        
        $table = $this->schema::getTableName();
        $mapper = \Plasma\Schemas\AbstractSchema::getMapper()[$table] ?? null;
        
        if($mapper === null) {
            $this->schema::build($this->repo, $data); // Create a schema, so the mapper gets created
            $mapper = \Plasma\Schemas\AbstractSchema::getMapper()[$table] ?? array();
        }
        
        $realValues = array();
        
        foreach($data as $colname => $value) {
            if(empty($mapper[$colname]) && !\array_search($colname, $mapper)) {
                throw new \Plasma\Exception('Unknown field "'.$colname.'"');
            }
            
            $realValues[($mapper[$colname] ?? $colname)] = $value;
        }
        
        if($this->schema::getIdentifierColumn() === null) {
            $callback = function (\Plasma\QueryResultInterface $result) use ($realValues) {
                return (new \Plasma\Schemas\SchemaCollection(array($this->schema::build($this->repo, $realValues)), $result));
            };
        } elseif(\count($data) >= \count($this->schema::getDefinition()) - 1) {
            $callback = function (\Plasma\QueryResultInterface $result) use ($realValues) {
                if($result->getInsertID() !== null) {
                    $realValues[$this->schema::getIdentifierColumn()] = $result->getInsertID();
                }
                
                return (new \Plasma\Schemas\SchemaCollection(array($this->schema::build($this->repo, $realValues)), $result));
            };
        } else {
            // If not all columns were filled by the user, we fetch the row from the DB instead
            // but only if it has an inserted ID, otherwise we just build the schema as is
            $callback = function (\Plasma\QueryResultInterface $result) use ($realValues) {
                if($result->getInsertID() !== null) {
                    return $this->fetch($result->getInsertID());
                }
                
                return (new \Plasma\Schemas\SchemaCollection(array($this->schema::build($this->repo, $realValues)), $result));
            };
        }
        
        $query = \Plasma\SQL\QueryBuilder::create()
            ->insert($realValues)
            ->into($table);
        
        if($this->grammar !== null) {
            $query = $query->withGrammar($this->grammar);
        }
        
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
     *     'conflictResolution' => \Plasma\AbstractSchema\OnConflict, (a conflict resolution strategy, this option takes precedence)
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
        $table = $this->schema::getTableName();
        
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
            ->insert($params, array(
                'onConflict' => $onConflict
            ))
            ->into($table);
        
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
     * Updates the row with the given data, identified by a specific field.
     * @param array   $data
     * @param string  $field
     * @param mixed   $value
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    function update(array $data, string $field, $value): \React\Promise\PromiseInterface {
        $query = \Plasma\SQL\QueryBuilder::create()
            ->update($data)
            ->from($this->schema::getTableName())
            ->where($field, '=', $value);
        
        if($this->grammar !== null) {
            $query = $query->withGrammar($this->grammar);
        }
        
        return $this->repo->execute(
            $query->getQuery(),
            $query->getParameters()
        )->then(function () use ($field, $value) {
            return $this->fetchBy($field, $value);
        });
    }
    
    /**
     * Deletes a row by the unique identifier. Resolves with a `QueryResultInterface` instance.
     * @param mixed   $value
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    function delete($value): \React\Promise\PromiseInterface {
        $column = $this->schema::getIdentifierColumn();
        
        if($column === null) {
            throw new \Plasma\Exception('AbstractSchema has no unique or primary column');
        }
        
        return $this->deleteBy($column, $value);
    }
    
    /**
     * Deletes a row by the specified column. Resolves with a `QueryResultInterface` instance.
     * @param string  $name
     * @param mixed   $value
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    function deleteBy(string $name, $value): \React\Promise\PromiseInterface {
        $query = \Plasma\SQL\QueryBuilder::create()
            ->delete()
            ->from($this->schema::getTableName())
            ->where($name, '=', $value);
        
        if($this->grammar !== null) {
            $query = $query->withGrammar($this->grammar);
        }
        
        return $this->repo->execute(
            $query->getQuery(),
            $query->getParameters()
        );
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
        
        if($this->schema::getIdentifierColumn() === null) {
            $callback = function (\Plasma\QueryResultInterface $result) use ($data) {
                return (new \Plasma\Schemas\SchemaCollection(array($this->schema::build($this->repo, $data)), $result));
            };
        } elseif(\count($data) >= \count($this->schema::getDefinition()) - 1) {
            $callback = function (\Plasma\QueryResultInterface $result) use ($data) {
                if($result->getInsertID() !== null) {
                    $data[$this->schema::getIdentifierColumn()] = $result->getInsertID();
                }
                
                return (new \Plasma\Schemas\SchemaCollection(array($this->schema::build($this->repo, $data)), $result));
            };
        } else {
            // If not all columns were filled by the user, we fetch the row from the DB instead
            // but only if it has an inserted ID, otherwise we just build the schema as is
            $callback = function (\Plasma\QueryResultInterface $result) use ($data) {
                if($result->getInsertID() !== null) {
                    return $this->fetch($result->getInsertID());
                }
                
                return (new \Plasma\Schemas\SchemaCollection(array($this->schema::build($this->repo, $data)), $result));
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
