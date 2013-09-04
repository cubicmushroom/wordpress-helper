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
         * @return CM_WP_Plugin
         */
        public function register( $slug, $file ) {

            // Check that this plugin has not already been registered
            if ( isset( self::$plugins[$slug] ) ) {
                throw new CM_WP_Exception_PluginAlreadyRegisteredException( $slug );
            }

            // Create the plugin object
            $this->plugins[$slug] = new CM_WP_Plugin( $file );

            return $this->plugins[$slug];
        }

        /**
         * Sets the root file for the plugin for use within function calls
         *
         * This method is protected as should be called from the static create
         * factory method
         * 
         * @param string $file The full path to the plugin's root file
         */
        protected function __construct( $file )
        {
            $this->file = $file;
        }




        /**************************
         * Getters, setters, etc. *
         **************************/


        public function get_file() {
            return $this->file;
        }
    }
}