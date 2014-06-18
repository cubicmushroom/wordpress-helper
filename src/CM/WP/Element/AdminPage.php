<?php
/**
* Created by PhpStorm.
* User: toby
* Date: 17/06/2014
* Time: 11:18
*/


/**
 * Class CM_WP_Element_AdminPage
 */
class CM_WP_Element_AdminPage {

	/**
	 * @var string Which admin section this should be added under
	 */
	protected $parent_slug;

	/**
	 * @var string Test for the settings menu
	 */
	protected $menu_title;

	/**
	 * @var string Title foe the admin page
	 */
	protected $page_title;

	/**
	 * Heading for the page
	 * @var string
	 */
	protected $page_heading;

	/**
	 * @var string User capability required to access this page
	 */
	protected $capability;

	/**
	 * @var string Slug used in the page URL
	 */
	protected $page_slug;

	/**
	 * @var callable Callback used to display the page's content
	 */
	protected $content_callback;


	/**
	 * @var array Settings sections added using the add_settings_section() method
	 */
	protected $sections = [ ];

	protected $settings = [ ];


	/**
	 * Adds hook to add Settings page
	 *
	 * @return \CM_WP_Element_AdminPage
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
			$this->page_slug,
			$this->content_callback
		);
	}

	/**
	 * Adds a Settings API section to the page
	 *
	 * If you want to use a callback for the intro text, then
	 *
	 * @param string $section_id ID of the section
	 * @param string $title Title used when displaying the section
	 * @param string|callable $intro Either the intro text to be displayed, or a callable used to return the intro
	 */
	public function add_settings_section( $section_id, $title, $intro = '' ) {

		$this->sections[ $section_id ] = array(
			'title' => $title,
			'intro' => $intro,
		);

		// Removed & re-add the action hook to register these, so it's not called twice if more than 1 section is
		// added
		remove_action( 'admin_init', array( $this, 'zz_admin_init_register_sections' ) );
		add_action( 'admin_init', array( $this, 'zz_admin_init_register_sections' ) );
	}

	/**
	 * Adds a settings to be managed by the page
	 *
	 * @param string $id Setting ID
	 * @param string $label Label to use for the input field
	 * @param string $section Section to add this setting to.  Must already have been added
	 * @param string|callable $type Type of input.  Can be either one of the predefined formats provided in
	 *                              $settings_input_templates property
	 * @param string $helper_text (optional) Text to be displayed after the input field
	 * @param array $attributes (optional) Array of additional attributes to set on the HTML tag
	 *
	 * @return CM_WP_Element_Setting
	 */
	public function add_setting( $id, $label, $section, $type, $helper_text = '', $attributes = array() ) {

		// Check the section has already been added
		if ( empty( $this->sections[ $section ] ) ) {
			throw new \RuntimeException( 'Section does not exist.  Please add the section first, and then add ' .
			                             'the settings to the section.' );
		}

		$this->settings[ $id ] = CM_WP_Element_SettingFactory::build( $type, $id, $label, $this->page_slug,
			$section, $helper_text, $attributes );

		return $this->settings[ $id ];
	}

	/**
	 * Registered the settings sections, ready to be displayed on the admin pages
	 */
	public function zz_admin_init_register_sections() {

		foreach ( $this->sections as $id => $section ) {
			// If the 'intro' property is a callable, call this for the content, otherwise just use the
			// zz_section_content_callback() method to simply echo it's content
			if ( is_callable( $section['intro'] ) ) {
				$content_callback = $section['intro'];
			} else {
				$content_callback = array( $this, 'zz_section_intro_callback' );
			}

			add_settings_section(
				$id,
				$section['title'],
				$content_callback,
				$this->page_slug
			);
		}
	}

	/**
	 * Used to display the section intro text, if we've not been passed a callable
	 *
	 * @param array $section Array of section settings
	 *
	 * @return void
	 */
	public function zz_section_intro_callback( array $section ) {
		$section_id = $section['id'];
		$intro      = $this->sections[ $section_id ]['intro'];
		if ( ! empty( $intro ) ) {
			echo $intro;
		}
	}

	/**
	 * Used to display the section intro text, if we've not been passed a callable
	 *
	 * @return void
	 */
	public function zz_setting_input_callback() {
		echo '<pre>';
		print_r( func_get_args() );
		echo '</pre>';
	}


	/**
	 * Renders the content of the admin page
	 */
	public function display_content() {
		echo '<div class="wrap">';
		if ( ! empty( $this->page_heading ) ) {
			echo "<h2>{$this->page_heading}</h2>";
		}
		echo '<form method="post" action="options.php">';

		// Page hidden fields
		settings_fields( $this->page_slug );

		// Sections
		do_settings_sections( $this->page_slug );

		submit_button();
		echo '</form>';
		echo '</div>';
	}


	/*****************************
	 *                           *
	 *   Getters, Setters, etc   *
	 *                           *
	 *****************************/

	/**
	 * @param string $capability
	 *
	 * @return $this
	 */
	public function setCapability( $capability ) {
		$this->capability = $capability;

		return $this;
	}

	/**
	 * @param callable $content_callback
	 *
	 * @throws CM_WP_Exception_InvalidCallbackException if $content_callback is not callable
	 *
	 * @return $this
	 */
	public function setContentCallback( $content_callback ) {
		if ( ! is_callable( $content_callback ) ) {
			throw new CM_WP_Exception_InvalidCallbackException( $content_callback );
		}

		$this->content_callback = $content_callback;

		return $this;
	}

	/**
	 * @param string $page_slug
	 *
	 * @return $this
	 */
	public function setPageSlug( $page_slug ) {
		$this->page_slug = $page_slug;

		return $this;
	}

	/**
	 * @param string $menu_title
	 *
	 * @return $this
	 */
	public function setMenuTitle( $menu_title ) {
		$this->menu_title = $menu_title;

		return $this;
	}

	/**
	 * @param string $page_title
	 *
	 * @return $this
	 */
	public function setPageTitle( $page_title ) {
		$this->page_title = $page_title;

		return $this;
	}

	/**
	 * @param string $page_heading
	 *
	 * @return $this
	 */
	public function setPageHeading( $page_heading ) {
		$this->page_heading = $page_heading;

		return $this;
	}

	/**
	 * @param string $parent_slug
	 *
	 * @return $this
	 */
	public function setParentSlug( $parent_slug ) {
		$this->parent_slug = $parent_slug;

		return $this;
	}
}