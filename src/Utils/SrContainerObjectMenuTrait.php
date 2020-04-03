<?php

namespace srag\Plugins\SrContainerObjectMenu\Utils;

use srag\Plugins\SrContainerObjectMenu\Repository;

/**
 * Trait SrContainerObjectMenuTrait
 *
 * @package srag\Plugins\SrContainerObjectMenu\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
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
