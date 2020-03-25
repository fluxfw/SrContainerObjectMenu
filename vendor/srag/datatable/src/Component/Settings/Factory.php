<?php

namespace srag\DataTable\SrContainerObjectMenu\Component\Settings;

use ILIAS\UI\Component\ViewControl\Pagination;
use srag\DataTable\SrContainerObjectMenu\Component\Settings\Sort\Factory as SortFactory;
use srag\DataTable\SrContainerObjectMenu\Component\Settings\Storage\Factory as StorageFactory;

/**
 * Interface Factory
 *
 * @package srag\DataTable\SrContainerObjectMenu\Component\Settings
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface Factory
{

    /**
     * @param Pagination $pagination
     *
     * @return Settings
     */
    public function settings(Pagination $pagination) : Settings;


    /**
     * @return SortFactory
     */
    public function sort() : SortFactory;


    /**
     * @return StorageFactory
     */
    public function storage() : StorageFactory;
}
