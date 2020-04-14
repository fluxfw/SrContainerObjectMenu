<?php

namespace srag\Plugins\SrContainerObjectMenu\ContainerObject\Form;

use ilAdministrationGUI;
use ilNonEditableValueGUI;
use ilObjMainMenuGUI;
use ilRepositorySelector2InputGUI;
use ilSrContainerObjectMenuPlugin;
use ilUtil;
use srag\CustomInputGUIs\SrContainerObjectMenu\FormBuilder\AbstractFormBuilder;
use srag\CustomInputGUIs\SrContainerObjectMenu\InputGUIWrapperUIInputComponent\InputGUIWrapperUIInputComponent;
use srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObject;
use srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObjectGUI;
use srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObjectsGUI;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class FormBuilder
 *
 * @package      srag\Plugins\SrContainerObjectMenu\ContainerObject\Form
 *
 * @author       studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_Calls srag\Plugins\SrContainerObjectMenu\ContainerObject\Form\FormBuilder: ilFormPropertyDispatchGUI
 */
class FormBuilder extends AbstractFormBuilder
{

    use SrContainerObjectMenuTrait;
    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    /**
     * @var ContainerObject
     */
    protected $container_object;


    /**
     * @inheritDoc
     *
     * @param ContainerObjectGUI $parent
     * @param ContainerObject    $container_object
     */
    public function __construct(ContainerObjectGUI $parent, ContainerObject $container_object)
    {
        $this->container_object = $container_object;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getButtons() : array
    {
        $buttons = [];

        if (!empty($this->container_object->getContainerObjectId())) {
            $buttons[ContainerObjectGUI::CMD_UPDATE_CONTAINER_OBJECT] = self::plugin()->translate("save", ContainerObjectsGUI::LANG_MODULE);
        } else {
            $buttons[ContainerObjectGUI::CMD_CREATE_CONTAINER_OBJECT] = self::plugin()->translate("add", ContainerObjectsGUI::LANG_MODULE);
            $buttons[ContainerObjectGUI::CMD_BACK] = self::plugin()->translate("cancel", ContainerObjectsGUI::LANG_MODULE);
        }

        return $buttons;
    }


    /**
     * @inheritDoc
     */
    protected function getData() : array
    {
        $data = [];

        if (!empty($this->container_object->getContainerObjectId())) {
            $data["obj_ref_id"] = $this->container_object->getObject()->getTitle() . " (" . $this->container_object->getMenuTitle() . ")";
        } else {
            $data["obj_ref_id"] = null;
        }

        return $data;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $fields = [];

        if (!empty($this->container_object->getContainerObjectId())) {
            $fields["obj_ref_id"] = new InputGUIWrapperUIInputComponent(new ilNonEditableValueGUI(self::plugin()->translate("container_object", ContainerObjectsGUI::LANG_MODULE)));
        } else {
            $fields["obj_ref_id"] = (new InputGUIWrapperUIInputComponent(new ilRepositorySelector2InputGUI(self::plugin()->translate("container_object", ContainerObjectsGUI::LANG_MODULE),
                "obj_ref_id", null, self::class)))->withRequired(true);
            $fields["obj_ref_id"]->getInput()->getExplorerGUI()->setSelectableTypes(["cat", "crs", "fold", "grp", "root"]);
        }

        return $fields;
    }


    /**
     * @inheritDoc
     */
    protected function getTitle() : string
    {
        if (!empty($this->container_object->getContainerObjectId())) {
            return self::plugin()->translate("edit_container_object", ContainerObjectsGUI::LANG_MODULE);
        } else {
            return self::plugin()->translate("add_container_object", ContainerObjectsGUI::LANG_MODULE);
        }
    }


    /**
     * @inheritDoc
     */
    public function render() : string
    {
        if (!empty($this->container_object->getContainerObjectId())) {
            self::dic()->ctrl()->setParameterByClass(ilObjMainMenuGUI::class, "ref_id", 69);

            ilUtil::sendInfo(self::plugin()->translate("info", ContainerObjectsGUI::LANG_MODULE, [
                self::output()->getHTML(self::dic()->ui()->factory()->link()->standard(self::dic()->language()->txt("obj_mme"), self::dic()->ctrl()->getLinkTargetByClass([
                    ilAdministrationGUI::class,
                    ilObjMainMenuGUI::class
                ])))
            ]));
        }

        return parent::render();
    }


    /**
     * @inheritDoc
     */
    protected function storeData(array $data)/*:void*/
    {
        if (empty($this->container_object->getContainerObjectId())) {
            $this->container_object->setObjRefId(intval($data["obj_ref_id"]));
        }

        self::srContainerObjectMenu()->containerObjects()->storeContainerObject($this->container_object);
    }
}
