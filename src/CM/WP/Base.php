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

            $this->prefix = $slug;
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

            return $post_type;
        }



    }
}