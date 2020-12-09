<?php

namespace srag\Plugins\SrContainerObjectMenu\Menu;

use ILIAS\GlobalScreen\Identification\IdentificationInterface;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\AbstractBaseItem;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\isItem;
use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticPluginMainMenuProvider;
use ILIAS\UI\Component\Symbol\Icon\Standard;
use ilLink;
use ilObject;
use ilSrContainerObjectMenuPlugin;
use ilUIPluginRouterGUI;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\Area\Area;
use srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObject;
use srag\Plugins\SrContainerObjectMenu\SelectedArea\SelectAreaCtrl;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class BaseMenu
 *
 * @package srag\Plugins\SrContainerObjectMenu\Menu
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class BaseMenu extends AbstractStaticPluginMainMenuProvider
{

    use DICTrait;
    use SrContainerObjectMenuTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    /**
     * @var array|null
     */
    protected $sub_items = null;
    /**
     * @var IdentificationInterface[]
     */
    protected $top_identifiers = [];
    /**
     * @var array|null
     */
    protected $top_items = null;


    /**
     *
     */
    public function ensureProvideNoItems()/* : void*/
    {
        $this->top_items = [];
        $this->top_identifiers = [];
        $this->sub_items = [];
    }


    /**
     * @inheritDoc
     */
    public function getStaticSubItems() : array
    {
        if ($this->sub_items === null) {
            $this->sub_items = array_merge(array_reduce(self::srContainerObjectMenu()->containerObjects()->getContainerObjects(),
                function (array $sub_items, ContainerObject $container_object) : array {
                    $position = 0;

                    foreach ($container_object->getChildren() as $child_id => $child_title) {

                        $position += 10;

                        $sub_items[] = $this->symbol($this->mainmenu->link($this->if->identifier($container_object->getMenuIdentifier($child_id, $position)))
                            ->withParent($this->top_identifiers[$container_object->getMenuIdentifier()])
                            ->withTitle($child_title)
                            ->withAction(ilLink::_getLink($child_id))
                            ->withPosition($position)
                            ->withAvailableCallable(function () : bool {
                                return self::plugin()->getPluginObject()->isActive();
                            })
                            ->withVisibilityCallable(function () use ($container_object, $child_id) : bool {
                                return $container_object->isVisible($child_id, true);
                            }), self::dic()->objDataCache()->lookupObjId($child_id));
                    }

                    return $sub_items;
                }, []), array_map(function (int $position, Area $area) : isItem {
                $position = (($position + 1) * 10);

                self::dic()->ctrl()->setParameterByClass(SelectAreaCtrl::class, SelectAreaCtrl::GET_PARAM_AREA_ID, $area->getAreaId());

                return $this->symbolArea($this->mainmenu->link($this->if->identifier($area->getMenuIdentifier($position)))
                    ->withParent($this->top_identifiers[$this->getAreasMenuIdentifier()])
                    ->withTitle($area->getTitle())
                    ->withAction(str_replace("\\", "%5C", self::dic()->ctrl()->getLinkTargetByClass([ilUIPluginRouterGUI::class, SelectAreaCtrl::class], SelectAreaCtrl::CMD_SELECT_AREA)))
                    ->withPosition($position)
                    ->withAvailableCallable(function () : bool {
                        return self::plugin()->getPluginObject()->isActive();
                    })
                    ->withVisibilityCallable(function () use ($area) : bool {
                        return $area->isVisible();
                    }));
            }, array_keys(self::srContainerObjectMenu()->areas()->getAreas()), self::srContainerObjectMenu()->areas()->getAreas()));
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
                $top_item = $this->symbol($this->mainmenu->topParentItem($this->if->identifier($container_object->getMenuIdentifier()))
                    ->withTitle($container_object->getObjectTitle())
                    ->withAvailableCallable(function () : bool {
                        return self::plugin()->getPluginObject()->isActive();
                    })
                    ->withVisibilityCallable(function () use ($container_object) : bool {
                        return $container_object->isVisible();
                    }), $container_object->getObject()->getId());

                $this->top_identifiers[$container_object->getMenuIdentifier()] = $top_item->getProviderIdentification();

                return $top_item;
            }, self::srContainerObjectMenu()->containerObjects()->getContainerObjects());

            $top_item = $this->symbolArea($this->mainmenu->topParentItem($this->if->identifier($this->getAreasMenuIdentifier()))
                ->withTitle(self::srContainerObjectMenu()->selectedArea()->getSelectedArea(self::dic()->user()->getId())->getTitle())
                ->withAvailableCallable(function () : bool {
                    return self::plugin()->getPluginObject()->isActive();
                })
                ->withVisibilityCallable(function () : bool {
                    return (!empty(self::srContainerObjectMenu()->areas()->getAreas(true)));
                }));
            $this->top_identifiers[$this->getAreasMenuIdentifier()] = $top_item->getProviderIdentification();
            $this->top_items[] = $top_item;
        }

        return $this->top_items;
    }


    /**
     * @return string
     */
    protected function getAreasMenuIdentifier() : string
    {
        return self::srContainerObjectMenu()->areas()->factory()->newInstance()->getMenuIdentifier();
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


    /**
     * @param AbstractBaseItem $entry
     *
     * @return AbstractBaseItem
     */
    protected function symbolArea(AbstractBaseItem $entry) : AbstractBaseItem
    {
        if (self::version()->is6()) {
            $entry = $entry->withSymbol(self::dic()->ui()->factory()->symbol()->icon()->standard(Standard::ITGR, ilSrContainerObjectMenuPlugin::PLUGIN_NAME)->withIsOutlined(true));
        }

        return $entry;
    }
}
