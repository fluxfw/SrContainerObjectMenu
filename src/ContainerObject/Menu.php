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
     * @inheritDoc
     */
    public function getStaticTopItems() : array
    {
        return array_map(function (ContainerObject $container_object) : isItem {
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


    /**
     * @inheritDoc
     */
    public function getStaticSubItems() : array
    {
        return array_reduce(self::srContainerObjectMenu()->containerObjects()->getContainerObjects(), function (array $sub_items, ContainerObject $container_object) : array {
            $parent = $this->getStaticTopItems()[$container_object->getContainerObjectId()];

            $position = 0;

            foreach ($container_object->getChildren() as $child_id => $child_title) {

                $sub_items[] = $this->mainmenu->link($this->if->identifier($container_object->getMenuIdentifier($child_id)))
                    ->withParent($parent->getProviderIdentification())
                    ->withTitle($child_title)
                    ->withAction(ilLink::_getLink($child_id))
                    ->withPosition($position += 10)
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
}
