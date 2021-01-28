<?php

namespace srag\Plugins\SrContainerObjectMenu\ContainerObject\Form;

use ilAdministrationGUI;
use ilDBConstants;
use ilMMTopItemGUI;
use ilNonEditableValueGUI;
use ilObjMainMenuGUI;
use ilRepositorySelector2InputGUI;
use ilSrContainerObjectMenuPlugin;
use srag\CustomInputGUIs\SrContainerObjectMenu\FormBuilder\AbstractFormBuilder;
use srag\CustomInputGUIs\SrContainerObjectMenu\InputGUIWrapperUIInputComponent\InputGUIWrapperUIInputComponent;
use srag\CustomInputGUIs\SrContainerObjectMenu\MultiSelectSearchNewInputGUI\MultiSelectSearchNewInputGUI;
use srag\Plugins\SrContainerObjectMenu\Area\Area;
use srag\Plugins\SrContainerObjectMenu\Area\AreasCtrl;
use srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObject;
use srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObjectCtrl;
use srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObjectsCtrl;
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
     * @param ContainerObjectCtrl $parent
     * @param ContainerObject     $container_object
     */
    public function __construct(ContainerObjectCtrl $parent, ContainerObject $container_object)
    {
        $this->container_object = $container_object;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    public function render() : string
    {
        if (!empty($this->container_object->getContainerObjectId())) {
            self::dic()
                ->ctrl()
                ->setParameterByClass(ilObjMainMenuGUI::class, "ref_id",
                    self::dic()->database()->queryF('SELECT ref_id FROM object_data INNER JOIN object_reference ON object_data.obj_id=object_reference.obj_id WHERE type=%s',
                        [ilDBConstants::T_TEXT], ["mme"])->fetchAssoc()["ref_id"]);

            $this->messages[] = self::dic()->ui()->factory()->messageBox()->info(self::plugin()->translate("info", ContainerObjectsCtrl::LANG_MODULE, [
                self::output()->getHTML(self::dic()->ui()->factory()->link()->standard(self::dic()->language()->txt("obj_mme"), self::dic()->ctrl()->getLinkTargetByClass([
                    ilAdministrationGUI::class,
                    ilObjMainMenuGUI::class,
                    ilMMTopItemGUI::class
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

        if (!empty($this->container_object->getContainerObjectId())) {
            $buttons[ContainerObjectCtrl::CMD_UPDATE_CONTAINER_OBJECT] = self::plugin()->translate("save", ContainerObjectsCtrl::LANG_MODULE);
        } else {
            $buttons[ContainerObjectCtrl::CMD_CREATE_CONTAINER_OBJECT] = self::plugin()->translate("add", ContainerObjectsCtrl::LANG_MODULE);
            $buttons[ContainerObjectCtrl::CMD_BACK] = self::plugin()->translate("cancel", ContainerObjectsCtrl::LANG_MODULE);
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
            $data["obj_ref_id"] = $this->container_object->getTitle();
        } else {
            $data["obj_ref_id"] = null;
        }
        $data["areas"] = $this->container_object->getAreaIds();

        return $data;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $fields = [];

        if (!empty($this->container_object->getContainerObjectId())) {
            $fields["obj_ref_id"] = new InputGUIWrapperUIInputComponent(new ilNonEditableValueGUI(self::plugin()->translate("container_object", ContainerObjectsCtrl::LANG_MODULE)));
        } else {
            $fields["obj_ref_id"] = (new InputGUIWrapperUIInputComponent(new ilRepositorySelector2InputGUI(self::plugin()->translate("container_object", ContainerObjectsCtrl::LANG_MODULE),
                "obj_ref_id", null, self::class)))->withRequired(true);
            $fields["obj_ref_id"]->getInput()->getExplorerGUI()->setSelectableTypes(["cat", "crs", "fold", "grp", "root"]);
        }

        $fields["areas"] = new InputGUIWrapperUIInputComponent(new MultiSelectSearchNewInputGUI(self::plugin()->translate("areas", AreasCtrl::LANG_MODULE)));
        $fields["areas"]->getInput()->setOptions(array_reduce(self::srContainerObjectMenu()->areas()->getAreas(), function (array $areas, Area $area) : array {
            $areas[$area->getAreaId()] = $area->getTitle2();

            return $areas;
        }, []));

        return $fields;
    }


    /**
     * @inheritDoc
     */
    protected function getTitle() : string
    {
        if (!empty($this->container_object->getContainerObjectId())) {
            return self::plugin()->translate("edit_container_object", ContainerObjectsCtrl::LANG_MODULE);
        } else {
            return self::plugin()->translate("add_container_object", ContainerObjectsCtrl::LANG_MODULE);
        }
    }


    /**
     * @inheritDoc
     */
    protected function storeData(array $data)/* : void*/
    {
        if (empty($this->container_object->getContainerObjectId())) {
            $this->container_object->setObjRefId(intval($data["obj_ref_id"]));
        }

        $this->container_object->setAreaIds(MultiSelectSearchNewInputGUI::cleanValues((array) $data["areas"]));

        self::srContainerObjectMenu()->containerObjects()->storeContainerObject($this->container_object);
    }
}
