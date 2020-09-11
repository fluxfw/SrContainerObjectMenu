<?php

namespace srag\Plugins\SrContainerObjectMenu\ContainerObject;

use ilConfirmationGUI;
use ilSrContainerObjectMenuPlugin;
use ilUtil;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\ContainerObject\Form\FormBuilder;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class ContainerObjectGUI
 *
 * @package           srag\Plugins\SrContainerObjectMenu\ContainerObject
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObjectGUI: srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObjectsGUI
 * @ilCtrl_Calls      srag\Plugins\SrContainerObjectMenu\ContainerObject\ContainerObjectGUI: srag\Plugins\SrContainerObjectMenu\ContainerObject\Form\FormBuilder
 */
class ContainerObjectGUI
{

    use DICTrait;
    use SrContainerObjectMenuTrait;

    const CMD_ADD_CONTAINER_OBJECT = "addContainerObject";
    const CMD_BACK = "back";
    const CMD_CREATE_CONTAINER_OBJECT = "createContainerObject";
    const CMD_EDIT_CONTAINER_OBJECT = "editContainerObject";
    const CMD_REMOVE_CONTAINER_OBJECT = "removeContainerObject";
    const CMD_REMOVE_CONTAINER_OBJECT_CONFIRM = "removeContainerObjectConfirm";
    const CMD_UPDATE_CONTAINER_OBJECT = "updateContainerObject";
    const GET_PARAM_CONTAINER_OBJECT_ID = "container_object_id";
    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    const TAB_EDIT_CONTAINER_OBJECT = "edit_container_object";
    /**
     * @var ContainerObject
     */
    protected $container_object;


    /**
     * ContainerObjectGUI constructor
     */
    public function __construct()
    {

    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->container_object = self::srContainerObjectMenu()->containerObjects()->getContainerObjectById(intval(filter_input(INPUT_GET, self::GET_PARAM_CONTAINER_OBJECT_ID)));

        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_CONTAINER_OBJECT_ID);

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(FormBuilder::class):
                self::dic()->ctrl()->forwardCommand(self::srContainerObjectMenu()->containerObjects()->factory()->newFormBuilderInstance($this, $this->container_object));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_ADD_CONTAINER_OBJECT:
                    case self::CMD_BACK:
                    case self::CMD_CREATE_CONTAINER_OBJECT:
                    case self::CMD_EDIT_CONTAINER_OBJECT:
                    case self::CMD_REMOVE_CONTAINER_OBJECT:
                    case self::CMD_REMOVE_CONTAINER_OBJECT_CONFIRM:
                    case self::CMD_UPDATE_CONTAINER_OBJECT:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     * @return ContainerObject
     */
    public function getContainerObject() : ContainerObject
    {
        return $this->container_object;
    }


    /**
     *
     */
    protected function addContainerObject()/*: void*/
    {
        $form = self::srContainerObjectMenu()->containerObjects()->factory()->newFormBuilderInstance($this, $this->container_object);

        self::output()->output($form);
    }


    /**
     *
     */
    protected function back()/*: void*/
    {
        self::dic()->ctrl()->redirectByClass(ContainerObjectsGUI::class, ContainerObjectsGUI::CMD_LIST_CONTAINER_OBJECTS);
    }


    /**
     *
     */
    protected function createContainerObject()/*: void*/
    {
        $form = self::srContainerObjectMenu()->containerObjects()->factory()->newFormBuilderInstance($this, $this->container_object);

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }

        self::dic()->ctrl()->setParameter($this, self::GET_PARAM_CONTAINER_OBJECT_ID, $this->container_object->getContainerObjectId());

        ilUtil::sendSuccess(self::plugin()->translate("added_container_object", ContainerObjectsGUI::LANG_MODULE, [$this->container_object->getObject()->getTitle()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_EDIT_CONTAINER_OBJECT);
    }


    /**
     *
     */
    protected function editContainerObject()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_EDIT_CONTAINER_OBJECT);

        $form = self::srContainerObjectMenu()->containerObjects()->factory()->newFormBuilderInstance($this, $this->container_object);

        self::output()->output($form);
    }


    /**
     *
     */
    protected function removeContainerObject()/*: void*/
    {
        self::srContainerObjectMenu()->containerObjects()->deleteContainerObject($this->container_object);

        ilUtil::sendSuccess(self::plugin()->translate("removed_container_object", ContainerObjectsGUI::LANG_MODULE, [$this->container_object->getObject()->getTitle()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_BACK);
    }


    /**
     *
     */
    protected function removeContainerObjectConfirm()/*: void*/
    {
        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

        $confirmation->setHeaderText(self::plugin()->translate("remove_container_object_confirm", ContainerObjectsGUI::LANG_MODULE, [$this->container_object->getObject()->getTitle()]));

        $confirmation->addItem(self::GET_PARAM_CONTAINER_OBJECT_ID, $this->container_object->getContainerObjectId(), $this->container_object->getObject()->getTitle());

        $confirmation->setConfirm(self::plugin()->translate("remove", ContainerObjectsGUI::LANG_MODULE), self::CMD_REMOVE_CONTAINER_OBJECT);
        $confirmation->setCancel(self::plugin()->translate("cancel", ContainerObjectsGUI::LANG_MODULE), self::CMD_BACK);

        self::output()->output($confirmation);
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {
        self::dic()->tabs()->clearTargets();

        self::dic()->tabs()->setBackTarget(self::plugin()->translate("container_objects", ContainerObjectsGUI::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_BACK));

        if ($this->container_object !== null) {
            if (self::dic()->ctrl()->getCmd() === self::CMD_REMOVE_CONTAINER_OBJECT_CONFIRM) {
                self::dic()->tabs()->addTab(self::TAB_EDIT_CONTAINER_OBJECT, self::plugin()->translate("remove_container_object", ContainerObjectsGUI::LANG_MODULE), self::dic()->ctrl()
                    ->getLinkTarget($this, self::CMD_REMOVE_CONTAINER_OBJECT_CONFIRM));
            } else {
                self::dic()->tabs()->addTab(self::TAB_EDIT_CONTAINER_OBJECT, self::plugin()->translate("edit_container_object", ContainerObjectsGUI::LANG_MODULE), self::dic()->ctrl()
                    ->getLinkTarget($this, self::CMD_EDIT_CONTAINER_OBJECT));

                self::dic()->locator()->addItem($this->container_object->getObject()->getTitle(), self::dic()->ctrl()->getLinkTarget($this, self::CMD_EDIT_CONTAINER_OBJECT));
            }
        } else {
            $this->container_object = self::srContainerObjectMenu()->containerObjects()->factory()->newInstance();

            self::dic()->tabs()->addTab(self::TAB_EDIT_CONTAINER_OBJECT, self::plugin()->translate("add_container_object", ContainerObjectsGUI::LANG_MODULE), self::dic()->ctrl()
                ->getLinkTarget($this, self::CMD_ADD_CONTAINER_OBJECT));
        }
    }


    /**
     *
     */
    protected function updateContainerObject()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_EDIT_CONTAINER_OBJECT);

        $form = self::srContainerObjectMenu()->containerObjects()->factory()->newFormBuilderInstance($this, $this->container_object);

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }

        ilUtil::sendSuccess(self::plugin()->translate("saved_container_object", ContainerObjectsGUI::LANG_MODULE, [$this->container_object->getObject()->getTitle()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_EDIT_CONTAINER_OBJECT);
    }
}
