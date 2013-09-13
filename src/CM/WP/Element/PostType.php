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
         * @param array      $args  (optional) Array of additional arguments
         *                          Possible arguments are...
         *                          - $post_class - Name of class to use for post
         *                          objects
         *                          - $slug - The slug for the post.  Will be prefixed
         *                          by the plugin/theme prefix.
         *                          If not provided, will be created from the $name
         *                          value
         *
         * @return CM_WP_Element_PostType
         */
        static public function register( $owner, $name, $args = array() ) {

            // Fill gaps in $args with defaults
            $defaults = array(
                'slug'       => null,
            );
            $args = wp_parse_args( $args, $defaults );

            $slug = $args['slug'];
            unset( $args['slug'] );

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

            $class = get_called_class();

            // Now build the new object
            self::$registered_post_types[$prefixed_slug] = 
                new $class( $owner, $prefixed_slug, $name, $args );

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
            'rewrite' => array( 'slug' => 'change_me' ),
            'capability_type' => 'post',
            'has_archive' => true, 
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
        );


        /**
         * The name of the class used to build post objects
         * @var string
         */
        protected $post_class = 'CM_WP_Element_PostType_Post';

        /**
         * Sets the object slug & name (label)
         *
         * @param CM_WP_Base $owner Plugin/theme that registered this post type
         * @param string $slug Slug used to register post with.  WIll be prefixes by
         *                     the plugin/theme prefix
         * @param string $name Name to use for the label for the plugin
         * @param array $args  Array of additional arguments
         */
        protected function __construct(
            CM_WP_Base $owner,
            $slug,
            $name,
            array $args = array()
        ) {

            parent::__construct( $owner );
            $this->slug = $slug;

            // Build the labels array from the $name
            $this->set_labels( $name );

            // Change the post slug from 'change_me'
            $this->post_args['rewrite']['slug'] = preg_replace(
                '/^' . $owner->get_prefix() . '_/',
                '',
                $this->slug
            );
           
            // If we've been passed a post_class, override the default class
            if ( ! empty( $args['post_class'] ) ) {
                $this->post_class = $args['post_class'];
            }
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

            $post_class = $this->post_class;
            return $post_class::create_from_post( $post_obj );
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
    }
}