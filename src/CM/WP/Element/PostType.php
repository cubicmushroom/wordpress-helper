<?php

if (!class_exists('CM_WP_Element_PostType')) {

    class CM_WP_Element_PostType extends CM_WP_Element {

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
        static protected $hook_registered = false;

        
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

            if ( ! self::$hook_registered ) {
                add_action( 'init', array( __CLASS__, 'register_post_types') );
                self::$hook_registered = true;
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
            parent::__construct( $owner );
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



        /**
         * Gets a post as a CM_WP_Post object
         *
         * @param  int|WP_Post $post The post to get
         *
         * @throws CM_WP_Exception_Element_PostType_PostNotFoundException if the post
         *         of this type is not found
         *
         * @return CM_WP_Post
         */
        public function get( $post ) {
            if ( $post instanceof WP_Post ) {
                $post_id = $post->ID;
            } else {
                $post_id = (int) $post;
            }

            $post_obj = get_post( &$post );

            if ( empty( $post_obj ) ) {
                throw new CM_WP_Exception_Element_PostType_PostNotFoundException(
                    "Unable to find post with ID {$post_id}"
                );
            }

            if ( $this->slug !== $post_obj->post_type ) {
                throw new CM_WP_Exception_Element_PostType_PostNotFoundException(
                    "Post ID {$post_id} is not of {$this->slug} post type"
                );
            }

            return CM_WP_Element_PostType_Post::create_from_post( $post_obj );
        }


        /**************************
         * Getters, setters, etc. *
         **************************/


        /**
         * Returns the post type slug
         * 
         * @return string
         */
        public function get_slug() {
            return $this->slug;
        }


        /**
         * Adds post metadata
         *
         * @param string  $meta_key   Meta key to store under
         * @param string  $meta_value Value to store
         * @param boolean $unique     Whether or not you want the key to stay unique.
         *                            When set to true, the custom field will not be
         *                            added if the given key already exists among
         *                            custom fields of the specified post.
         *                            Default: false
         *
         * @return bool Boolean true, except if the $unique argument was set to true
         *              and a custom field with the given key already exists, in
         *              which case false is returned.
         */
        public function add_meta( $meta_key, $meta_value, $unique = false ) {
            return add_post_meta( $this->get_ID(), $meta_key, $meta_value, $unique );
        }


        /**
         * Updates the value of an existing meta key (custom field) for the specified
         * post
         *
         * @param string $meta_key   The key of the custom field you will edit.
         * @param string $meta_value The new value of the custom field. A passed
         *                           array will be serialized into a string.
         * @param string $prev_value (optional) The old value of the custom field you
         *                           wish to change. This is to differentiate between
         *                           several fields with the same key. If omitted,
         *                           and there are multiple rows for this post and
         *                           meta key, all meta values will be updated.
         *
         * @return mixed Returns meta_id if the meta doesn't exist, otherwise returns
         *               true on success and false on failure. NOTE: If the
         *               meta_value passed to this function is the same as the value
         *               that is already in the database, this function returns false
         */
        public function update_post_meta( $meta_key, $meta_value, $prev_value = '' ) {
            return update_post_meta(
                $this->get_ID(),
                $meta_key,
                $meta_value,
                $prev_value
            );
        }
    }
}