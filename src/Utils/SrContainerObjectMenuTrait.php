<?php

namespace srag\Plugins\SrContainerObjectMenu\Utils;

use srag\Plugins\SrContainerObjectMenu\Repository;

/**
 * Trait SrContainerObjectMenuTrait
 *
 * @package srag\Plugins\SrContainerObjectMenu\Utils
 */
trait SrContainerObjectMenuTrait
{

    /**
     * @return Repository
     */
    protected static function srContainerObjectMenu() : Repository
    {
        return Repository::getInstance();
    }
}
