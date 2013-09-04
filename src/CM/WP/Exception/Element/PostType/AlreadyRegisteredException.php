<?php

if (!class_exists('CM_WP_Exception_Element_PostType_AlreadyRegisteredException')) {

    class CM_WP_Exception_Element_PostType_AlreadyRegisteredException extends
        Exception
    {
        /**
         * PostType slug
         * @var string
         */
        protected $slug;
        
        /**
         * Stores the slug & builds the exception message
         * 
         * @param string $slug Slug attempting to be registered
         */
        public function __construct( $slug )
        {
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