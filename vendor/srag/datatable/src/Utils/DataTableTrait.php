<?php

namespace srag\DataTable\SrContainerObjectMenu\Utils;

use srag\DataTable\SrContainerObjectMenu\Component\Factory as FactoryInterface;
use srag\DataTable\SrContainerObjectMenu\Implementation\Factory;

/**
 * Trait DataTableTrait
 *
 * @package srag\DataTable\SrContainerObjectMenu\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait DataTableTrait
{

    /**
     * @return FactoryInterface
     */
    protected static function dataTable() : FactoryInterface
    {
        return Factory::getInstance();
    }
}
