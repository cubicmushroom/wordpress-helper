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
    


General Methods
---------------

These are available to post Plugins & Themes

### Register a Custom Post Type

To register a post types, just call the register_post_type() method on the theme or plugin object…

    $packages_pt = $PluginName->register_post_type( 'Packages' );
    


### Adding rewrite rules

To add a rewrite rule for a plugin/theme…

    $PluginName->custom_url( $regex, $rewrite, $position );


To add an automatic handler for the rewrite…

    $PluginName->custom_url( $regex, [$rewrite, $position] )->is_handled_by( $callback );




Roadmap
-------

1. Add automatic plugin activation hook to flush permalinks for rewrite rules & post types