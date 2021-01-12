<?php

namespace srag\Plugins\SrContainerObjectMenu\SelectedArea;

use ilSrContainerObjectMenuPlugin;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\Area\Area;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrContainerObjectMenu\SelectedArea
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository
{

    use DICTrait;
    use SrContainerObjectMenuTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;
    /**
     * @var Area[]
     */
    protected $areas = [];
    /**
     * @var SelectedArea[]|null
     */
    protected $selected_areas = null;
    /**
     * @var SelectedArea[]
     */
    protected $selected_areas_by_usr_id = [];


    /**
     * Repository constructor
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
     * @internal
     */
    public function dropTables()/* : void*/
    {
        self::dic()->database()->dropTable(SelectedArea::TABLE_NAME, false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param SelectedArea $selected_area
     *
     * @return Area|null
     */
    public function getArea(SelectedArea $selected_area)/* : ?Area*/
    {
        if (empty($selected_area->getSelectAreaId())) {
            return null;
        }

        if ($this->areas[$selected_area->getSelectAreaId()] === null) {
            $areas = self::srContainerObjectMenu()->areas()->getAreas(true);

            foreach ($areas as $area) {
                if ($area->getAreaId() === $selected_area->getAreaId(true)) {
                    $this->areas[$selected_area->getSelectAreaId()] = $area;
                    break;
                }
            }

            if ($this->areas[$selected_area->getSelectAreaId()] === null) {
                $this->areas[$selected_area->getSelectAreaId()] = reset($areas);
            }
        }

        return ($this->areas[$selected_area->getSelectAreaId()] ?: null);
    }


    /**
     * @param int $usr_id
     *
     * @return SelectedArea
     */
    public function getSelectedArea(int $usr_id) : SelectedArea
    {
        if ($this->selected_areas_by_usr_id[$usr_id] === null) {
            $this->selected_areas_by_usr_id[$usr_id] = SelectedArea::where(["usr_id" => $usr_id])->first();

            if ($this->selected_areas_by_usr_id[$usr_id] === null) {
                $this->selected_areas_by_usr_id[$usr_id] = $this->factory()->newInstance();

                $this->selected_areas_by_usr_id[$usr_id]->setUsrId($usr_id);
            }
        }

        return $this->selected_areas_by_usr_id[$usr_id];
    }


    /**
     * @return SelectedArea[]
     */
    public function getSelectedAreas() : array
    {
        if ($this->selected_areas === null) {
            $this->selected_areas = array_values(SelectedArea::get());

            foreach ($this->selected_areas as $selected_area) {
                $this->selected_areas_by_usr_id[$selected_area->getUsrId()] = $selected_area;
            }
        }

        return $this->selected_areas;
    }


    /**
     * @internal
     */
    public function installTables()/* : void*/
    {
        SelectedArea::updateDB();
    }


    /**
     * @param SelectedArea $selected_area
     */
    public function storeSelectedArea(SelectedArea $selected_area)/* : void*/
    {
        $selected_area->store();

        $this->selected_areas_by_usr_id[$selected_area->getUsrId()] = $selected_area;
        $this->selected_areas = null;
        unset($this->areas[$selected_area->getSelectAreaId()]);
    }
}
