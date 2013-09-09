<?php

if (!class_exists('CM_WP_Element_Shortcode')) {

    class CM_WP_Element_Shortcode extends CM_WP_Element {
        
        /***************************************
         * Static factory properties & methods *
         ***************************************/
        
        /**
         * Stores all the registered shortcodes
         * @var array
         */
        static protected $registered_shortcodes = array();

        /**
         * Registers a shorcode with WordPress, returning a CM_WP_Element_Shortcode
         * object for further configuring the shortcode functionality
         *
         * @param CM_WP_Core $owner     Plugin/theme that this shortcode belongs to
         * @param string     $shortcode The string that will be the shortcode
         * 
         * @return CM_WP_Element_Shortcode
         */
        static public function register( CM_WP_Base $owner, $shortcode ) {

            if ( ! isset( self::$registered_shortcodes[$shortcode] ) ) {
                $class = get_called_class();
                self::$registered_shortcodes[$shortcode] = new $class(
                    $owner,
                    $shortcode
                );
            }

            return self::$registered_shortcodes[$shortcode];
        }



        /*******************************
         * Object properties & methods *
         *******************************/

        /**
         * Callback used to return content of the shortcode
         * @var callable
         */
        protected $handler;

        /**
         * Default attributes for the shortcode
         * @var array
         */
        protected $default_attributes = array();

        /**
         * Flag to indicate whether the shortcode has actually been added to
         * WordPress.
         *
         * @var bool
         */
        protected $is_added = false;


        /**
         * Sets the owning plugin/theme that this shortcode belongs to
         */
        public function __construct( CM_WP_Core $owner, $shortcode ) {
            parent::__construct( $owner );

            add_shortcode( $shortcode, array( $this, 'so_shortcode' ) );
        }


        /**
         * Sets the default attributes for the shortcode
         *
         * @param  array  $default_attributes Default values
         * 
         * @return $this (for method chaining)
         */
        public function default_attributes_are( array $default_attributes ) {
            $this->default_attributes = $default_attributes;

            return $this;
        }

        /**
         * Sets the handler callback for the shortcode
         *
         * @param callable $handler Callable handler that will be used to return the
         *                          shortcode content
         *
         * @return $this (for method chaining)
         */
        protected function is_handled_by( $handler ) {
            if ( ! is_callable( $handler ) ) {
                throw new CM_WP_Exception_InvalidCallbackException( $handler );
            }

            return $this;
        }






        /**
         * Does the actual rendering of the shortcode content
         *
         * Calls the registered handler for the shorcode
         *
         * @param array $attributes Attributes set on the shortcode
         * 
         * @return string Text to replace the shortcode string with
         */
        public function do_shortcode( $attributes ) {

            // Check we have a registered handler
            if ( ! isset( $this->handler ) ) {
                throw new CM_WP_Exception_Element_Shortcode_NoHandlerException();
            }

            $full_attributes = shortcode_atts(
                $this->default_attributes,
                $attributes
            );

            return call_user_func( $this->handler, $full_attributes );
        }
    }
}