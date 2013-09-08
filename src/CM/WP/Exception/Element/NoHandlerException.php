<?php

if (!class_exists('CM_WP_Exception_Element_NoHandlerException')) {

    class CM_WP_Exception_Element_NoHandlerException extends Exception {
        
        public function __construct() {
            parent::__construct(
                'No handler has been set on this object.  You need to register a ' .
                'handler using the is_handled_by() method before this shortcode ' .
                'can be used'
            );
        }
    }
}