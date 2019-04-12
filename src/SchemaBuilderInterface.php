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
 * Schema Builders are responsible for creating individual schemas from query results.
 */
interface SchemaBuilderInterface {
    /**
     * Sets the repository to use.
     * @param \Plasma\Schemas\Repository  $repository
     * @return void
     */
    function setRepository(\Plasma\Schemas\Repository $repository): void;
    
    /**
     * Fetches all rows. Resolves with an instance of `SchemaCollection`.
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    function fetchAll(): \React\Promise\PromiseInterface;
    
    /**
     * Fetch a row by the unique identifier. Resolves with an instance of `SchemaCollection`.
     * @param mixed  $value
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    function fetch($value): \React\Promise\PromiseInterface;
    
    /**
     * Fetch a row by the specified column. Resolves with an instance of `SchemaCollection`.
     * @param string  $name
     * @param mixed   $value
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    function fetchBy(string $name, $value): \React\Promise\PromiseInterface;
    
    /**
     * Inserts a row. Resolves with an instance of `SchemaCollection`.
     * @param array  $data
     * @return \React\Promise\PromiseInterface
     * @throws \Plasma\Exception
     */
    function insert(array $data): \React\Promise\PromiseInterface;
    
    /**
     * Builds schemas for the given SELECT query result.
     * @param \Plasma\QueryResult  $result
     * @return \Plasma\Schemas\SchemaCollection
     */
    function buildSchemas(\Plasma\QueryResult $result): \Plasma\Schemas\SchemaCollection;
}
