<?php

if (
    ! class_exists(
        'CM_WP_Exception_CM_WP_Exception_PluginAlreadyRegisteredException'
    )
) {

    class CM_WP_Exception_CM_WP_Exception_PluginAlreadyRegisteredException extends
        Exception
    {
        /**
         * Plugin slug
         * 
         * @var string
         */
        protected $slug;

        /**
         * Stores slug & builds exception message form slug
         * 
         * @param string $slug Slug that a plugin is already registered for
         */
        public function __construct( $slug ) {
            $this->slug = $slug;

            parent::__construct(
                sprintf(
                    "Plugin with the slug '%s' has already been registered",
                    $this->slug
                );
            );
        }
    }
}