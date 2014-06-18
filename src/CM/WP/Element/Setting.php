<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 17/06/2014
 * Time: 13:59
 */

/**
 * Used to store Settings for an Admin page
 */

/** @noinspection SpellCheckingInspection */
abstract class CM_WP_Element_Setting {

	/**
	 * Stores which settings have already been registered
	 * @var array
	 */
	static protected $registered_settings = [ ];
	/**
	 * ID of the setting
	 * @var string
	 */
	protected $id;
	/**
	 * Input field label
	 * @var string
	 */
	protected $label;
	/**
	 * Slug of the page this belongs to
	 * @var string
	 */
	protected $page;
	/**
	 * ID of the section this belongs to
	 * @var string
	 */
	protected $section;
	/**
	 * @var string HTML to display after the input field
	 */
	protected $helper_text;

	/**
	 * Optional additional HTML tag attributes
	 * @var array
	 */
	protected $attributes;

	/**
	 * Callback to use to render the input field
	 * @var callable
	 */
	protected $input_callback;
	/**
	 * @var array
	 */
	protected $input_template;

	/**
	 * @param string $id Setting ID
	 * @param string $label Label to use for the input field
	 * @param string $page ID of the page that this belongs to
	 * @param string $section Section to add this setting to.  Must already have been added
	 * @param string $helper_text (optional) Text to be displayed after the input field
	 * @param array $attributes (optional) Array of additional attributes to set on the HTML tag
	 */
	public function __construct( $id, $label, $page, $section, $helper_text = '', $attributes = array() ) {

		$this->id          = $id;
		$this->label       = $label;
		$this->page        = $page;
		$this->section     = $section;
		$this->helper_text = $helper_text;
		$this->attributes  = $attributes;

		// Default $input_callback
		$this->input_callback = array( $this, 'zz_input_callback' );

		// Register the settings in the admin_init hook
		add_action( 'admin_init', array( $this, 'zz_admin_init_register_settings' ) );
	}

	/**
	 * Registered the settings, ready to be displayed on the admin pages
	 */
	public function zz_admin_init_register_settings() {
		global $new_whitelist_options;

		// Add the field with the names and function to use for our new
		// settings, put it in our new section
		add_settings_field(
			$this->id,
			$this->label,
			array( $this, 'zz_input_callback' ),
			$this->page,
			$this->section
		);

		// Register our setting so that $_POST handling is done for us and
		// our callback function just has to echo the <input>
		if ( false === array_search( $this->section, (array) $new_whitelist_options[ $this->page ] ) ) {
			register_setting( $this->page, $this->section );
		}
	}

	/**
	 * Default callback to display the input field
	 *
	 * Can be overridden by using the setInputCallback() method
	 */
	public function zz_input_callback() {
		$template = $this->getInputTemplate();
		if ( is_callable( $template ) ) {
			call_user_func( $template, $this );

		} else {

			// Attributes?
			$attributes = $this->prepare_attributes();

			// Get values for field
			$section_options = get_option( $this->section );
			$value           = ! empty( $section_options[ $this->id ] ) ? $section_options[ $this->id ] : null;

			$this->display_input( $template, $value, $attributes );
		}
	}

	/**
	 * @param callable $input_callback
	 */
	public function setInputCallback( callable $input_callback ) {
		$this->input_callback = $input_callback;
	}

	/**
	 * Returns the sprintf() style template for use when rendering the input field for this setting
	 *
	 * sprintf() placeholders...
	 * - %1$s Settings section ID
	 * - %2$s Settings ID
	 * - %3$s Value
	 * - %4$s Helper text
	 * - %5$s Attributes
	 *
	 * @return string
	 */
	protected function getInputTemplate() {
		return $this->input_template;
	}

	/**
	 * Prepares any additional input field attributes
	 *
	 * @return array
	 */
	protected function prepare_attributes() {
		$attributes = [ ];
		foreach ( (array) $this->attributes as $attribute => $attribute_value ) {
			$attributes[] = "{$attribute}=\"{$attribute_value}\"";
		}

		return $attributes;
	}

	/**
	 * Displays the input field for the setting on a settings page
	 *
	 * @param string $template
	 * @param string $value
	 * @param array $attributes
	 *
	 * @return void
	 */
	protected function display_input( $template, $value, array $attributes ) {
		printf(
			$template,
			$this->section,
			$this->id,
			! empty( $value ) ? $value : '',
			$this->helper_text,
			implode( ' ', $attributes )
		);
	}
}