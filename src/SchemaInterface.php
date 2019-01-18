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
 * Schemas represent data rows and as such can be used to interact with the DBMS through the Repository and Schema Builder.
 */
interface SchemaInterface {
    /**
     * Builds a schema instance.
     * @param \Plasma\Schemas\Repository         $repository
     * @param \Plasma\QueryResultInterface|null  $result
     * @param array                              $row
     * @return self
     * @throws \Plasma\Exception
     */
    static function build(\Plasma\Schemas\Repository $repository, ?\Plasma\QueryResultInterface $result, array $row);
    
    /**
     * Returns the name of the identifier column (primary or unique).
     * @return string
     */
    function getIdentifierColumn(): string;
    
    /**
     * Updates the row with the new data.
     * @return \React\Promise\PromiseInterface
     */
    function update(array $data): \React\Promise\PromiseInterface;
    
    /**
     * Deletes the row.
     * @return \React\Promise\PromiseInterface
     */
    function delete(): \React\Promise\PromiseInterface;
}
