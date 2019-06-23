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
 * Schemas represent data rows and as such can be used to interact with the DBMS through the Repository and AbstractSchema Builder.
 */
interface SchemaInterface {
    /**
     * Builds a schema instance.
     * @param \Plasma\Schemas\Repository  $repository
     * @param array                       $row
     * @return \Plasma\Schemas\SchemaInterface
     * @throws \Plasma\Exception
     */
    static function build(\Plasma\Schemas\Repository $repository, array $row): \Plasma\Schemas\SchemaInterface;
    
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
     * @return \Plasma\Schemas\PreloadInterface[]
     */
    static function getPreloads(): array;
    
    /**
     * This is the after preload hook, which gets called with the preloads
     * which were used to create the schema. The hook is responsible for
     * creating the other schemas from the preloads and the table result.
     * @param \Plasma\QueryResultInterface        $result    This is always a query result with only a single row.
     * @param \Plasma\Schemas\PreloadInterface[]  $preloads
     * @return void
     * @throws \Plasma\Exception
     */
    function afterPreloadHook(\Plasma\QueryResultInterface $result, array $preloads): void;
    
    /**
     * Returns the asynchronous resolver to wait for before returning the schema.
     * Resolves with a new schema, which will get used instead, or null.
     * @return \React\Promise\PromiseInterface|null
     */
    function getAsyncResolver(): ?\React\Promise\PromiseInterface;
    
    /**
     * Inserts the row.
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    function insert(): \React\Promise\PromiseInterface;
    
    /**
     * Updates the row with the new data.
     * @param array  $data
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    function update(array $data): \React\Promise\PromiseInterface;
    
    /**
     * Deletes the row. Resolves with a `QueryResultInterface` instance.
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    function delete(): \React\Promise\PromiseInterface;
}
