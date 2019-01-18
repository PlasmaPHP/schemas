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
 * The repository is responsible for turning rows into specified PHP object.
 *
 * The repository implements the ClientInterface to allow it to be passed around
 * and get directly used. Internally it uses an actual plasma client,
 * itself has no client implementations.
 */
class Statement implements \Plasma\StatementInterface {
    /**
     * @var \Plasma\Schemas\Repository
     */
    protected $repo;
    
    /**
     * @var \Plasma\StatementInterface
     */
    protected $statement;
    
    /**
     * Constructor.
     * @param \Plasma\Schemas\Repository  $repo
     * @param \Plasma\StatementInterface  $statement
     * @internal
     */
    function __construct(\Plasma\Schemas\Repository $repo, \Plasma\StatementInterface $statement) {
        $this->repo = $repo;
        $this->statement = $statement;
    }
    
    /**
     * Get the driver-dependent ID of this statement.
     * The return type can be of ANY type, as the ID depends on the driver and DBMS.
     * @return mixed
     */
    function getID() {
        return $this->statement->getID();
    }
    
    /**
     * Get the prepared query.
     * @return string
     */
    function getQuery(): string {
        return $this->statement->getQuery();
    }
    
    /**
     * Whether the statement has been closed.
     * @return bool
     */
    function isClosed(): bool {
        return $this->statement->isClosed();
    }
    
    /**
     * Closes the prepared statement and frees the associated resources on the server.
     * Closing a statement more than once SHOULD have no effect.
     * @return \React\Promise\PromiseInterface
     */
    function close(): \React\Promise\PromiseInterface {
        return $this->statement->close();
    }
    
    /**
     * Executes the prepared statement. Resolves with a `QueryResult` instance.
     * @param array  $params
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     * @see \Plasma\QueryResultInterface
     */
    function execute(array $params = array()): \React\Promise\PromiseInterface {
        return $this->statement->execute($params)->then(array($this->repo, 'handleQueryResult'));
    }
}
