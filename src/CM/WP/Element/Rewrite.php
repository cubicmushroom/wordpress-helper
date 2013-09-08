<?php

if (!class_exists('CM_WP_Element_Rewrite')) {

    class CM_WP_Element_Rewrite extends CM_WP_Element {


        /*************
         * Constants *
         *************/

        const REDIRECT_ID_PARAM = 'cm_wp_rewrite_id';

        /*************************
         * Static factory method *
         *************************/
        
        /**
         * Array of registered rewrites
         * @var array
         */
        static protected $rewrites = array();

        /**
         * Flag to indicate whether the action hook for handling requests has been
         * registered
         * 
         * @var bool
         */
        static protected $hook_registered_for_handler = false;

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

            $rewrite = new CM_WP_Element_Rewrite(
                $owner,
                $regex,
                $redirect,
                $position
            );

            self::$rewrites[$rewrite->get_id()] = $rewrite;

            return $rewrite;
        }




        /*****************************
         * Additional static methods *
         *****************************/

        /**
         * Adds support for the redirect ID query var used for handling automatic
         * permalink handlers
         *
         * @param array $query_vars Existing array of query vars
         *
         * @return array Updated query_vars array with the additional item(s)
         */
        static public function add_redirect_id_query_var( $query_vars ) {
            $query_vars[] = self::REDIRECT_ID_PARAM;

            return $query_vars;
        }
        
        /**
         * 'parse_query' action hook callback used to check for permalink handler
         * 
         * @param WP_Query $query Current query
         * 
         * @return void
         */
        static public function call_handler( $query ) {

            // We're only interested in handling the main query
            if ( ! $query->is_main_query() ) {
                return;
            }

            $redirect_id = get_query_var( self::REDIRECT_ID_PARAM );

            // We're also only interested in pages that have a redirect id (i.e. were
            // created using the CM_WP_Element_Rewrite class)
            if ( empty( $redirect_id ) ) {
                return;
            }

            $rewrite = self::$rewrites[$redirect_id];

            // Try calling the rewrite handler
            $rewrite->handle_query( $query );
        }


        /*******************************
         * Object properties & methods *
         *******************************/

        /**
         * Unique ID for this redirect
         * @var string
         */
        protected $id;

        /**
         * Regex for the rewrite rule
         * @var string
         */
        protected $regex;

        /**
         * Redirect string for the rewrite rule
         * @var string
         */
        protected $redirect;

        /**
         * The position for registering the rewrite rule
         * @var string
         */
        protected $position;

        /**
         * Callback responsible for handling this permalink
         * @var callable
         */
        protected $handler;

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
            parent::__construct( $owner );

            // Generate a unique ID for the rewrite
            $this->id = md5( $regex );

            $this->regex = $regex;

            // Add the redirect's ID as a parameter to the redirect.  This allows us
            // to hook into request when it's received & handle the request using a
            // callback set using the is_handles_by() method
            $redirect_id_param = self::REDIRECT_ID_PARAM;
            $this->redirect = add_query_arg(
                array( $redirect_id_param => $this->id),
                $redirect
            );

            $this->position = $position;

            add_action( 'init', array( $this, 'hook_add_rewrite_rule_to_wp' ) );
        }

        /**
         * Called during the init action hook to register rewrite rules
         *
         * Uses an additional function call, so that this can be called during plugin
         * activation before flushing permalinks
         *
         * @return void
         */
        public function hook_add_rewrite_rule_to_wp() {
            $this->add_rewrite_rule_to_wp();
        }

        /**
         * Adds the rewrite rule to wordpress
         *
         * Called during plugin activation or init hook
         *
         * @return void
         */
        protected function add_rewrite_rule_to_wp() {
            add_rewrite_rule( $this->regex, $this->redirect, $this->position );
        }

        /**
         * Adds a callback to handle the rewrite request
         * 
         * @param callable $callback Callable callback that will be used to handle this callback
         *
         * @throws CM_WP_Exception_InvalidCallbackException if $callback is not
         *         callable
         *
         * @return void
         */
        public function handled_by( $callback ) {

            // Check the callback is a valid callable
            if ( ! is_callable( $callback ) ) {
                throw new CM_WP_Exception_InvalidCallbackException( $callback );
            }

            $this->handler = $callback;

            // If not already added, add the handler hook
            if ( ! self::$hook_registered_for_handler ) {
                // For this to work we need WP to recognise the query var used to
                // pass the rewrite ID
                add_action(
                    'query_vars',
                    array( __CLASS__, 'add_redirect_id_query_var' )
                );

                add_action( 'parse_query', array( __CLASS__, 'call_handler' ) );

                self::$hook_registered_for_handler = true;
            }
        }

        /**
         * Function to handle request
         *
         * Checks to see if there is a handler for this permalink.  If so, calls it.
         *
         * This method is actually called during 'parse_query' action hook to allow
         * access to the query object
         *
         * @param WP_Query $query WordPress query object for the current permalink
         * 
         * @return void
         */
        protected function handle_query( $query ) {
            if ( isset( $this->handler ) ) {
                call_user_func( $this->handler );
            }
        }





        /**************************
         * Getters, setters, etc. *
         **************************/

        /**
         * Returns the ID of the redirect
         * 
         * @return string
         */
        public function get_id() {
            return $this->id;
        }
    }
}