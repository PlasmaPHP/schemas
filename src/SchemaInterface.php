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
     * Returns the name of the database (or any other equivalent).
     * @return string
     */
    static function getDatabaseName(): string;
    
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
     * Returns the asynchronous resolver to wait for before returning the schema.
     * May resolve with a new schema, which will get used instead.
     * @param bool  $autoloading  Whether this method gets called for autoloading (not manually).
     * @return \React\Promise\PromiseInterface|null
     */
    function getAsyncResolver(bool $autoloading = false): ?\React\Promise\PromiseInterface;
    
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
