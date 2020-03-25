<?php

namespace srag\DataTable\SrContainerObjectMenu\Implementation\Column;

use srag\DataTable\SrContainerObjectMenu\Component\Column\Column as ColumnInterface;
use srag\DataTable\SrContainerObjectMenu\Component\Column\Factory as FactoryInterface;
use srag\DataTable\SrContainerObjectMenu\Component\Column\Formatter\Factory as FormatterFactoryInterface;
use srag\DataTable\SrContainerObjectMenu\Implementation\Column\Formatter\Factory as FormatterFactory;
use srag\DataTable\SrContainerObjectMenu\Utils\DataTableTrait;
use srag\DIC\SrContainerObjectMenu\DICTrait;

/**
 * Class Factory
 *
 * @package srag\DataTable\SrContainerObjectMenu\Implementation\Column
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Factory implements FactoryInterface
{

    use DICTrait;
    use DataTableTrait;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function column(string $key, string $title) : ColumnInterface
    {
        return new Column($key, $title);
    }


    /**
     * @inheritDoc
     */
    public function formatter() : FormatterFactoryInterface
    {
        return FormatterFactory::getInstance();
    }
}
