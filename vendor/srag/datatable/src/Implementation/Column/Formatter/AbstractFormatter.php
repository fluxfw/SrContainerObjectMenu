<?php

namespace srag\DataTable\SrContainerObjectMenu\Implementation\Column\Formatter;

use srag\DataTable\SrContainerObjectMenu\Component\Column\Formatter\Formatter;
use srag\DataTable\SrContainerObjectMenu\Utils\DataTableTrait;
use srag\DIC\SrContainerObjectMenu\DICTrait;

/**
 * Class AbstractFormatter
 *
 * @package srag\DataTable\SrContainerObjectMenu\Implementation\Column\Formatter
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractFormatter implements Formatter
{

    use DICTrait;
    use DataTableTrait;


    /**
     * AbstractFormatter constructor
     */
    public function __construct()
    {

    }
}
