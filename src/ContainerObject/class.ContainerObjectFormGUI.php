<?php

namespace srag\Plugins\SrContainerObjectMenu\ContainerObject;

use ilAdministrationGUI;
use ilNonEditableValueGUI;
use ilObjMainMenuGUI;
use ilRepositorySelector2InputGUI;
use ilSrContainerObjectMenuPlugin;
use ilUtil;
use srag\CustomInputGUIs\SrContainerObjectMenu\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\SrContainerObjectMenu\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class ContainerObjectFormGUI
 *
 * @package           srag\Plugins\SrContainerObjectMenu\ContainerObject
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_Calls      srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObjectFormGUI: ilFormPropertyDispatchGUI
 */
class ContainerObjectFormGUI extends PropertyFormGUI
{

    use SrContainerObjectMenuTrait;
    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    const LANG_MODULE = ContainerObjectsGUI::LANG_MODULE;
    /**
     * @var ContainerObject
     */
    protected $container_object;


    /**
     * ContainerObjectFormGUI constructor
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
    protected function getValue(/*string*/ $key)
    {
        switch ($key) {
            case "obj_ref_id":
                if (!empty($this->container_object->getContainerObjectId())) {
                    return $this->container_object->getObject()->getTitle() . ($this->container_object->getMenuItem() !== null ? " (" . $this->container_object->getMenuItem()->getDefaultTitle() . ")"
                            : "");
                } else {
                    return $this->container_object->getObjRefId();
                }
                break;

            default:
                return Items::getter($this->container_object, $key);
        }
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        if (!empty($this->container_object->getContainerObjectId())) {
            $this->addCommandButton(ContainerObjectGUI::CMD_UPDATE_CONTAINER_OBJECT, $this->txt("save"));
        } else {
            $this->addCommandButton(ContainerObjectGUI::CMD_CREATE_CONTAINER_OBJECT, $this->txt("add"));
            $this->addCommandButton(ContainerObjectGUI::CMD_BACK, $this->txt("cancel"));
        }
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*: void*/
    {
        if (!empty($this->container_object->getContainerObjectId())) {
            self::dic()->ctrl()->setParameterByClass(ilObjMainMenuGUI::class, "ref_id", 69);

            ilUtil::sendInfo(self::plugin()->translate("info", self::LANG_MODULE, [
                self::output()->getHTML(self::dic()->ui()->factory()->link()->standard(self::dic()->language()->txt("obj_mme"), self::dic()->ctrl()->getLinkTargetByClass([
                    ilAdministrationGUI::class,
                    ilObjMainMenuGUI::class
                ])))
            ]));
        }

        $this->fields = [
            "obj_ref_id" => (empty($this->container_object->getContainerObjectId())
                ? [
                    self::PROPERTY_CLASS    => ilRepositorySelector2InputGUI::class,
                    self::PROPERTY_REQUIRED => true,
                    "setTitle"              => $this->txt("container_object"),
                    "setSelectableTypes"    => [["cat", "crs", "fold", "grp", "root"]]
                ]
                : [
                    self::PROPERTY_CLASS => ilNonEditableValueGUI::class,
                    "setTitle"           => $this->txt("container_object")
                ])
        ];
    }


    /**
     * @inheritDoc
     */
    protected function initId()/*: void*/
    {

    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->txt(!empty($this->container_object->getContainerObjectId()) ? "edit_container_object" : "add_container_object"));
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(/*string*/ $key, $value)/*: void*/
    {
        switch ($key) {
            case "obj_ref_id":
                if (empty($this->container_object->getContainerObjectId())) {
                    $this->container_object->setObjRefId($value);
                }
                break;

            default:
                Items::setter($this->container_object, $key, $value);
                break;
        }
    }


    /**
     * @inheritDoc
     */
    public function storeForm() : bool
    {
        if (!parent::storeForm()) {
            return false;
        }

        self::srContainerObjectMenu()->containerObjects()->storeContainerObject($this->container_object);

        return true;
    }
}
