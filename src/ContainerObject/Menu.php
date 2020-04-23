<?php

namespace srag\Plugins\SrContainerObjectMenu\ContainerObject;

use ILIAS\GlobalScreen\Scope\MainMenu\Factory\isItem;
use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticPluginMainMenuProvider;
use ilLink;
use ilSrContainerObjectMenuPlugin;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class Menu
 *
 * @package srag\Plugins\SrContainerObjectMenu\ContainerObject
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Menu extends AbstractStaticPluginMainMenuProvider
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
            self::$instance = new self(self::dic()->dic(), self::plugin()->getPluginObject());
        }

        return self::$instance;
    }


    /**
     * @var array|null
     */
    protected $top_items = null;
    /**
     * @var array|null
     */
    protected $sub_items = null;


    /**
     * @inheritDoc
     */
    public function getStaticTopItems() : array
    {
        if ($this->top_items === null) {
            $this->top_items = array_map(function (ContainerObject $container_object) : isItem {
                return $this->mainmenu->topParentItem($this->if->identifier($container_object->getMenuIdentifier()))
                    ->withTitle($container_object->getObject()->getTitle())
                    ->withAvailableCallable(function () : bool {
                        return self::plugin()->getPluginObject()->isActive();
                    })
                    ->withVisibilityCallable(function () : bool {
                        return self::plugin()->getPluginObject()->isActive();
                    });
            }, self::srContainerObjectMenu()->containerObjects()->getContainerObjects());
        }

        return $this->top_items;
    }


    /**
     * @inheritDoc
     */
    public function getStaticSubItems() : array
    {
        if ($this->sub_items === null) {
            $this->sub_items = array_reduce(self::srContainerObjectMenu()->containerObjects()->getContainerObjects(), function (array $sub_items, ContainerObject $container_object) : array {
                $parent = $this->getStaticTopItems()[$container_object->getContainerObjectId()];

                $position = 0;

                foreach ($container_object->getChildren() as $child_id => $child_title) {

                    $position += 10;

                    $sub_items[] = $this->mainmenu->link($this->if->identifier($container_object->getMenuIdentifier($child_id, $position)))
                        ->withParent($parent->getProviderIdentification())
                        ->withTitle($child_title)
                        ->withAction(ilLink::_getLink($child_id))
                        ->withPosition($position)
                        ->withAvailableCallable(function () : bool {
                            return self::plugin()->getPluginObject()->isActive();
                        })
                        ->withVisibilityCallable(function () use ($child_id) : bool {
                            return self::dic()->access()->checkAccess("read", "", $child_id);
                        });
                }

                return $sub_items;
            }, []);
        }

        return $this->sub_items;
    }
}
