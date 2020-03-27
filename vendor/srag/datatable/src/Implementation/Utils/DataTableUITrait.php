<?php

namespace srag\DataTableUI\SrContainerObjectMenu\Implementation\Utils;

use srag\DataTableUI\SrContainerObjectMenu\Component\Factory as FactoryInterface;
use srag\DataTableUI\SrContainerObjectMenu\Implementation\Factory;

/**
 * Trait DataTableUITrait
 *
 * @package srag\DataTableUI\SrContainerObjectMenu\Implementation\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait DataTableUITrait
{

    /**
     * @return FactoryInterface
     */
    protected static function dataTableUI() : FactoryInterface
    {
        return Factory::getInstance();
    }
}
