<?php

namespace srag\DataTable\SrContainerObjectMenu\Component;

use srag\DataTable\SrContainerObjectMenu\Component\Column\Column;
use srag\DataTable\SrContainerObjectMenu\Component\Column\Factory as ColumnFactory;
use srag\DataTable\SrContainerObjectMenu\Component\Data\Factory as DataFactory;
use srag\DataTable\SrContainerObjectMenu\Component\Data\Fetcher\DataFetcher;
use srag\DataTable\SrContainerObjectMenu\Component\Format\Factory as FormatFactory;
use srag\DataTable\SrContainerObjectMenu\Component\Settings\Factory as SettingsFactory;
use srag\DIC\SrContainerObjectMenu\Plugin\PluginInterface;

/**
 * Interface Factory
 *
 * @package srag\DataTable\SrContainerObjectMenu\Component
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface Factory
{

    /**
     * @return ColumnFactory
     */
    public function column() : ColumnFactory;


    /**
     * @return ColumnFactory
     */
    public function data() : DataFactory;


    /**
     * @return ColumnFactory
     */
    public function format() : FormatFactory;


    /**
     * @return ColumnFactory
     */
    public function settings() : SettingsFactory;


    /**
     * @param string      $table_id
     * @param string      $action_url
     * @param string      $title
     * @param Column[]    $columns
     * @param DataFetcher $data_fetcher
     *
     * @return Table
     */
    public function table(string $table_id, string $action_url, string $title, array $columns, DataFetcher $data_fetcher) : Table;


    /**
     * @param PluginInterface $plugin
     */
    public function installLanguages(PluginInterface $plugin)/* : void*/;
}
