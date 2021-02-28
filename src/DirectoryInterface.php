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
 * Directories are responsible for building schemas from query results and interfaces with the repository for queries.
 */
interface DirectoryInterface {
    /**
     * Sets the repository to use.
     * @param Repository  $repository
     * @return void
     */
    function setRepository(Repository $repository): void;
    
    /**
     * Fetch a row by the unique identifier. Resolves with an instance of `SchemaCollection`.
     * @param mixed  $value
     * @return PromiseInterface
     * @throws Exception
     */
    function fetch($value): PromiseInterface;
    
    /**
     * Fetch a row by the specified column. Resolves with an instance of `SchemaCollection`.
     * @param string  $name
     * @param mixed   $value
     * @return PromiseInterface
     * @throws Exception
     */
    function fetchBy(string $name, $value): PromiseInterface;
    
    /**
     * Fetches all rows. Resolves with an instance of `SchemaCollection`.
     * @return PromiseInterface
     * @throws Exception
     */
    function fetchAll(): PromiseInterface;
    
    /**
     * Inserts a row. Resolves with an instance of `SchemaCollection`.
     * @param array  $data
     * @return PromiseInterface
     * @throws Exception
     */
    function insert(array $data): PromiseInterface;
    
    /**
     * Inserts a list of rows. Resolves with an instance of `SchemaCollection`.
     *
     * Options is an optional array, which supports these options:
     * ```
     * array(
     *     'ignoreConflict' => bool, (whether duplicate key conflicts get ignored, defaults to false)
     * )
     * ```
     *
     * Some builders may support more options.
     *
     * @param array  $data
     * @param array  $options
     * @return PromiseInterface
     * @throws Exception
     */
    function insertAll(array $data, array $options = array()): PromiseInterface;
    
    /**
     * Updates the row with the given data, identified by a specific field.
     * @param array   $data
     * @param string  $field
     * @param mixed   $value
     * @return PromiseInterface
     * @throws Exception
     */
    function update(array $data, string $field, $value): PromiseInterface;
    
    /**
     * Deletes a row by the unique identifier. Resolves with a `QueryResultInterface` instance.
     * @param mixed   $value
     * @return PromiseInterface
     * @throws Exception
     */
    function delete($value): PromiseInterface;
    
    /**
     * Deletes a row by the specified column. Resolves with a `QueryResultInterface` instance.
     * @param string  $name
     * @param mixed   $value
     * @return PromiseInterface
     * @throws Exception
     */
    function deleteBy(string $name, $value): PromiseInterface;
    
    /**
     * Builds schemas for the given SELECT query result.
     * @param QueryResultInterface  $result
     * @return SchemaCollection
     */
    function buildSchemas(QueryResultInterface $result): SchemaCollection;
}
