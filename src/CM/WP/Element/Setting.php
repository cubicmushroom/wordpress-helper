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
class CM_WP_Element_Setting {

	/**
	 * Stores which settings have already been registered
	 * @var array
	 */
	static protected $registered_settigns = [ ];
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
	 * Type of input.  Should match on of the $input_templates entry keys
	 * @var string
	 */
	protected $type;
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
	protected $input_templates = [
		'text' => '<input name="%1$s[%2$s]" id="%1$s:%2$s" type="text" value="%3$s" %5$s /> %4$s',
	];

	/**
	 * @param string $id Setting ID
	 * @param string $label Label to use for the input field
	 * @param string $section Section to add this setting to.  Must already have been added
	 * @param string|callable $type Type of input.  Can be either one of the predefined formats provided in
	 *                              $settings_input_templates property
	 * @param string $helper_text (optional) Text to be displayed after the input field
	 * @param array $attributes (optional) Array of additional attributes to set on the HTML tag
	 */
	public function __construct( $id, $label, $page, $section, $type, $helper_text = '', $attributes = array() ) {

		$this->id          = $id;
		$this->label       = $label;
		$this->page        = $page;
		$this->section     = $section;
		$this->type        = $type;
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
	 * Can be overwridden by using the setInputCallback() method
	 */
	public function zz_input_callback() {
		if ( is_callable( $this->type ) ) {
			call_user_func( $this->type, $this );
		} else {
			if ( isset( $this->input_templates[ $this->type ] ) ) {
				$template = $this->input_templates[ $this->type ];
			} else {
				$template = $this->type;
			}

			// Attributes?
			$attributes = [ ];
			foreach ( (array) $this->attributes as $attribute => $attribute_value ) {
				$attributes[] = "{$attribute}=\"{$attribute_value}\"";
			}

			// Get values for field
			$section_options = get_option( $this->section );

			printf(
				$template,
				$this->section,
				$this->id,
				! empty( $section_options[ $this->id ] ) ? $section_options[ $this->id ] : '',
				$this->helper_text,
				implode( ' ', $attributes )
		);
		}
	}

	/**
	 * @param callable $input_callback
	 */
	public function setInputCallback( callable $input_callback ) {
		$this->input_callback = $input_callback;
	}

	/**
	 * Checks if a given input type is supported
	 *
	 * @param string $type Type to be checked
	 */
	protected function validate_input_type( $type ) {
		if ( empty( $this->input_templates[ $type ] ) ) {
			throw new \InvalidArgumentException(
				sprintf( 'Unrecognised $type \'%s\'.  Available types are \'%s\'.',
					$type,
					implode( '\', \'', $this->input_templates )
				)
			);
		}
	}
}