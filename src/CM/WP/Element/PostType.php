<?php

if (!class_exists('CM_WP_Element_PostType')) {

    class CM_WP_Element_PostType {

        /***************************************
         * Static factory properties & methods *
         ***************************************/

        /**
         * Array of registered post
         * @var array
         */
        static protected $registered_post_types = array();

        /**
         * Flag to indicate whether the action hook to register the posts has been
         * added
         * @var boolean
         */
        static protected $registration_hooked = false;

        
        /**
         * Returns a constructed CM_WP_Element_PostType object
         *
         * @param CM_WP_Base $owner The the plugin/theme object that this belongs to
         * @param string     $name  Slug for the post being registered (will be
         *                          pre-fixed by the plugin prefix)
         * @param array      $slug  (optional) The slug for the post.  Will be
         *                          prefixed  by the plugin/theme prefix.
         *                          If not provided, will be created from the $name
         *                          value
         *
         * @return CM_WP_Element_PostType
         */
        public function register( $owner, $name, $slug = null ) {

            // If slug is not provided, build it from the $name value
            if ( empty( $slug ) ) {
                $slug = str_replace( '-', '_', sanitize_title( $name ) );
            }

            // Add the plugin/theme prefix to the slug
            $prefixed_slug = "{$owner->get_prefix()}_{$slug}";

            if ( isset( self::$registered_post_types[$prefixed_slug] ) ) {
                throw new
                    CM_WP_Exception_Element_PostType_AlreadyRegisteredException(
                        $slug
                    );
                
            }

            // Now build the new object
            self::$registered_post_types[$prefixed_slug] = 
                new CM_WP_Element_PostType( $owner, $prefixed_slug, $name );

            if ( ! self::$registration_hooked ) {
                add_action( 'init', array( __CLASS__, 'register_post_types') );
                self::$registration_hooked = true;
            }

            return self::$registered_post_types[$prefixed_slug];
        }



        /************************
         * Other static methods *
         ************************/

        public function register_post_types() {
            foreach ( self::$registered_post_types as $post_type ) {
                $post_type->register_with_wp();
            }
        }


        /*******************************
         * Object properties & methods *
         *******************************/

        /**
         * Plugin/Theme object that this plugin belongs to
         * @var CM_WP_Base
         */
        protected $owner;

        /**
         * Slug to use when registering the post_type
         * @var string
         */
        protected $slug;

        /**
         * Name of the post type (label)
         * @var string
         */
        protected $name;

        /**
         * Sets the object slug & name (label)
         * 
         * @param string $slug Slug used to register post with.  WIll be prefixes by
         *                     the plugin/theme prefix
         * @param string $name Name to use for the label for the plugin
         */
        protected function __construct( CM_WP_Base $owner, $slug, $name )
        {
            $this->owner = $owner;
            $this->slug = $slug;
            $this->name  = $name;
        }

        /**
         * Registered the post type with WordPress
         * 
         * @return void
         */
        protected function register_with_wp() {
        }
    }
}