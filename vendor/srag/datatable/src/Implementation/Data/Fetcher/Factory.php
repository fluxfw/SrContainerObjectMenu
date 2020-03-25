<?php

namespace srag\DataTable\SrContainerObjectMenu\Implementation\Data\Fetcher;

use srag\DataTable\SrContainerObjectMenu\Component\Data\Fetcher\DataFetcher;
use srag\DataTable\SrContainerObjectMenu\Component\Data\Fetcher\Factory as FactoryInterface;
use srag\DataTable\SrContainerObjectMenu\Utils\DataTableTrait;
use srag\DIC\SrContainerObjectMenu\DICTrait;

/**
 * Class Factory
 *
 * @package srag\DataTable\SrContainerObjectMenu\Implementation\Data\Fetcher
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
    public function staticData(array $data, string $id_key) : DataFetcher
    {
        return new StaticDataFetcher($data, $id_key);
    }
}
