<?php

namespace srag\DIC\SrContainerObjectMenu\DIC;

use ILIAS\DI\Container;
use srag\DIC\SrContainerObjectMenu\Database\DatabaseDetector;
use srag\DIC\SrContainerObjectMenu\Database\DatabaseInterface;

/**
 * Class AbstractDIC
 *
 * @package srag\DIC\SrContainerObjectMenu\DIC
 */
abstract class AbstractDIC implements DICInterface
{

    /**
     * @var Container
     */
    protected $dic;


    /**
     * @inheritDoc
     */
    public function __construct(Container &$dic)
    {
        $this->dic = &$dic;
    }


    /**
     * @inheritDoc
     */
    public function database() : DatabaseInterface
    {
        return DatabaseDetector::getInstance($this->databaseCore());
    }
}
