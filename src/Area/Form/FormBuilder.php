<?php

namespace srag\Plugins\SrContainerObjectMenu\Area\Form;

use ilAdministrationGUI;
use ilDBConstants;
use ilMMSubItemGUI;
use ilObjMainMenuGUI;
use ilSrContainerObjectMenuPlugin;
use ilTextInputGUI;
use srag\CustomInputGUIs\SrContainerObjectMenu\ColorPickerInputGUI\ColorPickerInputGUI;
use srag\CustomInputGUIs\SrContainerObjectMenu\FormBuilder\AbstractFormBuilder;
use srag\CustomInputGUIs\SrContainerObjectMenu\InputGUIWrapperUIInputComponent\InputGUIWrapperUIInputComponent;
use srag\CustomInputGUIs\SrContainerObjectMenu\MultiSelectSearchNewInputGUI\MultiSelectSearchNewInputGUI;
use srag\CustomInputGUIs\SrContainerObjectMenu\TabsInputGUI\MultilangualTabsInputGUI;
use srag\CustomInputGUIs\SrContainerObjectMenu\TabsInputGUI\TabsInputGUI;
use srag\Plugins\SrContainerObjectMenu\Area\Area;
use srag\Plugins\SrContainerObjectMenu\Area\AreaCtrl;
use srag\Plugins\SrContainerObjectMenu\Area\AreasCtrl;
use srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObject;
use srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObjectsCtrl;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class FormBuilder
 *
 * @package  srag\Plugins\SrContainerObjectMenu\Area\Form
 */
class FormBuilder extends AbstractFormBuilder
{

    use SrContainerObjectMenuTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    /**
     * @var Area
     */
    protected $area;


    /**
     * @inheritDoc
     *
     * @param AreaCtrl $parent
     * @param Area     $area
     */
    public function __construct(AreaCtrl $parent, Area $area)
    {
        $this->area = $area;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    public function render() : string
    {
        if (!empty($this->area->getAreaId())) {
            self::dic()
                ->ctrl()
                ->setParameterByClass(ilObjMainMenuGUI::class, "ref_id",
                    self::dic()->database()->queryF('SELECT ref_id FROM object_data INNER JOIN object_reference ON object_data.obj_id=object_reference.obj_id WHERE type=%s',
                        [ilDBConstants::T_TEXT], ["mme"])->fetchAssoc()["ref_id"]);

            $this->messages[] = self::dic()->ui()->factory()->messageBox()->info(self::plugin()->translate("info", ContainerObjectsCtrl::LANG_MODULE, [
                self::output()->getHTML(self::dic()->ui()->factory()->link()->standard(self::dic()->language()->txt("obj_mme"), self::dic()->ctrl()->getLinkTargetByClass([
                    ilAdministrationGUI::class,
                    ilObjMainMenuGUI::class,
                    ilMMSubItemGUI::class
                ])))
            ]));
        }

        return parent::render();
    }


    /**
     * @inheritDoc
     */
    protected function getButtons() : array
    {
        $buttons = [];

        if (!empty($this->area->getAreaId())) {
            $buttons[AreaCtrl::CMD_UPDATE_AREA] = self::plugin()->translate("save", AreasCtrl::LANG_MODULE);
        } else {
            $buttons[AreaCtrl::CMD_CREATE_AREA] = self::plugin()->translate("add", AreasCtrl::LANG_MODULE);
            $buttons[AreaCtrl::CMD_BACK] = self::plugin()->translate("cancel", AreasCtrl::LANG_MODULE);
        }

        return $buttons;
    }


    /**
     * @inheritDoc
     */
    protected function getData() : array
    {
        $data = [
            "titles"                => $this->area->getTitles(),
            "container_objects"     => $this->area->getContainerObjectsIds(),
            "color"                 => $this->area->getColor(),
            "link_container_object" => [$this->area->getLinkContainerObjectId()],
        ];

        return $data;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $fields = [
            "titles"                => (new InputGUIWrapperUIInputComponent(new TabsInputGUI(self::plugin()->translate("title", AreasCtrl::LANG_MODULE))))->withRequired(true),
            "container_objects"     => new InputGUIWrapperUIInputComponent(new MultiSelectSearchNewInputGUI(self::plugin()->translate("container_objects", ContainerObjectsCtrl::LANG_MODULE))),
            "color"                 => new InputGUIWrapperUIInputComponent(new ColorPickerInputGUI(self::plugin()->translate("color", AreasCtrl::LANG_MODULE))),
            "link_container_object" => new InputGUIWrapperUIInputComponent(new MultiSelectSearchNewInputGUI(self::plugin()->translate("link_container_object", AreasCtrl::LANG_MODULE))),
        ];
        MultilangualTabsInputGUI::generateLegacy($fields["titles"]->getInput(), [
            new ilTextInputGUI(self::plugin()->translate("title", AreasCtrl::LANG_MODULE), "title")
        ], true);

        $container_object_options = array_reduce(self::srContainerObjectMenu()->containerObjects()->getContainerObjects(),
            function (array $container_objects, ContainerObject $container_object) : array {
                $container_objects[$container_object->getContainerObjectId()] = $container_object->getTitle();

                return $container_objects;
            }, []);
        $fields["container_objects"]->getInput()->setOptions($container_object_options);

        $fields["link_container_object"]->getInput()->setOptions($container_object_options);
        $fields["link_container_object"]->getInput()->setLimitCount(1);

        $fields["color"]->getInput()->setDefaultColor("");

        return $fields;
    }


    /**
     * @inheritDoc
     */
    protected function getTitle() : string
    {
        if (!empty($this->area->getAreaId())) {
            return self::plugin()->translate("edit_area", AreasCtrl::LANG_MODULE);
        } else {
            return self::plugin()->translate("add_area", AreasCtrl::LANG_MODULE);
        }
    }


    /**
     * @inheritDoc
     */
    protected function storeData(array $data)/* : void*/
    {
        if (empty($this->area->getAreaId())) {
            self::srContainerObjectMenu()->areas()->storeArea($this->area);
        }

        $this->area->setTitles((array) ($data["titles"]));
        $this->area->setContainerObjectsIds(MultiSelectSearchNewInputGUI::cleanValues((array) $data["container_objects"]));
        $this->area->setColor(strval($data["color"]));
        $this->area->setLinkContainerObjectId(current(MultiSelectSearchNewInputGUI::cleanValues((array) $data["link_container_object"])) ?: null);

        self::srContainerObjectMenu()->areas()->storeArea($this->area);
    }
}
