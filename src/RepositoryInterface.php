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
 * The Repository Interface describes the public API of repositories.
 * The repository is responsible for turning row results into specified PHP object.
 */
interface RepositoryInterface extends \Evenement\EventEmitterInterface, \Plasma\QueryableInterface {
    /**
     * Get the internally used client.
     * @return \Plasma\ClientInterface
     */
    function getClient(): \Plasma\ClientInterface;
    
    /**
     * Get the Schema Builder for the schema.
     * @param string  $schemaName  The schema name. This would be the table name.
     * @return \Plasma\Schemas\SchemaBuilderInterface
     * @throws \Plasma\Exception
     */
    function getSchemaBuilder(string $schemaName): \Plasma\Schemas\SchemaBuilderInterface;
    
    /**
     * Register a Schema Builder for the schema to be used by the Repository.
     * @param string                                  $schemaName     The schema name. This would be the table name.
     * @param \Plasma\Schemas\SchemaBuilderInterface  $schemaBuilder  The schema builder for the schema.
     * @return $this
     * @throws \Plasma\Exception
     */
    function registerSchemaBuilder(string $schemaName, \Plasma\Schemas\SchemaBuilderInterface $schemaBuilder);
    
    /**
     * Unregister the Schema Builder of the schema.
     * @param string  $schemaName  The schema name. This would be the table name.
     * @return $this
     */
    function unregisterSchemaBuilder(string $schemaName);
}