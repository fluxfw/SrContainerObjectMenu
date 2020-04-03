<?php

namespace srag\DataTableUI\SrContainerObjectMenu\Component\Utils;

use srag\DataTableUI\SrContainerObjectMenu\Component\Table;

/**
 * Interface TableBuilder
 *
 * @package srag\DataTableUI\SrContainerObjectMenu\Component\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
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
