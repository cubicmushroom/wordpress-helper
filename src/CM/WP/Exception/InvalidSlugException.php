<?php

if (
    ! class_exists(
        'CM_WP_Exception_CM_WP_Exception_InvalidSlugException'
    )
) {

    class CM_WP_Exception_CM_WP_Exception_InvalidSlugException extends
        Exception
    {
        /**
         * Plugin slug
         * @var string
         */
        protected $slug;

        /**
         * String of valid characters
         * @var string
         */
        protected $valid_chars;

        /**
         * Stores the slug & builds the exception message
         * 
         * @param string $slug        Slug that contains invalid characters
         * @param string $valid_chars String of valid characters
         */
        public function __construct( $slug, $valid_chars )
        {
            $this->slug = $slug;
            $this->valid_chars = $valid_chars;

            parent::__construct(
                sprintf(
                    "Slug '%s' contains invalid characters.  Allowed characters " .
                    "are '%s'",
                    $this->slug,
                    $this->valid_chars
                );
            );
        }
    }
}