<?php

abstract class CM_WP_Module {

    /***************************************
     * Static factory properties & methods *
     ***************************************/

    /**
     * Loads the module, passing in the plugin/theme that registered the module
     *
     * The loading plugin/theme is needed to determine where to load related
     * files from, if the plugin uses them
     *
     * @param CM_WP_Base $owner
     *
     * @return CM_WP_Module
     */
    static public function load( CM_WP_Base $owner ) {

        $class = get_called_class();

        /** @var CM_WP_Module $module */
        $module = new $class( $owner );
        $module->set_owner( $owner );

        // Add hook to initialise module after all plugins & theme is loaded
        if ( $owner instanceof CM_WP_Plugin ) {
            add_action( 'plugins_loaded', array( $module, 'initialise' ), 5 );
        } elseif ( $owner instanceof CM_WP_Theme ) {
            add_action( 'setup_theme', array( $module, 'initialise' ), 5 );
        }

        return $module;
    }
    /*******************************
     * Object properties & methods *
     *******************************/

    /**
     * Plugin/theme that registered this module
     *
     * @var CM_WP_Base|CM_WP_Theme|CM_WP_Plugin
     */
    protected $owner;


    /**
     * Array of plugins/themes that have registered the module
     *
     * @var array
     */
    protected $registered_by = array();


    /**
     * Directory that the module was loaded from
     *
     * This is determined from the plugin/theme that registered the module
     *
     * @var string
     */
    protected $dir;


    /**
     * URI that the module was loaded from
     *
     * This is determined from the plugin/theme that registered the module
     *
     * @var string
     */
    protected $uri;


    /**
     * Initialise method called after the module is instantiated & configured by
     * self::load()
     *
     * This does nothing, but can be overridden by modules if required
     *
     * @return void
     */
    public function initialise() {}



    /**************************
     * Getters, setters, etc. *
     **************************/

    /**
     * Sets the plugin/theme that registered the module
     *
     * @param \CM_WP_Base|\CM_WP_Plugin|\CM_WP_Theme $owner Plugin or theme that registered the
     *                                        module
     *
     * @return $this
     */
    public function set_owner( CM_WP_Base $owner ) {
        $this->owner = $owner;
        $this->registered_by[$owner->get_slug()] = $owner;
        $this->update_dir_uri();

        return $this;
    }



    public function also_registered_by( CM_WP_Base $registered_by ) {
        if (
            ! $registered_by instanceof CM_WP_Plugin &&
            ! $registered_by instanceof CM_WP_Theme
        ) {
            throw new InvalidArgumentException(
                "Owner must be either a CM_WP_Plugin or CM_WP_Theme object"
            );
        }
        $this->registered_by[$registered_by->get_slug()] = $registered_by;
    }


    /**
     * Updates the $dir & $uri properties of the module
     *
     * @return $this
     */
    protected function update_dir_uri() {
        switch ( get_class( $this->owner ) ) {
            case 'CM_WP_Plugin':
	            $this->dir = plugin_dir_path( $this->owner->get_file() );
                $this->uri = plugin_dir_url( $this->owner->get_file() );
                break;
            case 'CM_WP_Theme':
                $this->dir = get_stylesheet_directory();
                $this->uri = get_stylesheet_directory_uri();
                break;
            default:
                throw new InvalidArgumentException(
                    "Owner must be either a CM_WP_Plugin or CM_WP_Theme object"
                );
        }

        return $this;
    }
}