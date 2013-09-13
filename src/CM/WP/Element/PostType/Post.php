<?php

if (!class_exists('CM_WP_Element_PostType_Post')) {

    class CM_WP_Element_PostType_Post {
        
        /***************************************
         * Static factory properties & methods *
         ***************************************/

        /**
         * Creates a CM_WP_Post object from a WP_Post object
         *
         * @param WP_Post $post WP_Post object containing post details
         *
         * @return CM_WP_Element_PostType_Post
         */
        static public function create_from_post( WP_Post $post ) {
            $post_obj = new CM_WP_Element_PostType_Post;
            $post_obj->set_post( $post );

            return $post_obj;
        }



        /*******************************
         * Object properties & methods *
         *******************************/

        /**
         * Stores the WP_Post object containing the post's details
         *
         * @var WP_Post
         */
        protected $wp_post;





        /**************************
         * Getters, setters, etc. *
         **************************/

        /**
         * Sets the post for this object
         *
         * @param WP_Post $post WP_Post object to be set
         *
         * @throws  RuntimeException if the wp_post property has already been set
         *
         * @return void
         */
        protected function set_post( WP_Post $post ) {
            if ( isset( $tthis->wp_post ) ) {
                throw new RuntimeException( "Post has already been set" );
            }

            $this->wp_post = $post;
        }
    }
}