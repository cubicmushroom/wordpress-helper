<?php

if (!class_exists('CM_WP_Element')) {

    abstract class CM_WP_Element {


        /*******************************
         * Object properties & methods *
         *******************************/

        /**
         * Plugin/Theme object that this plugin belongs to
         * @var CM_WP_Base
         */
        protected $owner;

	    /**
	     * Sets the object owner
	     *
	     * @param \CM_WP_Base $owner Plugin/theme that this element belongs to
	     */
        protected function __construct( CM_WP_Base $owner ) {
            $this->owner  = $owner;
        }
    }
}