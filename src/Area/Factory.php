<?php

namespace srag\Plugins\SrContainerObjectMenu\Area;

use ilSrContainerObjectMenuPlugin;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\Area\Form\FormBuilder;
use srag\Plugins\SrContainerObjectMenu\Area\Table\TableBuilder;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrContainerObjectMenu\Area
 */
final class Factory
{

    use DICTrait;
    use SrContainerObjectMenuTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


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
     * @param AreaCtrl $parent
     * @param Area     $area
     *
     * @return FormBuilder
     */
    public function newFormBuilderInstance(AreaCtrl $parent, Area $area) : FormBuilder
    {
        $form = new FormBuilder($parent, $area);

        return $form;
    }


    /**
     * @return Area
     */
    public function newInstance() : Area
    {
        $area = new Area();

        return $area;
    }


    /**
     * @param AreasCtrl $parent
     *
     * @return TableBuilder
     */
    public function newTableBuilderInstance(AreasCtrl $parent) : TableBuilder
    {
        $table = new TableBuilder($parent);

        return $table;
    }
}
