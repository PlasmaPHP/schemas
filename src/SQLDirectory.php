<?php
/**
 * Plasma Schemas component
 * Copyright 2018-2019 PlasmaPHP, All Rights Reserved
 *
 * Website: https://github.com/PlasmaPHP
 * License: https://github.com/PlasmaPHP/schemas/blob/master/LICENSE
*/

namespace Plasma\Schemas;

use Plasma\Exception;
use Plasma\QueryResult;
use Plasma\QueryResultInterface;
use Plasma\SQL\GrammarInterface;
use Plasma\SQL\OnConflict;
use Plasma\SQL\QueryBuilder;
use Plasma\SQL\QueryExpressions\Parameter;
use Plasma\StatementInterface;
use Plasma\TransactionInterface;
use React\Promise\PromiseInterface;
use function React\Promise\resolve;

/**
 * This is a SQL Directory implementation.
 */
class SQLDirectory extends AbstractDirectory {
    /**
     * @var GrammarInterface
     */
    protected $grammar;
    
    /**
     * Constructor.
     * @param string                 $schema   The class name of the schema to build for. Must be a child of `SQLSchema`.
     * @param GrammarInterface|null  $grammar  The SQL grammar to use.
     * @throws Exception
     * @see \Plasma\Schemas\SQLSchema
     */
    function __construct(string $schema, ?GrammarInterface $grammar) {
        parent::__construct($schema);
        $this->grammar = $grammar;
    }
    
    /**
     * Fetch a row by the unique identifier. Resolves with an instance of `SchemaCollection`.
     * @param mixed  $value
     * @return PromiseInterface
     * @throws Exception
     */
    function fetch($value): PromiseInterface {
        $column = $this->schema::getIdentifierColumn();
        
        if($column === null) {
            throw new Exception('AbstractSchema has no unique or primary column');
        }
        
        return $this->fetchBy($column, $value);
    }
    
    /**
     * Fetch a row by the specified column. Resolves with an instance of `SchemaCollection`.
     * @param string  $name
     * @param mixed   $value
     * @return PromiseInterface
     * @throws Exception
     */
    function fetchBy(string $name, $value): PromiseInterface {
        $query = QueryBuilder::create()
            ->select()
            ->from($this->schema::getTableName())
            ->where($name, '=', $value);
        
        if($this->grammar !== null) {
            $query = $query->withGrammar($this->grammar);
        }
        
        $preloads = $this->schema::getPreloads();
        
        foreach($preloads as $preload) {
            $query->leftJoin(
                $preload->getForeignTarget()
            )->on(
                $this->schema::getTableName().'.'.$preload->getLocalKey(),
                $preload->getForeignTarget().'.'.$preload->getForeignKey()
            );
        }
        
        return $this->repo->execute(
            $query->getQuery(),
            $query->getParameters()
        )->then(function (SchemaCollection $schemaCollection) use ($preloads) {
            return $this->handlePreloadResult($schemaCollection, $preloads);
        });
    }
    
    /**
     * Fetches all rows. Resolves with an instance of `SchemaCollection`.
     * @return PromiseInterface
     * @throws Exception
     */
    function fetchAll(): PromiseInterface {
        $query = QueryBuilder::create()
            ->select()
            ->from($this->schema::getTableName());
        
        if($this->grammar !== null) {
            $query = $query->withGrammar($this->grammar);
        }
        
        $preloads = $this->schema::getPreloads();
        
        foreach($preloads as $preload) {
            $query->leftJoin(
                $preload->getForeignTarget()
            )->on(
                $this->schema::getTableName().'.'.$preload->getLocalKey(),
                $preload->getForeignTarget().'.'.$preload->getForeignKey()
            );
        }
        
        return $this->repo->execute(
            $query->getQuery(),
            array()
        )->then(function (SchemaCollection $schemaCollection) use ($preloads) {
            return $this->handlePreloadResult($schemaCollection, $preloads);
        });
    }
    
    /**
     * Inserts a row. Resolves with an instance of `SchemaCollection`, if there is a primary column. Otherwise resolves with the query result.
     * @param array  $data
     * @return PromiseInterface
     * @throws Exception
     */
    function insert(array $data): PromiseInterface {
        if(empty($data)) {
            throw new Exception('Nothing to insert, empty data set');
        }
        
        $table = $this->schema::getTableName();
        $mapper = AbstractSchema::getMapper()[$table] ?? null;
        
        if($mapper === null) {
            $this->schema::build($this->repo, $data); // Create a schema, so the mapper gets created
            $mapper = AbstractSchema::getMapper()[$table] ?? array();
        }
        
        $realValues = array();
        
        foreach($data as $colname => $value) {
            if(empty($mapper[$colname]) && !\in_array($colname, $mapper, true)) {
                throw new Exception('Unknown field "'.$colname.'"');
            }
            
            $realValues[($mapper[$colname] ?? $colname)] = $value;
        }
        
        if($this->schema::getIdentifierColumn() === null) {
            $callback = function (QueryResultInterface $result) use ($realValues) {
                return (new SchemaCollection(array($this->schema::build($this->repo, $realValues)), $result));
            };
        } elseif(\count($data) >= \count($this->schema::getDefinition()) - 1) {
            $callback = function (QueryResultInterface $result) use ($realValues) {
                if($result->getInsertID() !== null) {
                    $realValues[$this->schema::getIdentifierColumn()] = $result->getInsertID();
                }
                
                return (new SchemaCollection(array($this->schema::build($this->repo, $realValues)), $result));
            };
        } else {
            // If not all columns were filled by the user, we fetch the row from the DB instead
            // but only if it has an inserted ID, otherwise we just build the schema as is
            $callback = function (QueryResultInterface $result) use ($realValues) {
                if($result->getInsertID() !== null) {
                    return $this->fetch($result->getInsertID());
                }
                
                return (new SchemaCollection(array($this->schema::build($this->repo, $realValues)), $result));
            };
        }
        
        $query = QueryBuilder::create()
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
     * @return PromiseInterface
     * @throws \InvalidArgumentException
     * @throws Exception
     */
    function insertAll(array $data, array $options = array()): PromiseInterface {
        $table = $this->schema::getTableName();
        
        $params = array();
        $columns = \array_reduce($data, static function ($carry, $item) {
            $c = \count($item);
            $d = \count($carry);
            
            if($c > $d) {
                return $item;
            }
            
            return $carry;
        }, array());
        
        foreach($columns as $column => $_) {
            $params[$column] = new Parameter();
        }
        
        if(!empty($options['conflictResolution'])) {
            $onConflict = $options['conflictResolution'];
        } elseif(($options['ignoreConflict'] ?? false) === true) {
            $onConflict = new OnConflict(OnConflict::RESOLUTION_DO_NOTHING);
        } else {
            $onConflict = null;
        }
        
        if(!isset($options['transactionIsolation'])) {
            $options['transactionIsolation'] = TransactionInterface::ISOLATION_COMMITTED;
        }
        
        $query = QueryBuilder::create();
        
        if($this->grammar !== null) {
            $query = $query->withGrammar($this->grammar);
        }
        
        $query
            ->insert($params, array(
                'onConflict' => $onConflict
            ))
            ->into($table);
        
        return $this->repo->getClient()
            ->beginTransaction($options['transactionIsolation'])
            ->then(function (TransactionInterface $transaction) use ($data, $params, $query) {
                return $transaction
                    ->prepare($query->getQuery())
                    ->then(function (StatementInterface $stmt) use ($data, $params, $query) {
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
     * @return PromiseInterface
     * @throws Exception
     */
    function update(array $data, string $field, $value): PromiseInterface {
        $query = QueryBuilder::create()
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
     * @return PromiseInterface
     * @throws Exception
     */
    function delete($value): PromiseInterface {
        $column = $this->schema::getIdentifierColumn();
        
        if($column === null) {
            throw new Exception('AbstractSchema has no unique or primary column');
        }
        
        return $this->deleteBy($column, $value);
    }
    
    /**
     * Deletes a row by the specified column. Resolves with a `QueryResultInterface` instance.
     * @param string  $name
     * @param mixed   $value
     * @return PromiseInterface
     * @throws Exception
     */
    function deleteBy(string $name, $value): PromiseInterface {
        $query = QueryBuilder::create()
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
     * Handles a preload result.
     * @param SchemaCollection    $schemaCollection
     * @param PreloadInterface[]  $preloads
     * @return SchemaCollection
     * @throws Exception
     */
    protected function handlePreloadResult(SchemaCollection $schemaCollection, array $preloads): SchemaCollection {
        if(empty($preloads)) {
            return $schemaCollection;
        }
        
        $result = $schemaCollection->getResult();
        $schemas = $schemaCollection->getSchemas();
        
        $affRows = $result->getAffectedRows();
        $warnings = $result->getWarningsCount();
        $fields = $result->getFieldDefinitions();
        $rows = $result->getRows();
        
        $i = 0;
        foreach($schemas as $schema) {
            $sResult = new QueryResult(
                $affRows,
                $warnings,
                null,
                $fields,
                array($rows[$i++])
            );
            
            $schema->afterPreloadHook($sResult, $preloads);
            
            if($schema instanceof AbstractSchema) {
                $schema->validateData();
            }
        }
        
        return $schemaCollection;
    }
    
    /**
     * Executes the next set of the rows to be inserted.
     * @param QueryBuilder               $query
     * @param StatementInterface         $stmt
     * @param array                      $rows
     * @param Parameter[]                $params
     * @param array                      $insertedRows
     * @param QueryResultInterface|null  $result
     * @return PromiseInterface
     * @throws Exception
     */
    protected function executeNextRowInsert(
        QueryBuilder $query,
        StatementInterface $stmt,
        array $rows,
        array $params,
        array $insertedRows,
        ?QueryResultInterface $result
    ): PromiseInterface {
        $data = \array_shift($rows);
        
        if($data === null) {
            $result2 = new QueryResult(\count($insertedRows),
                0,
                null,
                ($result ? $result->getFieldDefinitions() : null),
                null
            );
            
            return resolve((new SchemaCollection($insertedRows, $result2)));
        }
        
        foreach($params as $par) {
            $par->setValue(null);
        }
        
        foreach($data as $col => $val) {
            $params[$col]->setValue($val);
        }
        
        if($this->schema::getIdentifierColumn() === null) {
            $callback = function (QueryResultInterface $result) use ($data) {
                return (new SchemaCollection(array($this->schema::build($this->repo, $data)), $result));
            };
        } elseif(\count($data) >= \count($this->schema::getDefinition()) - 1) {
            $callback = function (QueryResultInterface $result) use ($data) {
                if($result->getInsertID() !== null) {
                    $data[$this->schema::getIdentifierColumn()] = $result->getInsertID();
                }
                
                return (new SchemaCollection(array($this->schema::build($this->repo, $data)), $result));
            };
        } else {
            // If not all columns were filled by the user, we fetch the row from the DB instead
            // but only if it has an inserted ID, otherwise we just build the schema as is
            $callback = function (QueryResultInterface $result) use ($data) {
                if($result->getInsertID() !== null) {
                    return $this->fetch($result->getInsertID());
                }
                
                return (new SchemaCollection(array($this->schema::build($this->repo, $data)), $result));
            };
        }
        
        return $stmt->execute($query->getParameters())
            ->then($callback)
            ->then(function (SchemaCollection $result) use ($query, $stmt, $rows, $params, $insertedRows) {
                $qr = $result->getResult();
                $irows = $result->getSchemas();
                
                return $this->executeNextRowInsert($query, $stmt, $rows, $params, \array_merge($insertedRows, $irows), $qr);
            });
    }
}
