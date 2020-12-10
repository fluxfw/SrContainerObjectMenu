<?php

namespace srag\Plugins\SrContainerObjectMenu\Area;

use ilConfirmationGUI;
use ilSrContainerObjectMenuPlugin;
use ilUtil;
use srag\DIC\SrContainerObjectMenu\DICTrait;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class AreaCtrl
 *
 * @package           srag\Plugins\SrContainerObjectMenu\Area
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrContainerObjectMenu\Area\AreaCtrl: srag\Plugins\SrContainerObjectMenu\Area\AreasCtrl
 */
class AreaCtrl
{

    use DICTrait;
    use SrContainerObjectMenuTrait;

    const CMD_ADD_AREA = "addArea";
    const CMD_BACK = "back";
    const CMD_CREATE_AREA = "createArea";
    const CMD_EDIT_AREA = "editArea";
    const CMD_REMOVE_AREA = "removeArea";
    const CMD_REMOVE_AREA_CONFIRM = "removeAreaConfirm";
    const CMD_UPDATE_AREA = "updateArea";
    const GET_PARAM_AREA_ID = "area_id";
    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    const TAB_EDIT_AREA = "edit_area";
    /**
     * @var Area
     */
    protected $area;


    /**
     * AreaCtrl constructor
     */
    public function __construct()
    {

    }


    /**
     *
     */
    public function executeCommand()/* : void*/
    {
        $this->area = self::srContainerObjectMenu()->areas()->getAreaById(intval(filter_input(INPUT_GET, self::GET_PARAM_AREA_ID)));

        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_AREA_ID);

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_ADD_AREA:
                    case self::CMD_BACK:
                    case self::CMD_CREATE_AREA:
                    case self::CMD_EDIT_AREA:
                    case self::CMD_REMOVE_AREA:
                    case self::CMD_REMOVE_AREA_CONFIRM:
                    case self::CMD_UPDATE_AREA:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     * @return Area
     */
    public function getArea() : Area
    {
        return $this->area;
    }


    /**
     *
     */
    protected function addArea()/* : void*/
    {
        $form = self::srContainerObjectMenu()->areas()->factory()->newFormBuilderInstance($this, $this->area);

        self::output()->output($form);
    }


    /**
     *
     */
    protected function back()/* : void*/
    {
        self::dic()->ctrl()->redirectByClass(AreasCtrl::class, AreasCtrl::CMD_LIST_AREAS);
    }


    /**
     *
     */
    protected function createArea()/* : void*/
    {
        $form = self::srContainerObjectMenu()->areas()->factory()->newFormBuilderInstance($this, $this->area);

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }

        self::dic()->ctrl()->setParameter($this, self::GET_PARAM_AREA_ID, $this->area->getAreaId());

        ilUtil::sendSuccess(self::plugin()->translate("added_area", AreasCtrl::LANG_MODULE, [$this->area->getTitle()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_EDIT_AREA);
    }


    /**
     *
     */
    protected function editArea()/* : void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_EDIT_AREA);

        $form = self::srContainerObjectMenu()->areas()->factory()->newFormBuilderInstance($this, $this->area);

        self::output()->output($form);
    }


    /**
     *
     */
    protected function removeArea()/* : void*/
    {
        self::srContainerObjectMenu()->areas()->deleteArea($this->area);

        ilUtil::sendSuccess(self::plugin()->translate("removed_area", AreasCtrl::LANG_MODULE, [$this->area->getTitle()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_BACK);
    }


    /**
     *
     */
    protected function removeAreaConfirm()/* : void*/
    {
        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

        $confirmation->setHeaderText(self::plugin()->translate("remove_area_confirm", AreasCtrl::LANG_MODULE, [$this->area->getTitle()]));

        $confirmation->addItem(self::GET_PARAM_AREA_ID, $this->area->getAreaId(), $this->area->getTitle());

        $confirmation->setConfirm(self::plugin()->translate("remove", AreasCtrl::LANG_MODULE), self::CMD_REMOVE_AREA);
        $confirmation->setCancel(self::plugin()->translate("cancel", AreasCtrl::LANG_MODULE), self::CMD_BACK);

        self::output()->output($confirmation);
    }


    /**
     *
     */
    protected function setTabs()/* : void*/
    {
        self::dic()->tabs()->clearTargets();

        self::dic()->tabs()->setBackTarget(self::plugin()->translate("areas", AreasCtrl::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_BACK));

        if ($this->area !== null) {
            if (self::dic()->ctrl()->getCmd() === self::CMD_REMOVE_AREA_CONFIRM) {
                self::dic()->tabs()->addTab(self::TAB_EDIT_AREA, self::plugin()->translate("remove_area", AreasCtrl::LANG_MODULE), self::dic()->ctrl()
                    ->getLinkTarget($this, self::CMD_REMOVE_AREA_CONFIRM));
            } else {
                self::dic()->tabs()->addTab(self::TAB_EDIT_AREA, self::plugin()->translate("edit_area", AreasCtrl::LANG_MODULE), self::dic()->ctrl()
                    ->getLinkTarget($this, self::CMD_EDIT_AREA));

                self::dic()->locator()->addItem($this->area->getTitle(), self::dic()->ctrl()->getLinkTarget($this, self::CMD_EDIT_AREA));
            }
        } else {
            $this->area = self::srContainerObjectMenu()->areas()->factory()->newInstance();

            self::dic()->tabs()->addTab(self::TAB_EDIT_AREA, self::plugin()->translate("add_area", AreasCtrl::LANG_MODULE), self::dic()->ctrl()
                ->getLinkTarget($this, self::CMD_ADD_AREA));
        }
    }


    /**
     *
     */
    protected function updateArea()/* : void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_EDIT_AREA);

        $form = self::srContainerObjectMenu()->areas()->factory()->newFormBuilderInstance($this, $this->area);

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }

        ilUtil::sendSuccess(self::plugin()->translate("saved_area", AreasCtrl::LANG_MODULE, [$this->area->getTitle()]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_EDIT_AREA);
    }
}
