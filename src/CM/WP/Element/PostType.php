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
         *                          Name should be the plural term
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
         * Labels for the post type
         * @var array
         */
        protected $labels = array();

        /**
         * Default register_post_type arguments
         * @var array
         */
        protected $post_args = array(
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true, 
            'show_in_menu' => true, 
            'query_var' => true,
            'rewrite' => array( 'slug' => 'book' ),
            'capability_type' => 'post',
            'has_archive' => true, 
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
        );

        /**
         * Sets the object slug & name (label)
         * 
         * @param string $slug Slug used to register post with.  WIll be prefixes by
         *                     the plugin/theme prefix
         * @param string $name Name to use for the label for the plugin
         */
        protected function __construct( CM_WP_Base $owner, $slug, $name )
        {
            $this->owner  = $owner;
            $this->slug   = $slug;

            // Build the labels array from the $name
            $this->set_labels( $name );
            
        }


        protected function set_labels( $plural_label, $single_label = null ) {

            if ( is_null( $single_label ) ) {
                $single_label = preg_replace('/s$/', '', $plural_label);
            }

            $this->labels = array(
                'name'               => $plural_label,
                'singular_name'      => $single_label,
                'add_new'            => "Add New {$single_label}",
                'add_new_item'       => "Add New {$single_label}",
                'edit_item'          => "Edit {$single_label}",
                'new_item'           => "New {$single_label}",
                'all_items'          => "All {$plural_label}",
                'view_item'          => "View {$single_label}",
                'search_items'       => "Search {$plural_label}",
                'not_found'          => sprintf(
                    "No %s found",
                    strtolower($plural_label)
                ),
                'not_found_in_trash' => sprintf(
                    "No %s found in Trash",
                    strtolower($plural_label)
                ),
                'parent_item_colon'  => "",
                'menu_name'          => $plural_label,
            );
        }

        /**
         * Builds the register_post_type arguments form the object properties
         * 
         * @return array
         */
        protected function prepare_post_args() {
            $args = $this->post_args;
            $args['labels'] = $this->labels;

            return $args;
        }

        /**
         * Registered the post type with WordPress
         * 
         * @return void
         */
        protected function register_with_wp() {
            register_post_type( $this->slug, $this->prepare_post_args() );
        }
    }
}