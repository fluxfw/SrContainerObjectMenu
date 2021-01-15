### Container objects

You can select repository container objects in the plugin config

The plugin provides it as main menu dropdowns items with the objects children

You can adjust the position and custom title of container objects in the main menu configuration

#### Container objects table

![Container objects table](../doc/images/container_objects_table.png)

#### Add container object form

![Add container object form](../doc/images/add_container_object_form.png)

#### Edit container object form

![Edit container object form](../doc/images/edit_container_object_form.png)

#### Repository object

![Repository object](../doc/images/repository_object.png)

#### Main menu

![Main menu](../doc/images/main_menu.png)

### Areas

If you use much container objects, on which the user has access (For example admin users), the main menu may will confused, and the display much main menu items

You can create areas and assign container objects to it

An additional main menu container item will be provided, in which the user can select an area and only its container objects will be shown in main menu and makes the main menu clear

The selection of area is personally and retains after relogin tooo

Container objects without areas will display still for the user, independently of area selection

#### Areas table

![Areas table](../doc/images/areas_table.png)

#### Add area form

![Add area form](../doc/images/add_area_form.png)

#### Edit area form

![Edit area form](../doc/images/edit_area_form.png)

#### Main menu items table

![Main menu items table](../doc/images/main_menu_items_table.png)

#### Main menu area 1

![Main menu area 1](../doc/images/main_menu_area_1.png)

#### Main menu area 2

![Main menu area 2](../doc/images/main_menu_area_2.png)

#### Main menu area 3

![Main menu area 3](../doc/images/main_menu_area_3.png)

### Known issues

#### Lost main menu items

May some lost items will stay in the main menu administration, if you change the position of children or delete children

If you delete container objects or areas from the plugin or uninstall the plugin, the main menu items are automatic clean up

Otherwise, you can ignore the lost main menu items because not viewed in the menu or delete it manually

The plugin has a manual action too for delete lost menu items of the plugin, which is more advanced than the core action is

### Use selected area color and title in skin

You can use the selected area color and title in your skin like with CSS native variables, which the plugin are delivered

```
background-color: var(--srcontainerobjectmenu_area_color, #000000);
content: var(--srcontainerobjectmenu_area_title, Empty);
```

The second parameter is a fallback value, if the plugin is disabled in the update state

Or use in JS too

```js
getComputedStyle(document.documentElement).getPropertyValue("--srcontainerobjectmenu_area_color") || "#000000";
getComputedStyle(document.documentElement).getPropertyValue("--srcontainerobjectmenu_area_title") || "Empty";
```

### Custom event plugins

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
	public function handleEvent(/*string*/ $a_component, /*string*/ $a_event, /*array*/ $a_parameter)/* : void*/ {
		switch ($a_component) {
			case IL_COMP_PLUGIN . "/" . ilSrContainerObjectMenuPlugin::PLUGIN_NAME:
				switch ($a_event) {
					case ilSrContainerObjectMenuPlugin::EVENT_...:
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
