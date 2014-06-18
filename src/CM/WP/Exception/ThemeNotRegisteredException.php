<?php

class CM_WP_Exception_ThemeNotRegisteredException extends
    Exception
{

    /**
     * [__construct description]
     */
    public function __construct() {
        parent::__construct('A theme has not been registered yet');
    }
}