<?php

namespace srag\DataTableUI\SrContainerObjectMenu\Implementation\Column\Formatter;

use srag\DataTableUI\SrContainerObjectMenu\Component\Column\Formatter\Formatter;
use srag\DataTableUI\SrContainerObjectMenu\Implementation\Utils\DataTableUITrait;
use srag\DIC\SrContainerObjectMenu\DICTrait;

/**
 * Class AbstractFormatter
 *
 * @package srag\DataTableUI\SrContainerObjectMenu\Implementation\Column\Formatter
 */
abstract class AbstractFormatter implements Formatter
{

    use DICTrait;
    use DataTableUITrait;

    /**
     * AbstractFormatter constructor
     */
    public function __construct()
    {

    }
}
