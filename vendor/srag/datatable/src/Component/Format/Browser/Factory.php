<?php

namespace srag\DataTable\SrContainerObjectMenu\Component\Format\Browser;

/**
 * Interface Factory
 *
 * @package srag\DataTable\SrContainerObjectMenu\Component\Format\Browser
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface Factory
{

    /**
     * @return BrowserFormat
     */
    public function default() : BrowserFormat;
}
