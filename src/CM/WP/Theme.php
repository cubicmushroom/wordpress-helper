<?php

class CM_WP_Theme extends CM_WP_Base {

	/***************************************
	 * Static factory properties & methods *
	 ***************************************/

	/**
	 * Stores all registered plugins
	 *
	 * @var array
	 */
	static protected $theme;

	/**
	 * Registers a new plugin object
	 *
	 * @param string $slug The slug used to identify this plugin
	 *
	 * @throws CM_WP_Exception_ThemeAlreadyRegisteredException
	 *
	 * @return CM_WP_Theme
	 */
	static public function register( $slug ) {

		// Check that this plugin has not already been registered
		if ( isset( self::$theme ) ) {
			throw new CM_WP_Exception_ThemeAlreadyRegisteredException( $slug );
		}

		// Create the plugin object
		self::$theme = new CM_WP_Theme( $slug );

		return self::$theme;
	}

	/**
	 * Loads a previously registered plugin
	 *
	 * @throws CM_WP_Exception_ThemeNotRegisteredException
	 *
	 * @return CM_WP_Theme
	 */
	static public function load() {

		// Check that the plugin requested is registered
		if ( ! isset( self::$theme ) ) {
			throw new CM_WP_Exception_ThemeNotRegisteredException();
		}
		return self::$theme;
	}









	/*******************************
	 * Object properties & methods *
	 *******************************/

	/**
	 * Sets the root file for the plugin for use within function calls
	 *
	 * This method is protected as should be called from the static create
	 * factory method
	 *
	 * @param string $slug The slug used to identify this plugin used to build
	 *                     prefix
	 */
	protected function __construct( $slug ) {

		// Call the CM_WP_Base constructor to set the plugin prefix (used to
		// namespace various items)
		parent::__construct( $slug );
	}
}