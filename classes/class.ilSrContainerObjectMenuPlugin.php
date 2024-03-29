<?php

require_once __DIR__ . "/../vendor/autoload.php";

use ILIAS\DI\Container;
use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticPluginMainMenuProvider;
use srag\CustomInputGUIs\SrContainerObjectMenu\Loader\CustomInputGUIsLoaderDetector;
use srag\DataTableUI\SrContainerObjectMenu\Implementation\Utils\DataTableUITrait;
use srag\DevTools\SrContainerObjectMenu\DevToolsCtrl;
use srag\Plugins\SrContainerObjectMenu\Utils\SrContainerObjectMenuTrait;
use srag\RemovePluginDataConfirm\SrContainerObjectMenu\PluginUninstallTrait;

/**
 * Class ilSrContainerObjectMenuPlugin
 */
class ilSrContainerObjectMenuPlugin extends ilUserInterfaceHookPlugin
{

    use PluginUninstallTrait;
    use SrContainerObjectMenuTrait;
    use DataTableUITrait;

    const EVENT_CHANGE_MENU_ENTRY = "change_menu_entry";
    const PLUGIN_CLASS_NAME = self::class;
    const PLUGIN_ID = "srcontobjmenu";
    const PLUGIN_NAME = "SrContainerObjectMenu";
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * ilSrContainerObjectMenuPlugin constructor
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @inheritDoc
     */
    public function exchangeUIRendererAfterInitialization(Container $dic) : Closure
    {
        return CustomInputGUIsLoaderDetector::exchangeUIRendererAfterInitialization();
    }


    /**
     * @inheritDoc
     */
    public function getPluginName() : string
    {
        return self::PLUGIN_NAME;
    }


    /**
     * @inheritDoc
     */
    public function promoteGlobalScreenProvider() : AbstractStaticPluginMainMenuProvider
    {
        return self::srContainerObjectMenu()->menu()->factory()->instance();
    }


    /**
     * @inheritDoc
     */
    public function updateLanguages(/*?array*/ $a_lang_keys = null) : void
    {
        parent::updateLanguages($a_lang_keys);

        $this->installRemovePluginDataConfirmLanguages();

        self::dataTableUI()->installLanguages(self::plugin());

        DevToolsCtrl::installLanguages(self::plugin());
    }


    /**
     * @inheritDoc
     */
    protected function deleteData() : void
    {
        self::srContainerObjectMenu()->dropTables();
    }


    /**
     * @inheritDoc
     */
    protected function shouldUseOneUpdateStepOnly() : bool
    {
        return true;
    }
}
