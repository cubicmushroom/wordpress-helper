<?php

if (
    ! class_exists(
        'CM_WP_Exception_InvalidCallbackException'
    )
) {

    class CM_WP_Exception_InvalidCallbackException extends
        Exception
    {

        /**
         * Passed callback
         * @var mixed
         */
        protected $callback;

        /**
         * Stores the invalid callback & builds the exception message
         * 
         * @param mixed $callback Invalid callback
         */
        public function __construct( $callback ) {

            $this->callback = $callback;

            parent::__construct(
                sprintf(
                    "Invalid callback '%s'",
                    $this->callback
                )
            );
        }
    }
}