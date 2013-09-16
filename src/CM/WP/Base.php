<?php

if (!class_exists('CM_WP_Base')) {

    abstract class CM_WP_Base {

        /**
         * Slug of the plugin/theme
         * 
         * @var string
         */
        protected $slug;


        /**
         * Prefix used to namespace various items created
         * @var string
         */
        protected $prefix;

        /**
         * Stores registered custom post types
         * @var array
         */
        protected $post_types = array();

        /**
         * Array of registered modules
         *
         * Each module is instantiated as an object & stored with the array key of
         * it's name
         *
         * @var array
         */
        protected $registered_modules = array();

        /**
         * Prepares the plugin prefix
         *
         * @throws CM_WP_Exception_InvalidSlugException If $slug contains invalid characters
         * 
         * @param string $slug The slug that the plugin or theme is identified by
         */
        protected function __construct( $slug ) {

            $this->slug = $slug;

            // Check slug for invalid characters
            $valid_chars = 'a-z 0-9 _';
            $valid_regex = '/^[' . str_replace(' ', '', $valid_chars) . ']+$/';
            if ( ! preg_match( $valid_regex, $slug ) ) {
                throw new CM_WP_Exception_InvalidSlugException( $slug, $valid_chars );
            }

            // Set the prefix to the slug with the _s stripped out
            $this->prefix = str_replace( '_', '', $slug );


            // Add specialise hooks
            add_action( 'parse_request', array( $this, 'handle_forms' ) );
        }



        /*********************
         * Post Type Helpers *
         *********************/

        /**
         * Creates a post type object that will be used to register a post type
         * during the 'init' action hook
         * 
         * @param string $name Slug for the post being registered (will be pre-fixed
         *                     by the plugin prefix)
         * @param array  $args (optional) Array of arguments for registering the new
         *                     post type.
         *                     Possible arguments are...
         *                     - class      - Class name to use to register the post
         *                                    type.
         *                                    Should be a class that extends the 
         *                                    CM_WP_Element_PostType class
         *                     - post_class - Class name to use for post objects.
         *                                    Should be a class that extends the
         *                                    CM_WP_Element_PostType_Post class
         *                     - slug       - The slug for the post.  Will be
         *                                    prefixed by the plugin/theme prefix.
         *                                    If not provided, will be created from
         *                                    the $name value
         * 
         * @return CM_WP_PostType
         */
        public function register_post_type( $name, $args = array() ) {

            // Fill gaps in $args with defaults
            $defaults = array(
                'class'      => 'CM_WP_Element_PostType',
            );
            $args = wp_parse_args( $args, $defaults );

            $class = $args['class'];
            unset( $args['class'] );

            $post_type = $class::register( $this, $name, $args );

            $this->post_types[$post_type->get_slug()] = $post_type;

            return $post_type;
        }


        /*******************
         * Rewrite Helpers *
         *******************/

        /**
         * Adds a rewrite rule for a custom permalink format
         *
         * @param string $regex         Regex defining the URL to match  This should
         *                              not include the regex delimiters or the
         *                              leading '^' as these are added automatically
         *                              by WordPress
         * @param string $redirect      (optional) Where to redirect to.
         *                              If not provided, will redirect to index.php
         *                              This may not seem to be much use as WordPress
         *                              relies on parameters to determine the page to
         *                              display, however when used in conjunction
         *                              with the CM_WP_Rewrite::is_handled_by()
         *                              method that can be used to handle the request
         * @param string $position      Position to add the rewrite (top|bottom).
         *                              See WP add_rewrite_rule() function for
         *                              explanation
         * @param array  $register_tags Array of tags to register
         *
         * @return CM_WP_Element_Rewrite Returns an object that can then be used to
         *                               add a request handler
         */
        public function custom_uri(
            $regex,
            $redirect = 'index.php',
            $position = 'top',
            $register_tags = array()
        ) {
            // Register the new rewrite
            $rewrite = CM_WP_Element_Rewrite::register(
                $this,
                $regex,
                $redirect,
                $position
            );

            $rewrite->register_tags( $register_tags );

            return $rewrite;
        }



        /********************
         * Shortcode helpers *
         ********************/

        /**
         * Registers a shortcode with WordPres, & returns an object to 
         * 
         * @param string $shortcode Shortcode to be added
         */
        public function add_shrotcode( $shortcode ) {
            $shortcode = CM_WP_Element_Shortcode::register( $this, $shortcode );

            return $shortcode;
        }



        /******************
         * Module methods *
         ******************/

        /**
         * Works out the class name for a give module
         * 
         * @param string $module module name
         *
         * @return string
         */
        protected function get_module_class( $module ) {
            return 'CM_WP_Module_' .
                str_replace(
                    ' ',
                    '',
                    ucwords(
                        str_replace( '_', ' ', $module )
                    )
                );
        }

        /**
         * Registers a plugin/theme module
         * 
         * @param string $module       Name of the module
         * @param string $module_class (optional) Class name to use for the module
         *                             If not provided, class name will be determined
         *                             by calling $this->get_module_class()
         *
         * @return CM_WP_Module Module object
         */
        public function register_module( $module, $module_class = null ) {

            if ( is_null( $module_class ) ) {
                $module_class = $this->get_module_class( $module );
            }

            if ( ! isset( $this->registered_modules[$module] ) ) {
                $this->registered_modules[$module] = $module_class::load( $this );
            } else {
                $this->registered_modules[$module]->also_registered_by( $this );
            }

            return $this->registered_modules[$module];
        }


        public function load_module( $module ) {

            if ( ! isset( $this->registered_modules[$module] ) ) {
                throw new CM_WP_Exception_Module_NotAvailableException( $module );
                
            }

            return $this->registered_modules[$module];
        }



        /************************
         * Other useful methods *
         ************************/

        /**
         * Displays the WP 404 page template
         *
         * Code courtesy of http://bit.ly/16c5MiF
         *
         * @return void
         */
        public function display_404() {
            status_header(404);
            nocache_headers();
            include( get_404_template() );
            exit;
        }




        /****************
         * Useful hooks *
         ****************/

        /**
         * Handles any submitted forms
         * 
         * @return void
         */
        public function handle_forms() {
            // We're only interested in forms with a frm_action
            if ( empty( $_POST['frm_action'] ) ) {
                return;
            }

            do_action( "frm_action_{$_POST['frm_action']}" );
        }




        /**************************
         * Getters, setters, etc. *
         **************************/

        /**
         * Returns the plugin/theme slug
         * 
         * @return string
         */
        public function get_slug() {
            return $this->slug;
        }


        /**
         * Returns the plugin/theme prefix
         * 
         * @return string
         */
        public function get_prefix() {
            return $this->prefix;
        }
    }
}