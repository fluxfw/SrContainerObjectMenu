<?php

namespace srag\DataTable\SrContainerObjectMenu\Implementation;

use srag\DataTable\SrContainerObjectMenu\Component\Column\Factory as ColumnFactoryInterface;
use srag\DataTable\SrContainerObjectMenu\Component\Data\Factory as DataFactoryInterface;
use srag\DataTable\SrContainerObjectMenu\Component\Data\Fetcher\DataFetcher;
use srag\DataTable\SrContainerObjectMenu\Component\Factory as FactoryInterface;
use srag\DataTable\SrContainerObjectMenu\Component\Format\Factory as FormatFactoryInterface;
use srag\DataTable\SrContainerObjectMenu\Component\Settings\Factory as SettingsFactoryInterface;
use srag\DataTable\SrContainerObjectMenu\Component\Table as TableInterface;
use srag\DataTable\SrContainerObjectMenu\Implementation\Column\Factory as ColumnFactory;
use srag\DataTable\SrContainerObjectMenu\Implementation\Data\Factory as DataFactory;
use srag\DataTable\SrContainerObjectMenu\Implementation\Format\Factory as FormatFactory;
use srag\DataTable\SrContainerObjectMenu\Implementation\Settings\Factory as SettingsFactory;
use srag\DataTable\SrContainerObjectMenu\Utils\DataTableTrait;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\DIC\SrContainerObjectMenu\Plugin\PluginInterface;
use srag\DIC\SrContainerObjectMenu\Util\LibraryLanguageInstaller;

/**
 * Class Factory
 *
 * @package srag\DataTable\SrContainerObjectMenu\Implementation
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
    public function column() : ColumnFactoryInterface
    {
        return ColumnFactory::getInstance();
    }


    /**
     * @inheritDoc
     */
    public function data() : DataFactoryInterface
    {
        return DataFactory::getInstance();
    }


    /**
     * @inheritDoc
     */
    public function format() : FormatFactoryInterface
    {
        return FormatFactory::getInstance();
    }


    /**
     * @inheritDoc
     */
    public function settings() : SettingsFactoryInterface
    {
        return SettingsFactory::getInstance();
    }


    /**
     * @inheritDoc
     */
    public function table(string $table_id, string $action_url, string $title, array $columns, DataFetcher $data_fetcher) : TableInterface
    {
        return new Table($table_id, $action_url, $title, $columns, $data_fetcher);
    }


    /**
     * @inheritDoc
     */
    public function installLanguages(PluginInterface $plugin)/* : void*/
    {
        LibraryLanguageInstaller::getInstance()->withPlugin($plugin)->withLibraryLanguageDirectory(__DIR__
            . "/../../lang")->updateLanguages();
    }
}
