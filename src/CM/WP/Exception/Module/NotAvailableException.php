<?php

if (!class_exists('CM_WP_Exception_Module_NotAvailableException')) {

    class CM_WP_Exception_Module_NotAvailableException extends Exception {
        
        /**
         * Name of the module wanted
         * @var string
         */
        protected $module;

        /**
         * Stores module name & prepares exception message
         *
         * @param string $module Module wanted
         */
        public function __construct( $module ) {
            $this->module = $module;

            parent::__construct(
                sprintf(
                    "Module '%s' has not been registered",
                    $this->module
                )
            );
        }
    }
}