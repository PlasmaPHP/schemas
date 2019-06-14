<?php
/**
 * Lacia
 * Copyright 2018-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: -
 */

namespace Plasma\Schemas;

/**
 * The repository is responsible for turning query results into schemas and interfaces with a Plasma Client implementation.
 */
interface RepositoryInterface extends \Evenement\EventEmitterInterface, \Plasma\QueryableInterface {
    /**
     * Get the internally used client.
     * @return \Plasma\ClientInterface
     */
    function getClient(): \Plasma\ClientInterface;
    
    /**
     * Get the directory for the schema.
     * @param string  $schemaName  The schema name. This would be the table name.
     * @return \Plasma\Schemas\DirectoryInterface
     * @throws \Plasma\Exception
     */
    function getDirectory(string $schemaName): \Plasma\Schemas\DirectoryInterface;
    
    /**
     * Register a directory for the schema to be used by the repository.
     * @param string                              $schemaName  The schema name. This would be the table name.
     * @param \Plasma\Schemas\DirectoryInterface  $directory   The directory for the schema.
     * @return $this
     * @throws \Plasma\Exception
     */
    function registerDirectory(string $schemaName, \Plasma\Schemas\DirectoryInterface $directory);
    
    /**
     * Unregister the directory of the schema.
     * @param string  $schemaName  The schema name. This would be the table name.
     * @return $this
     */
    function unregisterDirectory(string $schemaName);
}