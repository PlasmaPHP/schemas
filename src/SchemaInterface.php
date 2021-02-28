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
use Plasma\QueryResultInterface;
use React\Promise\PromiseInterface;

/**
 * Schemas represent data rows and as such can be used to interact with the DBMS through the Repository and AbstractSchema Builder.
 */
interface SchemaInterface {
    /**
     * Builds a schema instance.
     * @param Repository  $repository
     * @param array       $row
     * @return SchemaInterface
     * @throws Exception
     */
    static function build(Repository $repository, array $row): SchemaInterface;
    
    /**
     * Returns the schema definition.
     * @return \Plasma\ColumnDefinitionInterface[]
     */
    static function getDefinition(): array;
    
    /**
     * Returns the name of the table (or any other equivalent).
     * @return string
     */
    static function getTableName(): string;
    
    /**
     * Returns the name of the identifier column (primary or unique), or null.
     * @return string|null
     */
    static function getIdentifierColumn(): ?string;
    
    /**
     * Lets the directory preload the foreign references on schema request.
     * Returns an array of `PreloadInterface`.
     * @return PreloadInterface[]
     */
    static function getPreloads(): array;
    
    /**
     * This is the after preload hook, which gets called with the preloads
     * which were used to create the schema. The hook is responsible for
     * creating the other schemas from the preloads and the table result.
     * @param QueryResultInterface  $result    This is always a query result with only a single row.
     * @param PreloadInterface[]    $preloads
     * @return void
     * @throws Exception
     */
    function afterPreloadHook(QueryResultInterface $result, array $preloads): void;
    
    /**
     * Resolves the outstanding foreign targets. Resolves with a new schema.
     * @return PromiseInterface|null
     */
    function resolveForeignTargets(): ?PromiseInterface;
    
    /**
     * Inserts the row.
     * @return PromiseInterface
     * @throws Exception
     */
    function insert(): PromiseInterface;
    
    /**
     * Updates the row with the new data.
     * @param array  $data
     * @return PromiseInterface
     * @throws Exception
     */
    function update(array $data): PromiseInterface;
    
    /**
     * Deletes the row. Resolves with a `QueryResultInterface` instance.
     * @return PromiseInterface
     * @throws Exception
     */
    function delete(): PromiseInterface;
}
