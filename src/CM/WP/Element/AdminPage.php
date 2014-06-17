<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 17/06/2014
 * Time: 11:18
 */

if ( ! class_exists( 'CM_WP_Element_AdminPage' ) ) {


	/**
	 * Class CM_WP_Element_AdminPage
	 */
	class CM_WP_Element_AdminPage {

		/**
		 * @var string Which admin section this should be added under
		 */
		protected $parent_slug;

		/**
		 * @var string Title foe the admin page
		 */
		protected $page_title;

		/**
		 * @var string Test for the settings menu
		 */
		protected $menu_title;

		/**
		 * @var string User capability required to access this page
		 */
		protected $capability;

		/**
		 * @var string Slug used in the page URL
		 */
		protected $menu_slug;

		/**
		 * @var callable Callback used to display the page's content
		 */
		protected $content_callback;


		/**
		 * Adds hook to add Settings page
		 *
		 * @return void
		 */
		public function __construct() {

			// Set the default content callback.  May be overridden using setContentCallback method
			$this->content_callback = array( $this, 'display_content' );

			// Queue action hook for adding
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}

		/**
		 * Adds LIM Tool settings page
		 */
		public function admin_menu() {
			add_submenu_page(
				$this->parent_slug,
				$this->page_title,
				$this->menu_title,
				$this->capability,
				$this->menu_slug,
				$this->$content_callback
			);
		}

		/**
		 * Renders the content of the admin page
		 */
		public function display_content() {
			echo '<br><br>This is the default display_content() method.<br>You probably want to replace this!';

		}


		/*****************************
		 *                           *
		 *   Getters, Setters, etc   *
		 *                           *
		 *****************************/

		/**
		 * @param string $capability
		 */
		public function setCapability( $capability ) {
			$this->capability = $capability;

			return $this;
		}

		/**
		 * @param callable $content_callback
		 *
		 * @throws CM_WP_Exception_InvalidCallbackException if $content_callback is not callable
		 */
		public function setContentCallback( $content_callback ) {
			if ( ! is_callable( $content_callback ) ) {
				throw new CM_WP_Exception_InvalidCallbackException( $content_callback );
			}

			$this->content_callback = $content_callback;

			return $this;
		}

		/**
		 * Renders the content of the admin page
		 */
		public function settings_page() {
			echo '<br><br>This is the page content';

		}

		/**
		 * @param string $menu_slug
		 */
		public function setMenuSlug( $menu_slug ) {
			$this->menu_slug = $menu_slug;

			return $this;
		}

		/**
		 * @param string $menu_title
		 */
		public function setMenuTitle( $menu_title ) {
			$this->menu_title = $menu_title;

			return $this;
		}

		/**
		 * @param string $page_title
		 */
		public function setPageTitle( $page_title ) {
			$this->page_title = $page_title;

			return $this;
		}

		/**
		 * @param string $parent_slug
		 */
		public function setParentSlug( $parent_slug ) {
			$this->parent_slug = $parent_slug;

			return $this;
		}
	}
}