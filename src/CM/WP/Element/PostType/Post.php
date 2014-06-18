<?php

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
        $class = get_called_class();
	    /** @var CM_WP_Element_PostType_Post $post_obj */
	    $post_obj = new $class;
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





    /**
     * Updated the saved post data
     *
     * @return $this
     */
    protected function update_post() {
        $post = get_post( $this->get_ID() );

        $this->wp_post = $post;

        return $this;
    }

    /**
     * Saves the current post to the database
     *
     * @return $this
     */
    protected function save() {
        wp_update_post( $this->wp_post );
    }


    /**
     * Publishes the post
     * @return void
     */
    public function publish() {
        $this->set_status( 'publish' );
    }





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
        if ( isset( $this->wp_post ) ) {
            throw new RuntimeException( "Post has already been set" );
        }

        $this->wp_post = $post;
    }


    /**
     * Returns the ID of the post
     *
     * @return int
     */
    public function get_ID() {
        return $this->wp_post->ID;
    }


    /**
     * Sets the post status
     *
     * @param string $status The status to set to
     *
     * @return void
     */
    public function set_status( $status ) {
        $this->update_post();
        $this->wp_post->post_status = $status;
        $this->save();
    }

    /**
     * Used to determine if the property on the WP_Post object is set
     *
     * @param string $what property requested
     *
     * @return boolean
     */
    public function __isset( $what ) {
        return isset( $this->wp_post->{$what} );
    }

    /**
     * Used to return properties of the WP_Post object
     *
     * @param string $what Name of the property requested
     *
     * @return mixed       Property of the WP_Post object
     */
    public function __get( $what ) {
        return $this->wp_post->{$what};
    }
}