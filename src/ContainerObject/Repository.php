<?php

namespace srag\Plugins\SrContainerObjectMenu\ContainerObject;

use ilContainer;
use ilContainerSorting;
use ilDBConstants;
use ilMMItemFacadeInterface;
use ilMMItemRepository;
use ilSrContainerObjectMenuPlugin;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrContainerObjectMenu\ContainerObject
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
     * Repository constructor
     */
    private function __construct()
    {

    }


    /**
     * @return ilMMItemRepository
     */
    protected function coreMenu() : ilMMItemRepository
    {
        return new ilMMItemRepository();
    }


    /**
     * @param ContainerObject $container_object
     */
    public function deleteContainerObject(ContainerObject $container_object)/*: void*/
    {
        $container_object->delete();

        $this->deleteCoreMenuItems($container_object);
    }


    /**
     * @param ContainerObject|null $container_object
     */
    protected function deleteCoreMenuItems(/*?*/ ContainerObject $container_object = null)/*: void*/
    {
        if ($container_object === null) {
            $container_object = $this->factory()->newInstance();
        }

        foreach (
            self::dic()->database()->fetchAll(self::dic()->database()->query('SELECT identification FROM il_mm_items WHERE ' . self::dic()
                    ->database()
                    ->like("identification", ilDBConstants::T_TEXT, '%' . $container_object->getMenuIdentifier() . '%'))) as $item
        ) {
            $this->coreMenu()->deleteItem($this->coreMenu()->getItemFacadeForIdentificationString($item["identification"]));
        };
    }


    /**
     * @internal
     */
    public function dropTables()/*:void*/
    {
        self::dic()->database()->dropTable(ContainerObject::TABLE_NAME, false);

        $this->deleteCoreMenuItems();
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param ilContainer $object
     *
     * @return array
     */
    public function getChildren(ilContainer $object) : array
    {
        $children = [];

        $sub_items = $object->getSubItems();

        foreach ((array) ilContainerSorting::_getInstance($object->getId())->getBlockPositions() as $type) {

            foreach ((array) $sub_items[$type] as $sub_item) {
                $children[$sub_item["child"]] = $sub_item["title"];
            }
        }

        return $children;
    }


    /**
     * @param int $container_object_id
     *
     * @return ContainerObject|null
     */
    public function getContainerObjectById(int $container_object_id)/*: ?ContainerObject*/
    {
        /**
         * @var ContainerObject|null $container_object
         */

        $container_object = ContainerObject::where(["container_object_id" => $container_object_id])->first();

        return $container_object;
    }


    /**
     * @return ContainerObject[]
     */
    public function getContainerObjects() : array
    {
        if (!self::dic()->database()->tableExists(ContainerObject::TABLE_NAME)) {
            return [];
        }

        return ContainerObject::get();
    }


    /**
     * @param string $menu_identifier
     *
     * @return ilMMItemFacadeInterface|null
     */
    public function getMenuItem(string $menu_identifier)/* : ?ilMMItemFacadeInterface*/
    {
        $item = self::dic()->database()->fetchAssoc(self::dic()->database()->query('SELECT identification FROM il_mm_items WHERE ' . self::dic()
                ->database()
                ->like("identification", ilDBConstants::T_TEXT, '%' . $menu_identifier)));

        if (!$item) {
            return null;
        }

        return $this->coreMenu()->getItemFacadeForIdentificationString($item["identification"]);
    }


    /**
     * @internal
     */
    public function installTables()/*:void*/
    {
        ContainerObject::updateDB();
    }


    /**
     * @param ContainerObject $container_object
     */
    public function storeContainerObject(ContainerObject $container_object)/*: void*/
    {
        $container_object->store();
    }
}
