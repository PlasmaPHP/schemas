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
 * The repository is responsible for turning row results into specified PHP object.
 *
 * The repository uses internally an actual plasma client, itself has no client implementation.
 * Not implementing the interface prevents bugs (expecting a query result but getting a schema collection).
 *
 * SELECT queries will be wrapped (if a schema builder exists for the table) within a `SchemaCollection`.
 * All other queries get returned as is.
 */
class Repository implements RepositoryInterface {
    /**
     * @var \Plasma\ClientInterface
     */
    protected $client;
    
    /**
     * @var \Plasma\Schemas\DirectoryInterface[]
     */
    protected $builders = array();
    
    /**
     * Constructor.
     * @param \Plasma\ClientInterface  $client
     */
    function __construct(\Plasma\ClientInterface $client) {
        $this->client = $client;
    }
    
    /**
     * Get the internally used client.
     * @return \Plasma\ClientInterface
     */
    function getClient(): \Plasma\ClientInterface {
        return $this->client;
    }
    
    /**
     * Get the directory for the schema.
     * @param string  $schemaName  The schema name. This would be the table name.
     * @return \Plasma\Schemas\DirectoryInterface
     * @throws \Plasma\Exception
     */
    function getDirectory(string $schemaName): \Plasma\Schemas\DirectoryInterface {
        if(!isset($this->builders[$schemaName])) {
            throw new \Plasma\Exception('The schema is not registered');
        }
        
        return $this->builders[$schemaName];
    }
    
    /**
     * Register a directory for the schema to be used by the repository.
     * @param string                              $schemaName  The schema name. This would be the table name.
     * @param \Plasma\Schemas\DirectoryInterface  $directory   The directory for the schema.
     * @return $this
     * @throws \Plasma\Exception
     */
    function registerDirectory(string $schemaName, \Plasma\Schemas\DirectoryInterface $directory) {
        if(isset($this->builders[$schemaName])) {
            throw new \Plasma\Exception('The schema is already registered');
        }
    
        $directory->setRepository($this);
        $this->builders[$schemaName] = $directory;
        
        return $this;
    }
    
    /**
     * Unregister the directory of the schema.
     * @param string  $schemaName  The schema name. This would be the table name.
     * @return $this
     */
    function unregisterDirectory(string $schemaName) {
        unset($this->builders[$schemaName]);
        return $this;
    }
    
    /**
     * Get the amount of connections.
     * @return int
     */
    function getConnectionCount(): int {
        return $this->client->getConnectionCount();
    }
    
    /**
     * Checks a connection back in, if usable and not closing.
     * @param \Plasma\DriverInterface  $driver
     * @return void
     */
    function checkinConnection(\Plasma\DriverInterface $driver): void {
        $this->client->checkinConnection($driver);
    }
    
    /**
     * Begins a transaction. Resolves with a `TransactionInterface` instance.
     *
     * Checks out a connection until the transaction gets committed or rolled back.
     * It must be noted that the user is responsible for finishing the transaction. The client WILL NOT automatically
     * check the connection back into the pool, as long as the transaction is not finished.
     *
     * Some databases, including MySQL, automatically issue an implicit COMMIT when a database definition language (DDL)
     * statement such as DROP TABLE or CREATE TABLE is issued within a transaction.
     * The implicit COMMIT will prevent you from rolling back any other changes within the transaction boundary.
     * @param int  $isolation  See the `TransactionInterface` constants.
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     * @see \Plasma\TransactionInterface
     */
    function beginTransaction(int $isolation = \Plasma\TransactionInterface::ISOLATION_COMMITTED): \React\Promise\PromiseInterface {
        return $this->client->beginTransaction($isolation)->then(function (\Plasma\TransactionInterface $transaction) {
            return (new \Plasma\Schemas\Transaction($this, $transaction));
        });
    }
    
    /**
     * Closes all connections gracefully after processing all outstanding requests.
     * @return \React\Promise\PromiseInterface
     */
    function close(): \React\Promise\PromiseInterface {
        return $this->client->close();
    }
    
    /**
     * Forcefully closes the connection, without waiting for any outstanding requests. This will reject all outstanding requests.
     * @return void
     */
    function quit(): void {
        $this->client->quit();
    }
    
    /**
     * Runs the given command.
     * @param \Plasma\CommandInterface  $command
     * @return mixed  Return depends on command and driver.
     * @throws \Plasma\Exception  Thrown if the client is closing all connections.
     */
    function runCommand(\Plasma\CommandInterface $command) {
        return $this->client->runCommand($command);
    }
    
    /**
     * Runs the given querybuilder on an underlying driver instance.
     * The driver CAN throw an exception if the given querybuilder is not supported.
     * An example would be a SQL querybuilder and a Cassandra driver.
     * @param \Plasma\QueryBuilderInterface  $query
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    function runQuery(\Plasma\QueryBuilderInterface $query): \React\Promise\PromiseInterface {
        return $this->client->runQuery($query)->then(array($this, 'handleQueryResult'));
    }
    
    /**
     * Creates a new cursor to seek through SELECT query results. Resolves with a `CursorInterface` instance.
     * @param string                   $query
     * @param array                    $params
     * @return \React\Promise\PromiseInterface
     * @throws \LogicException  Thrown if the driver or DBMS does not support cursors.
     * @throws \Plasma\Exception
     */
    function createReadCursor(string $query, array $params = array()): \React\Promise\PromiseInterface {
        return $this->client->createReadCursor($query, $params);
    }
    
    /**
     * Executes a plain query. Resolves with a `QueryResultInterface` instance.
     * @param string  $query
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     * @see \Plasma\QueryResultInterface
     */
    function query(string $query): \React\Promise\PromiseInterface {
        return $this->client->query($query)->then(array($this, 'handleQueryResult'));
    }
    
    /**
     * Prepares a query. Resolves with a `StatementInterface` instance.
     * @param string  $query
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     * @see \Plasma\StatementInterface
     */
    function prepare(string $query): \React\Promise\PromiseInterface {
        return $this->client->prepare($query)->then(array($this, 'handlePrepareStatement'));
    }
    
    /**
     * Prepares and executes a query. Resolves with a `QueryResultInterface` instance.
     * This is equivalent to prepare -> execute -> close.
     * If you need to execute a query multiple times, prepare the query manually for performance reasons.
     * @param string  $query
     * @param array   $params
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     * @see \Plasma\StatementInterface
     */
    function execute(string $query, array $params = array()): \React\Promise\PromiseInterface {
        return $this->client->execute($query, $params)->then(array($this, 'handleQueryResult'));
    }
    
    /**
     * Quotes the string for use in the query.
     * @param string  $str
     * @param int     $type  For types, see the driver interface constants.
     * @return string
     * @throws \LogicException  Thrown if the driver does not support quoting.
     * @throws \Plasma\Exception
     */
    function quote(string $str, int $type = \Plasma\DriverInterface::QUOTE_TYPE_VALUE): string {
        return $this->client->quote($str, $type);
    }
    
    /**
     * {@inheritDoc}
     * @internal
     */
    function on($event, callable $listener) {
        return $this->client->on($event, $listener);
    }
    
    /**
     * {@inheritDoc}
     * @internal
     */
    function once($event, callable $listener) {
        return $this->client->once($event, $listener);
    }
    
    /**
     * {@inheritDoc}
     * @internal
     */
    function removeListener($event, callable $listener) {
        return $this->client->removeListener($event, $listener);
    }
    
    /**
     * {@inheritDoc}
     * @internal
     */
    function removeAllListeners($event = null) {
        return $this->client->removeAllListeners($event);
    }
    
    /**
     * {@inheritDoc}
     * @internal
     */
    function listeners($event = null): array {
        return $this->client->listeners($event);
    }
    
    /**
     * {@inheritDoc}
     * @internal
     */
    function emit($event, array $arguments = []) {
        return $this->client->emit($event, $arguments);
    }
    
    /**
     * Handles a query result and maps it. Rows get buffered. Returns a `SchemaCollection`, if a SELECT query.
     * @param \Plasma\QueryResultInterface  $result
     * @return \Plasma\Schemas\SchemaCollection|\Plasma\QueryResultInterface|\React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     * @internal
     */
    function handleQueryResult(\Plasma\QueryResultInterface $result) {
        if($result instanceof \Plasma\StreamQueryResultInterface) {
            return $result->all()->then(array($this, 'handleQueryResult'));
        }
        
        $rows = $result->getRows();
        
        if($rows !== null) {
            if(empty($rows)) {
                return (new \Plasma\Schemas\SchemaCollection(array(), $result));
            }
            
            $fields = $result->getFieldDefinitions();
            $table = \reset($fields)->getTableName();
            
            if(isset($this->builders[$table])) {
                return $this->getDirectory($table)->buildSchemas($result);
            }
        }
        
        return $result;
    }
    
    /**
     * Handles a prepared statement and wraps it.
     * @param \Plasma\StatementInterface  $statement
     * @return \Plasma\Schemas\Statement
     * @internal
     */
    function handlePrepareStatement(\Plasma\StatementInterface $statement) {
        return (new \Plasma\Schemas\Statement($this, $statement));
    }
}
