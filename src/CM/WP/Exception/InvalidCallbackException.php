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

            if ( is_array( $this->callback ) ) {
                $callback_str = var_export( $this->callback, true );
            } else {
                $callback_str = $this->callback;
            }

            $msg = sprintf(
                "Invalid callback '%s'",
                $callback_str
            );

            parent::__construct( $msg );
        }
    }
}