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
 * Represents a Transaction. This class wraps a Transaction.
 */
class Transaction implements \Plasma\TransactionInterface {
    /**
     * @var \Plasma\Schemas\Repository
     */
    protected $repo;
    
    /**
     * @var \Plasma\TransactionInterface
     */
    protected $transaction;
    
    /**
     * Constructor.
     * @param \Plasma\Schemas\Repository  $repo
     * @param \Plasma\TransactionInterface  $transaction
     */
    function __construct(\Plasma\Schemas\Repository $repo, \Plasma\TransactionInterface $transaction) {
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
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\TransactionException  Thrown if the transaction has been committed or rolled back.
     * @see \Plasma\QueryResultInterface
     */
    function query(string $query): \React\Promise\PromiseInterface {
        return $this->transaction->query($query)->then(array($this->repo, 'handleQueryResult'));
    }

    /**
     * Prepares a query. Resolves with a `StatementInterface` instance.
     * @param string  $query
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\TransactionException  Thrown if the transaction has been committed or rolled back.
     * @see \Plasma\StatementInterface
     */
    function prepare(string $query): \React\Promise\PromiseInterface {
        return $this->transaction->prepare($query)->then(function (\Plasma\StatementInterface $stmt) {
            return (new \Plasma\Schemas\Statement($this->repo, $stmt));
        });
    }

    /**
     * Prepares and executes a query. Resolves with a `QueryResultInterface` instance.
     * This is equivalent to prepare -> execute -> close.
     * If you need to execute a query multiple times, prepare the query manually for performance reasons.
     * @param string  $query
     * @param array   $params
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\TransactionException  Thrown if the transaction has been committed or rolled back.
     * @throws \Plasma\Exception
     * @see \Plasma\StatementInterface
     */
    function execute(string $query, array $params = array()): \React\Promise\PromiseInterface {
        return $this->transaction->execute($query, $params)->then(array($this->repo, 'handleQueryResult'));
    }
    
    /**
     * Quotes the string for use in the query.
     * @param string  $str
     * @param int     $type  For types, see the driver interface constants.
     * @return string
     * @throws \LogicException               Thrown if the driver does not support quoting.
     * @throws \Plasma\TransactionException  Thrown if the transaction has been committed or rolled back.
     */
    function quote(string $str, int $type = \Plasma\DriverInterface::QUOTE_TYPE_VALUE): string {
        return $this->transaction->quote($str, $type);
    }

    /**
     * Runs the given querybuilder on the underlying driver instance.
     * The driver CAN throw an exception if the given querybuilder is not supported.
     * An example would be a SQL querybuilder and a Cassandra driver.
     * @param \Plasma\QueryBuilderInterface  $query
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    function runQuery(\Plasma\QueryBuilderInterface $query): \React\Promise\PromiseInterface {
        return $this->transaction->runQuery($query)->then(array($this->repo, 'handleQueryResult'));
    }
    
    /**
     * Commits the changes.
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\TransactionException  Thrown if the transaction has been committed or rolled back.
     */
    function commit(): \React\Promise\PromiseInterface {
        return $this->transaction->commit();
    }
    
    /**
     * Rolls back the changes.
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\TransactionException  Thrown if the transaction has been committed or rolled back.
     */
    function rollback(): \React\Promise\PromiseInterface {
        return $this->transaction->rollback();
    }
    
    /**
     * Creates a savepoint with the given identifier.
     * @param string  $identifier
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\TransactionException  Thrown if the transaction has been committed or rolled back.
     */
    function createSavepoint(string $identifier): \React\Promise\PromiseInterface {
        return $this->transaction->createSavepoint($identifier);
    }
    
    /**
     * Rolls back to the savepoint with the given identifier.
     * @param string  $identifier
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\TransactionException  Thrown if the transaction has been committed or rolled back.
     */
    function rollbackTo(string $identifier): \React\Promise\PromiseInterface {
        return $this->transaction->rollbackTo($identifier);
    }
    
    /**
     * Releases the savepoint with the given identifier.
     * @param string  $identifier
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\TransactionException  Thrown if the transaction has been committed or rolled back.
     */
    function releaseSavepoint(string $identifier): \React\Promise\PromiseInterface {
        return $this->transaction->releaseSavepoint($identifier);
    }
}
