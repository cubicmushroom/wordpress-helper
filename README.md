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

Additionally, you can provide an array of arguments as the 2nd parameter…

    $packages_pt = $PluginName->register_post_type( 'Packages', array(
    	'class'      => '<Name of that extends the CM_WP_Element_PostType class>',
        'post_class' => '<Name of that extends the CM_WP_Element_PostType_Post class>',
    	'slug'       => '<slug to be used when registering post (will be prefixed by the plugin/theme prefix.>',
    ) );


### Adding rewrite rules

To add a rewrite rule for a plugin/theme…

    $PluginName->custom_url( $regex, $rewrite, $position );


To add an automatic handler for the rewrite…

    $PluginName->custom_url( $regex, [$rewrite, $position] )->is_handled_by( $callback );


You can also provide additional tags to register as the 4th parameter in 2 forms…

	// This will register tags with the tag regex of /([^&]+)/
	$additional_tags = array(
		'<tag_name>',
		'<tag_name>',
	);
    $PluginName->custom_url( $regex, $rewrite, $position, $additional_tags );

	// This will register tags with the provided tag regex
	$additional_tags = array(
		'<tag_name>' => '<regex>',
		'<tag_name>' => '<regex>,
	);
    $PluginName->custom_url( $regex, $rewrite, $position, $additional_tags );

	// You can also use a combination of these…
	$additional_tags = array(
		'<tag_name>',
		'<tag_name>' => '<regex>,
	);
    $PluginName->custom_url( $regex, $rewrite, $position, $additional_tags );





Modules
-------

Modules are CM_WP_Module class objects that a plugin or theme can register to add additional functionality.

To write a plugin create a class using the naming convention `CP_WP_Module_<ModuleName>` that extends the `CM_WP_Module` class.

To load the module in a plugin or theme make the following call…

    $plugin->register_module( '<module_name>' )
    
The module name will be converted to camel case to work out the class name of the module to load.


Every plugin/theme that registers the module is stored in the module's `$registereed_by` property array.

The directory & the URI of the plugin/theme that registered the module are available in the `$dir` & `$uri` properties, respectfully.  This allows for the loading of files from additional libraries within the plugin/theme directory.  These properties are set immediately after the module object is instantiated, but not during.  There for if these are needed for setup, setup should be performed in the `initialise()` method, that is called immediately after the `$dir` & `$uri` properties are set.


Roadmap
-------

1. Add automatic plugin activation hook to flush permalinks for rewrite rules & post types