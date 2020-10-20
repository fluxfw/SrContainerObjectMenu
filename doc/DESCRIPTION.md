# SrContainerObjectMenu ILIAS Plugin Description

Select some repository container objects in the plugin config and the plugin provides it as main menu dropdowns with the objects children

Config:

![Config 1](./images/config_1.png)

![Config 2](./images/config_2.png)

![Config 3](./images/config_3.png)

Menu:

![Menu](./images/menu.png)

Known issues:
- Currently may some lost items will stay in the main menu administration (You can ignore it because not viewed in the menu or delete it manually)

## Custom event plugins
If you need to adapt some custom SrContainerObjectMenu changes which can not be configured to your needs, SrContainerObjectMenu will trigger some events, you can listen and react to this in an other custom plugin (plugin type is no matter)

First create or extend a `plugin.xml` in your custom plugin (You need to adapt `PLUGIN_ID` with your own plugin id) to tell ILIAS, your plugins wants to listen to SrContainerObjectMenu events (You need also to increase your plugin version for take effect)

```xml
<?php xml version = "1.0" encoding = "UTF-8"?>
<plugin id="PLUGIN_ID">
	<events>
		<event id="Plugins/SrContainerObjectMenu" type="listen" />
	</events>
</plugin>
```

In your plugin class implement or extend the `handleEvent` method

```php
...
require_once __DIR__ . "/../../SrContainerObjectMenu/vendor/autoload.php";
...
class ilXPlugin extends ...
...
	/**
	 * @inheritDoc
	 */
	public function handleEvent(/*string*/ $a_component, /*string*/ $a_event, /*array*/ $a_parameter)/*: void*/ {
		switch ($a_component) {
			case IL_COMP_PLUGIN . "/" . ilSrContainerObjectMenuPlugin::PLUGIN_NAME:
				switch ($a_event) {
					case ilSrContainerObjectMenuPlugin::EVENT_...;
						...
						break;

					default:
						break;
				}
				break;

			default:
				break;
		}
	}
...
```

| Event | Parameters | Purpose |
|-------|------------|---------|
| `ilSrContainerObjectMenuPlugin::EVENT_CHANGE_MENU_ENTRY` | `entry => &AbstractBaseItem`<br>`obj_id => int` | Change menu entry (Please note `entry` is a reference variable, if it should not works) |
