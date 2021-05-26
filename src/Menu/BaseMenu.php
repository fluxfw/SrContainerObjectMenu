<?php

namespace srag\Plugins\SrContainerObjectMenu\Menu;

use ILIAS\GlobalScreen\Identification\IdentificationInterface;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\AbstractBaseItem;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\isItem;
use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticPluginMainMenuProvider;
use ILIAS\UI\Component\Symbol\Icon\Standard;
use ILIAS\UI\Component\Symbol\Symbol;
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
 */
abstract class BaseMenu extends AbstractStaticPluginMainMenuProvider
{

    use DICTrait;
    use SrContainerObjectMenuTrait;

    const BASE_BORDER_STYLE = "2px solid";
    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    /**
     * @var array
     */
    protected $css = [];
    /**
     * @var isItem[]|null
     */
    protected $sub_items = null;
    /**
     * @var IdentificationInterface[]
     */
    protected $top_identifiers = [];
    /**
     * @var isItem[]|null
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
                            ->withAction($container_object->getLink($child_id))
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
                $position = $area->calcPosition($position);

                self::dic()->ctrl()->setParameterByClass(SelectAreaCtrl::class, SelectAreaCtrl::GET_PARAM_AREA_ID, $area->getAreaId());

                $this->css[] = ":root{" . implode("", array_map(function (string $key, string $value) use ($area) : string {
                        return "--" . $area->getMenuIdentifier() . "_" . $key . ":" . $this->escapeCSSValue($value) . ";";
                    }, array_keys($area->getCssVariables()), $area->getCssVariables())) . "}";

                if (!empty($area->getColorHex())) {
                    $this->css[] = $area->getMenuCSSIdentifier($position) . "{border-left:" . self::BASE_BORDER_STYLE . " var(--" . $area->getMenuIdentifier() . "_color)!important;}";
                }

                return $this->symbolArea($this->mainmenu->link($this->if->identifier($area->getMenuIdentifier($position)))
                    ->withParent($this->top_identifiers[self::srContainerObjectMenu()->areas()->getMenuIdentifier()])
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

            $this->deliverCss();
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
                if (!empty($container_object->getAreaColorHex())) {
                    $this->css[] = $container_object->getMenuCSSIdentifier() . "{border-bottom:" . self::BASE_BORDER_STYLE . " var(--" . $container_object->getAreaMenuIdentifier()
                        . "_color)!important;}";
                }

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

            $top_item = $this->symbolArea($this->mainmenu->topParentItem($this->if->identifier(self::srContainerObjectMenu()->areas()->getMenuIdentifier()))
                ->withTitle(self::srContainerObjectMenu()->selectedArea()->getSelectedArea(self::dic()->user()->getId())->getTitle())
                ->withAvailableCallable(function () : bool {
                    return self::plugin()->getPluginObject()->isActive();
                })
                ->withVisibilityCallable(function () : bool {
                    return (count(self::srContainerObjectMenu()->areas()->getAreas(true)) >= 2);
                }));
            $this->top_identifiers[self::srContainerObjectMenu()->areas()->getMenuIdentifier()] = $top_item->getProviderIdentification();
            $this->top_items[] = $top_item;
        }

        return $this->top_items;
    }


    /**
     * @param AbstractBaseItem $entry
     *
     * @return AbstractBaseItem
     */
    protected function cssMenuIdentifierClass(AbstractBaseItem $entry) : AbstractBaseItem
    {
        if (self::version()->is6()) {
            $entry = $entry->addSymbolDecorator(function (Symbol $symbol) use ($entry) : Symbol {
                return $symbol->withAdditionalOnLoadCode(function (string $id) use ($entry) : string {
                    return 'document.getElementById("' . $id . '").parentElement.classList.add("' . self::srContainerObjectMenu()->menu()->getMenuCSSIdentifier($entry->getProviderIdentification()
                            ->getInternalIdentifier(), false) . '");';
                });
            });
        }

        return $entry;
    }


    /**
     *
     */
    protected function deliverCss()/* : void*/
    {
        $selected_area = self::srContainerObjectMenu()->selectedArea()->getSelectedArea(self::dic()->user()->getId());

        if ($selected_area->getArea() !== null) {
            $this->css[] = ":root{" . implode("", array_map(function (string $key) use ($selected_area) : string {
                    return "--" . strtolower(ilSrContainerObjectMenuPlugin::PLUGIN_NAME) . "_area_" . $key . ":var(--" . $selected_area->getAreaMenuIdentifier() . "_" . $key . ");";
                }, array_keys($selected_area->getAreaCssVariables()))) . "}";

            if (!empty($selected_area->getAreaColorHex())) {
                $this->css[] = self::srContainerObjectMenu()->menu()->getMenuCSSIdentifier(self::srContainerObjectMenu()->areas()->getMenuIdentifier()) . "{border-bottom:" . self::BASE_BORDER_STYLE
                    . " var(--"
                    . $selected_area->getAreaMenuIdentifier() . "_color)!important;}";
            }
        }

        if (!empty($this->css)) {
            self::dic()->ui()->mainTemplate()->addInlineCss(implode("\n", $this->css));
            $this->css = [];
        }
    }


    /**
     * @param string $text
     *
     * @return string
     */
    protected function escapeCSSValue(string $text) : string
    {
        return str_replace(["\n", "\r", ";", "<", ">"], "", $text); // TODO: How to escape css variable value?
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

            $entry = $this->cssMenuIdentifierClass($entry);
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

            $entry = $this->cssMenuIdentifierClass($entry);
        }

        return $entry;
    }
}
