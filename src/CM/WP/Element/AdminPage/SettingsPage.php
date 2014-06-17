<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 17/06/2014
 * Time: 11:10
 */

if ( ! class_exists( 'CM_WP_Element_AdminPage_SettingsPage' ) ) {

	/**
	 * Displays a Settings admin pages
	 *
	 * Extends the CM_WP_Element_AdminPage class, simply including the $parent_slug property to include page under the
	 * settings section
	 */
	class CM_WP_Element_AdminPage_SettingsPage extends CM_WP_Element_AdminPage {

		/**
		 * @var string Which admin section this should be added under
		 */
		protected $parent_slug = 'options-general.php';

	}
}
