<?php

if (
    ! class_exists(
        'CM_WP_Exception_CM_WP_Exception_PluginNotRegisteredException'
    )
) {

    class CM_WP_Exception_CM_WP_Exception_PluginNotRegisteredException extends
        Exception
    {
        /**
         * Plugin slug
         * 
         * @var string
         */
        protected $slug;

        /**
         * [__construct description]
         * @param [type] $slug [description]
         */
        public function __construct( $slug ) {
            $this->slug = $slug;

            parent::__construct(
                sprintf(
                    "Plugin with the slug '%s' has not been registered yet",
                    $this->slug
                );
            );
        }
    }
}