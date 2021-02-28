<?php
/**
 * Plasma Schemas component
 * Copyright 2018-2019 PlasmaPHP, All Rights Reserved
 *
 * Website: https://github.com/PlasmaPHP
 * License: https://github.com/PlasmaPHP/schemas/blob/master/LICENSE
*/

namespace Plasma\Schemas;

use Plasma\DriverInterface;
use Plasma\Exception;
use Plasma\QueryBuilderInterface;
use Plasma\StatementInterface;
use Plasma\TransactionException;
use Plasma\TransactionInterface;
use React\Promise\PromiseInterface;

/**
 * Represents a Transaction. This class wraps a Transaction.
 */
class Transaction implements TransactionInterface {
    /**
     * @var Repository
     */
    protected $repo;
    
    /**
     * @var TransactionInterface
     */
    protected $transaction;
    
    /**
     * Constructor.
     * @param Repository            $repo
     * @param TransactionInterface  $transaction
     */
    function __construct(Repository $repo, TransactionInterface $transaction) {
        $this->repo = $repo;
        $this->transaction = $transaction;
    }
    
    /**
     * Destructor.
     * @return void
     */
    function __destruct() {
        $this->transaction = null;
    }
    
    /**
     * Get the isolation level for this transaction.
     * @return int
     */
    function getIsolationLevel(): int {
        return $this->transaction->getIsolationLevel();
    }
    
    /**
     * Whether the transaction is still active, or has been committed/rolled back.
     * @return bool
     */
    function isActive(): bool {
        return $this->transaction->isActive();
    }
    
    /**
     * Executes a plain query. Resolves with a `QueryResult` instance.
     * @param string  $query
     * @return PromiseInterface
     * @throws Exception
     * @see \Plasma\QueryResultInterface
     */
    function query(string $query): PromiseInterface {
        return $this->transaction->query($query)->then(array($this->repo, 'handleQueryResult'));
    }
    
    /**
     * Prepares a query. Resolves with a `StatementInterface` instance.
     * @param string  $query
     * @return PromiseInterface
     * @throws Exception
     * @see \Plasma\StatementInterface
     */
    function prepare(string $query): PromiseInterface {
        return $this->transaction->prepare($query)->then(function (StatementInterface $stmt) {
            return (new Statement($this->repo, $stmt));
        });
    }
    
    /**
     * Prepares and executes a query. Resolves with a `QueryResultInterface` instance.
     * This is equivalent to prepare -> execute -> close.
     * If you need to execute a query multiple times, prepare the query manually for performance reasons.
     * @param string  $query
     * @param array   $params
     * @return PromiseInterface
     * @throws Exception
     * @see \Plasma\StatementInterface
     */
    function execute(string $query, array $params = array()): PromiseInterface {
        return $this->transaction->execute($query, $params)->then(array($this->repo, 'handleQueryResult'));
    }
    
    /**
     * Quotes the string for use in the query.
     * @param string  $str
     * @param int     $type  For types, see the driver interface constants.
     * @return string
     * @throws \LogicException  Thrown if the driver does not support quoting.
     * @throws Exception
     */
    function quote(string $str, int $type = DriverInterface::QUOTE_TYPE_VALUE): string {
        return $this->transaction->quote($str, $type);
    }

    /**
     * Runs the given querybuilder on the underlying driver instance.
     * The driver CAN throw an exception if the given querybuilder is not supported.
     * An example would be a SQL querybuilder and a Cassandra driver.
     * @param QueryBuilderInterface  $query
     * @return PromiseInterface
     * @throws Exception
     */
    function runQuery(QueryBuilderInterface $query): PromiseInterface {
        return $this->transaction->runQuery($query)->then(array($this->repo, 'handleQueryResult'));
    }
    
    /**
     * Commits the changes.
     * @return PromiseInterface
     * @throws TransactionException  Thrown if the transaction has been committed or rolled back.
     */
    function commit(): PromiseInterface {
        return $this->transaction->commit();
    }
    
    /**
     * Rolls back the changes.
     * @return PromiseInterface
     * @throws TransactionException  Thrown if the transaction has been committed or rolled back.
     */
    function rollback(): PromiseInterface {
        return $this->transaction->rollback();
    }
    
    /**
     * Creates a savepoint with the given identifier.
     * @param string  $identifier
     * @return PromiseInterface
     * @throws TransactionException  Thrown if the transaction has been committed or rolled back.
     */
    function createSavepoint(string $identifier): PromiseInterface {
        return $this->transaction->createSavepoint($identifier);
    }
    
    /**
     * Rolls back to the savepoint with the given identifier.
     * @param string  $identifier
     * @return PromiseInterface
     * @throws TransactionException  Thrown if the transaction has been committed or rolled back.
     */
    function rollbackTo(string $identifier): PromiseInterface {
        return $this->transaction->rollbackTo($identifier);
    }
    
    /**
     * Releases the savepoint with the given identifier.
     * @param string  $identifier
     * @return PromiseInterface
     * @throws TransactionException  Thrown if the transaction has been committed or rolled back.
     */
    function releaseSavepoint(string $identifier): PromiseInterface {
        return $this->transaction->releaseSavepoint($identifier);
    }
}
