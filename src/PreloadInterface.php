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
 * Preloads are a way to load foreign references at the same time as a schema gets loaded,
 * and let your schema be always filled with the foreign reference schema.
 * How the preloads are exactly loaded depends on the Directory implementation.
 *
 * Preloads are foreign targets with fetch mode `ALWAYS` and are automatically handled.
 * Foreign target with fetch mode `LAZY` are not automatically loaded and need to be
 * explicitely asked for by calling `getAsyncResolver` on the schema.
 *
 * Whether one uses one over the other fetch mode depends on the use case. It makes sense
 * to only preload schemas you actually really always need.
 */
interface PreloadInterface {
    /**
     * Foreign keys are never automatically resolved in schemas.
     * Foreign keys need to be explicitely resolved.
     * Default mode.
     * @var int
     * @source
     */
    const FETCH_MODE_LAZY = 0x1;
    
    /**
     * Foreign keys are always automatically resolved in schemas.
     * However this will lead to more data fetching and memory growth.
     * This may be unnecessary in certain situations.
     * @var int
     * @source
     */
    const FETCH_MODE_ALWAYS = 0x5;
    
    /**
     * Returns the foreign target (in SQL the table).
     * @return mixed  Can be any type, must be understood by the underlying directory.
     */
    function getForeignTarget();
    
    /**
     * Returns the foreign key (in SQL the column).
     * @return mixed  Can be any type, must be understood by the underlying directory.
     */
    function getForeignKey();
    
    /**
     * Returns the local key (in SQL the column).
     * @return mixed  Can be any type, must be understood by the underlying directory.
     */
    function getLocalKey();
}