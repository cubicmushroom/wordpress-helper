<?php

if (!class_exists('CM_WP_Plugin')) {

    class CM_WP_Plugin extends CM_WP_Base {

        /***************************************
         * Static factory properties & methods *
         ***************************************/

        /**
         * Stores all registered plugins
         * 
         * @var array
         */
        static protected $plugins = array();
        
        /**
         * Registers a new plugin object
         *
         * @param string $slug The slug used to identify this plugin
         * @param string $file The full path to the plugin's root file
         *
         * @throws CM_WP_Exception_PluginAlreadyRegisteredException If a plugin has
         *         already been registered with that slug
         * 
         * @return CM_WP_Plugin
         */
        static public function register( $slug, $file ) {

            // Check that this plugin has not already been registered
            if ( isset( self::$plugins[$slug] ) ) {
                throw new CM_WP_Exception_PluginAlreadyRegisteredException( $slug );
            }

            // Create the plugin object
            self::$plugins[$slug] = new CM_WP_Plugin( $slug, $file );

            return self::$plugins[$slug];
        }

        /**
         * Loads a previously registered plugin
         * 
         * @param string $slug Slug of the plugin required
         *
         * @throws CM_WP_Exception_PluginNotRegisteredException If the plugin has not
         *         been registered
         * 
         * @return CM_WP_Plugin
         */
        static public function load( $slug ) {

            // Check that the plugin requested is registered
            if ( ! isset( self::$plugins[$slug] ) ) {
                throw new CM_WP_Exception_PluginNotRegisteredException( $slug );
            }

            return self::$plugins[$slug];
        }









        /*******************************
         * Object properties & methods *
         *******************************/

        /**
         * Plugin's main file
         * 
         * @var string
         */
        protected $file;

        /**
         * Sets the root file for the plugin for use within function calls
         *
         * This method is protected as should be called from the static create
         * factory method
         * 
         * @param string $slug The slug used to identify this plugin used to build
         *                     prefix
         * @param string $file The full path to the plugin's root file
         */
        protected function __construct( $slug, $file )
        {
            $this->file = $file;

            // Call the CM_WP_Base constructor to set the plugin prefix (used to
            // namespace various items)
            parent::__construct( $slug );
        }




        /**************************
         * Getters, setters, etc. *
         **************************/


        public function get_file() {
            return $this->file;
        }
    }
}