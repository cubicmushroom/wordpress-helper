<?php

if (!class_exists('CM_WP_Base')) {

    class CM_WP_Base {

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
         * Prepares the plugin prefix
         *
         * @throws CM_WP_Exception_InvalidSlugException If $slug contains invalid characters
         * 
         * @param string $slug The slug that the plugin or theme is identified by
         */
        protected function __construct( $slug ) {

            // Check slug for invalid characters
            $valid_chars = 'a-z 0-9 _';
            $valid_regex = '/^[' . str_replace(' ', '', $valid_chars) . ']+$/';
            if ( ! preg_match( $valid_regex, $slug ) ) {
                throw new CM_WP_Exception_InvalidSlugException( $slug, $valid_chars );
            }

            // Set the prefix to the slug with the _s stripped out
            $this->prefix = str_replace( '_', '', $slug );
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
         * @param array  $slug (optional) The slug for the post.  Will be prefixed 
         *                     by the plugin/theme prefix.
         *                     If not provided, will be created from the $name value
         * 
         * @return CM_WP_PostType
         */
        public function register_post_type( $name, $slug = null ) {
            $post_type = CM_WP_Element_PostType::register( $this, $name, $slug );

            $this->post_types[$post_type->get_slug()] = $post_type;

            return $post_type;
        }


        /*******************
         * Rewrite Helpers *
         *******************/

        /**
         * Adds a rewrite rule for a custom permalink format
         *
         * @param string $regex    Regex defining the URL to match  This should not
         *                         include the regex delimiters or the leading '^' as
         *                         these are added automatically by WordPress
         * @param string $redirect (optional) Where to redirect to.
         *                         If not provided, will redirect to index.php
         *                         This may not seem to be much use as WordPress
         *                         relies on parameters to determine the page to
         *                         display, however when used in conjunction with the
         *                         CM_WP_Rewrite::is_handled_by() method that can be
         *                         used to handle the request
         * @param string $position Position to add the rewrite (top|bottom).
         *                         See WP add_rewrite_rule() function for explanation
         *
         * @return CM_WP_Element_Rewrite Returns an object that can then be used to
         *                               add a request handler
         */
        public function custom_uri(
            $regex,
            $redirect = 'index.php',
            $position = 'top'
        ) {
            // Register the new rewrite
            $rewrite = CM_WP_Element_Rewrite::register(
                $this,
                $regex,
                $redirect,
                $position
            );

            return $rewrite;
        }



        /********************
         * Shorcode helpers *
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

        /**************************
         * Getters, setters, etc. *
         **************************/

        public function get_prefix() {
            return $this->prefix;
        }
    }
}