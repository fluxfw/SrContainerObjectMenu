<?php

namespace srag\DataTableUI\SrContainerObjectMenu\Implementation\Utils;

use srag\DataTableUI\SrContainerObjectMenu\Component\Factory as FactoryInterface;
use srag\DataTableUI\SrContainerObjectMenu\Implementation\Factory;

/**
 * Trait DataTableUITrait
 *
 * @package srag\DataTableUI\SrContainerObjectMenu\Implementation\Utils
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
