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
 * A preload implementation.
 */
class Preload implements PreloadInterface {
    /**
     * @var string
     */
    protected $foreignTarget;
    
    /**
     * @var string
     */
    protected $foreignKey;
    
    /**
     * @var string
     */
    protected $localKey;
    
    /**
     * Constructor.
     * @param string  $foreignTarget
     * @param string  $foreignKey
     * @param string  $localKey
     */
    function __construct(string $foreignTarget, string $foreignKey, string $localKey) {
        $this->foreignTarget = $foreignTarget;
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
    }
    
    /**
     * Returns the foreign Target.
     * @return string
     */
    function getForeignTarget() {
        return $this->foreignTarget;
    }
    
    /**
     * Returns the foreign column.
     * @return string
     */
    function getForeignKey() {
        return $this->foreignKey;
    }
    
    /**
     * Returns the local column.
     * @return string
     */
    function getLocalKey() {
        return $this->localKey;
    }
}