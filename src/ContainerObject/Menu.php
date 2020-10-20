<?php

namespace srag\Plugins\SrContainerObjectMenu\ContainerObject;

use ILIAS\GlobalScreen\Scope\MainMenu\Factory\AbstractBaseItem;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\isItem;
use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticPluginMainMenuProvider;
use ILIAS\UI\Component\Symbol\Icon\Standard;
use ilLink;
use ilObject;
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
     * @var array|null
     */
    protected $sub_items = null;
    /**
     * @var array|null
     */
    protected $top_items = null;


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

                    $sub_items[] = $this->symbol($this->mainmenu->link($this->if->identifier($container_object->getMenuIdentifier($child_id, $position)))
                        ->withParent($parent->getProviderIdentification())
                        ->withTitle($child_title)
                        ->withAction(ilLink::_getLink($child_id))
                        ->withPosition($position)
                        ->withAvailableCallable(function () : bool {
                            return self::plugin()->getPluginObject()->isActive();
                        })
                        ->withVisibilityCallable(function () use ($child_id) : bool {
                            return self::dic()->access()->checkAccess("read", "", $child_id);
                        }), self::dic()->objDataCache()->lookupObjId($child_id));
                }

                return $sub_items;
            }, []);
        }

        return $this->sub_items;
    }


    /**
     * @inheritDoc
     */
    public function getStaticTopItems() : array
    {
        if ($this->top_items === null) {
            $this->top_items = array_map(function (ContainerObject $container_object) : isItem {
                return $this->symbol($this->mainmenu->topParentItem($this->if->identifier($container_object->getMenuIdentifier()))
                    ->withTitle($container_object->getObject()->getTitle())
                    ->withAvailableCallable(function () : bool {
                        return self::plugin()->getPluginObject()->isActive();
                    })
                    ->withVisibilityCallable(function () : bool {
                        return self::plugin()->getPluginObject()->isActive();
                    }), $container_object->getObject()->getId());
            }, self::srContainerObjectMenu()->containerObjects()->getContainerObjects());
        }

        return $this->top_items;
    }


    /**
     * @param AbstractBaseItem $entry
     * @param int              $obj_id
     *
     * @return AbstractBaseItem
     */
    protected function symbol(AbstractBaseItem $entry, int $obj_id) : AbstractBaseItem
    {
        if (self::version()->is6()) {
            $type = strtoupper(self::dic()->objDataCache()->lookupType($obj_id));

            if (defined(Standard::class . "::" . $type)) {
                // Most core objects
                $entry = $entry->withSymbol(self::dic()
                    ->ui()
                    ->factory()
                    ->symbol()
                    ->icon()
                    ->standard(constant(Standard::class . "::" . $type), ilSrContainerObjectMenuPlugin::PLUGIN_NAME)
                    ->withIsOutlined(true));
            } else {
                // Other core objects & plugin objects
                $icon = ilObject::_getIcon($obj_id);

                $icon_outlined = str_replace("/images/icon_", "/images/outlined/icon_", $icon);
                if ($icon !== $icon_outlined && file_exists($icon_outlined)) {
                    $icon = $icon_outlined;
                }

                $entry = $entry->withSymbol(self::dic()->ui()->factory()->symbol()->icon()->custom($icon, ilSrContainerObjectMenuPlugin::PLUGIN_NAME));
            }
        }

        self::dic()->appEventHandler()->raise(IL_COMP_PLUGIN . "/" . ilSrContainerObjectMenuPlugin::PLUGIN_NAME, ilSrContainerObjectMenuPlugin::EVENT_CHANGE_MENU_ENTRY, [
            "entry"  => &$entry, // Unfortunately ILIAS Raise Event System not supports return results so use a referenced variable
            "obj_id" => $obj_id
        ]);

        return $entry;
    }
}
