<?php

if (!class_exists('CM_WP_Element_Rewrite')) {

    class CM_WP_Element_Rewrite {

        /*************************
         * Static factory method *
         *************************/
        
        /**
         * Array of registered rewrites
         * @var array
         */
        static protected $rewrites = array();

        /**
         * Registers a new rewrite
         * 
         * @param CM_WP_Base $owner    Plugin/theme object that this rewrite belongs
         *                             to
         * @param string     $regex    Regex defining the URL to match
         * @param string     $redirect (optional) Where to redirect to.
         *                             If not provided, will redirect to index.php
         *                             This may not seem to be much use as WordPress
         *                             relies on parameters to determine the page to
         *                             display, however when used in conjunction with
         *                             the CM_WP_Rewrite::is_handled_by() method that
         *                             can be used to handle the request
         * @param string     $position Position to add the rewrite (top|bottom).
         *                             See WP add_rewrite_rule() function for
         *                             explanation
         *
         * @return CM_WP_Element_Rewrite Returns an object that can then be used to
         *                               add a request handler
         */
        static public function register(
            CM_WP_Base $owner,
            $regex,
            $redirect = 'index.php',
            $position = 'top'
        ) {

            $rewrite = new 

            self::$rewrites[$rewrite->get_id()] = $rewrite;

            return $rewrite;
        }


        /*******************************
         * Object properties & methods *
         *******************************/

        /**
         * The plugin/theme obejct that this redirect belongs to
         * @var CM_WP_Base
         */
        protected $owner;

        /**
         * Unique ID for this redirect
         * @var [type]
         */
        protected $id;

        /**
         * Generates a unique ID for the rewrite & adds the rewrite rule
         * 
         * @param CM_WP_Base $owner    Plugin/theme object that this rewrite belongs
         *                             to
         * @param string     $regex    Regex defining the URL to match
         * @param string     $redirect (optional) Where to redirect to.
         *                             If not provided, will redirect to index.php
         *                             This may not seem to be much use as WordPress
         *                             relies on parameters to determine the page to
         *                             display, however when used in conjunction with
         *                             the CM_WP_Rewrite::is_handled_by() method that
         *                             can be used to handle the request
         * @param string     $position Position to add the rewrite (top|bottom).
         *                             See WP add_rewrite_rule() function for
         *                             explanation
         */
        public function __construct(
            CM_WP_Base $owner,
            $regex,
            $redirect,
            $position
        ) {
            // Store the owning plugin/theme object
            $this->owner = $owner;

            // Generate a unique ID for the rewrite
            $this->id = md5( $regex );

            // Add the redirect's ID as a parameter to the redirect.  This allows us
            // to hook into request when it's received & handle the request using a
            // callback set using the is_handles_by() method
            $redirect = add_query_arg( array( self::, $id), $redirect );

            // Register the rewrite rule
            add_rewrite_rule( $regex, $redirect, $position );
        }
    }
}