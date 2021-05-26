<?php

namespace srag\DataTableUI\SrContainerObjectMenu\Component\Utils;

use srag\DataTableUI\SrContainerObjectMenu\Component\Table;

/**
 * Interface TableBuilder
 *
 * @package srag\DataTableUI\SrContainerObjectMenu\Component\Utils
 */
interface TableBuilder
{

    /**
     * @return Table
     */
    public function getTable() : Table;


    /**
     * @return string
     */
    public function render() : string;
}
