<?php

if (!class_exists('CM_WP_Base')) {

    class CM_WP_Base {

        /**
         * Prefix used to namespace various items created
         * @var string
         */
        protected $prefix;

        /**
         * Prepares the plugin prefix
         *
         * @throws CM_WP_Exception_InvalidSlugException If $slug contains invalid characters
         * 
         * @param string $slug The slug that the plugin or theme is identified by
         */
        public function __construct( $slug ) {

            // Check slug for invalid characters
            $valid_chars = 'a-z 0-9 _';
            $valid_regex = '/^[' . str_replace(' ', '', $valid_chars) . ']+$/';
            if ( ! preg_match( $valid_regex, $slug ) ) {
                throw new CM_WP_Exception_InvalidSlugException( $slug, $valid_chars );
            }

            $this->prefix = $slug;
        }
    }
}