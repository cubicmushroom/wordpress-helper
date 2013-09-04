WordPress Helper
================

Library to make common WordPress tasks easier

Plugins
-------

A plugin object allow some plugin tasks to be performed easier that through the typical functions.

The plugin class is planned to support modules so that each plugin can add additional functionality to the standard plugin object.

### To register a new plugin

In the plugin root file...

    $PluginName = CM_WP_Plugin::register( 'plugin_slug', __FILE__ );
    

To access a plugin object that has been registered anywhere within WordPress…

    $PluginName = CM_WP_Plugin::load( 'plugin_slug' );