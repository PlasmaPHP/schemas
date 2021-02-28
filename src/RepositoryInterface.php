<?php
/**
 * Lacia
 * Copyright 2018-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: -
 */

namespace Plasma\Schemas;

use Evenement\EventEmitterInterface;
use Plasma\ClientInterface;
use Plasma\Exception;
use Plasma\QueryableInterface;

/**
 * The repository is responsible for turning query results into schemas and interfaces with a Plasma Client implementation.
 */
interface RepositoryInterface extends EventEmitterInterface, QueryableInterface {
    /**
     * Get the internally used client.
     * @return ClientInterface
     */
    function getClient(): ClientInterface;
    
    /**
     * Get the directory for the schema.
     * @param string  $schemaName  The schema name. This would be the table name.
     * @return DirectoryInterface
     * @throws Exception
     */
    function getDirectory(string $schemaName): DirectoryInterface;
    
    /**
     * Register a directory for the schema to be used by the repository.
     * @param string              $schemaName  The schema name. This would be the table name.
     * @param DirectoryInterface  $directory   The directory for the schema.
     * @return $this
     * @throws Exception
     */
    function registerDirectory(string $schemaName, DirectoryInterface $directory): self;
    
    /**
     * Unregister the directory of the schema.
     * @param string  $schemaName  The schema name. This would be the table name.
     * @return $this
     */
    function unregisterDirectory(string $schemaName): self;
}
