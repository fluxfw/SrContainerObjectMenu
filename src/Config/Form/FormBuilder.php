<?php

namespace srag\Plugins\SrContainerObjectMenu\Config\Form;

use ilSrContainerObjectMenuPlugin;
use ilTextInputGUI;
use srag\CustomInputGUIs\SrContainerObjectMenu\FormBuilder\AbstractFormBuilder;
use srag\CustomInputGUIs\SrContainerObjectMenu\InputGUIWrapperUIInputComponent\InputGUIWrapperUIInputComponent;
use srag\CustomInputGUIs\SrContainerObjectMenu\TabsInputGUI\MultilangualTabsInputGUI;
use srag\CustomInputGUIs\SrContainerObjectMenu\TabsInputGUI\TabsInputGUI;
use srag\Plugins\SrContainerObjectMenu\Config\Config;
use srag\Plugins\SrContainerObjectMenu\Config\ConfigCtrl;
use srag\Plugins\SrContainerObjectMenu\SelectedArea\SelectedArea;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;

/**
 * Class FormBuilder
 *
 * @package  srag\Plugins\SrContainerObjectMenu\Config\Form
 *
 * @author   studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class FormBuilder extends AbstractFormBuilder
{

    use SrContainerObjectMenuTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectMenuPlugin::class;
    /**
     * @var Config
     */
    protected $config;


    /**
     * @inheritDoc
     *
     * @param ConfigCtrl $parent
     * @param Config     $config
     */
    public function __construct(ConfigCtrl $parent, Config $config)
    {
        $this->config = $config;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getButtons() : array
    {
        $buttons = [
            ConfigCtrl::CMD_UPDATE_CONFIG => self::plugin()->translate("save", ConfigCtrl::LANG_MODULE)
        ];

        return $buttons;
    }


    /**
     * @inheritDoc
     */
    protected function getData() : array
    {
        $data = [
            "selected_area_menu_titles" => $this->config->getSelectedAreaMenuTitles()
        ];

        return $data;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $fields = [
            "selected_area_menu_titles" => (new InputGUIWrapperUIInputComponent(new TabsInputGUI(self::plugin()
                ->translate("selected_area_menu_title", ConfigCtrl::LANG_MODULE))))->withByline(self::plugin()
                ->translate("selected_area_menu_title_info", ConfigCtrl::LANG_MODULE, [SelectedArea::AREA_TITLE_PLACEHOLDER, SelectedArea::AREA_MENU_TITLE_PLACEHOLDER]))
        ];
        MultilangualTabsInputGUI::generateLegacy($fields["selected_area_menu_titles"]->getInput(), [
            new ilTextInputGUI(self::plugin()->translate("selected_area_menu_title", ConfigCtrl::LANG_MODULE), "selected_area_menu_title")
        ], true);

        return $fields;
    }


    /**
     * @inheritDoc
     */
    protected function getTitle() : string
    {
        return self::plugin()->translate("configuration", ConfigCtrl::LANG_MODULE);
    }


    /**
     * @inheritDoc
     */
    protected function storeData(array $data)/* : void*/
    {
        $this->config->setSelectedAreaMenuTitles((array) $data["selected_area_menu_titles"]);

        self::srContainerObjectMenu()->config()->storeConfig($this->config);
    }
}
