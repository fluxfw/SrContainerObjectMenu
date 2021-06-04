<?php

namespace srag\DataTableUI\SrContainerObjectMenu\Component\Column;

use srag\DataTableUI\SrContainerObjectMenu\Component\Column\Formatter\Factory as FormatterFactory;

/**
 * Interface Factory
 *
 * @package srag\DataTableUI\SrContainerObjectMenu\Component\Column
 */
interface Factory
{

    /**
     * @param string $key
     * @param string $title
     *
     * @return Column
     */
    public function column(string $key, string $title) : Column;


    /**
     * @return FormatterFactory
     */
    public function formatter() : FormatterFactory;
}
