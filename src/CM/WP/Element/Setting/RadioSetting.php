<?php

/**
 * Used to display a Radio Button style setting using the WordPress Settings API
 *
 * To use, you need to call setOptions() to set the possible options (see method docs)
 *
 * To set a default value for the radio buttons, call setDefaultValue() with the value (not the label) that should be
 * set as the default when no previous value is set.
 *
 * To change the HTML around the option inputs + labels, use set setBetweenOptions()
 */
class CM_WP_Element_Setting_RadioSetting extends CM_WP_Element_Setting {


	/**
	 * Array of possible options
	 *
	 * Should contain an array with keys being the value of the input and the value being an array containing any of the
	 * following...
	 * - label for the input
	 *
	 * @var array
	 */
	protected $options = [ ];


	/**
	 * Default value to select if no value is already set
	 * @var string
	 */
	protected $default_value;

	/**
	 * Template for input field
	 * @var string
	 */
	protected $input_template = '<label title="%7$s"><input name="%1$s" id="%2$s" type="radio" value="%6$s" %3$s> <span>%7$s</span></label> %4$s';

	/**
	 * @var string HTML to place before the options
	 */
	protected $before_options = '<p>';
	/**
	 * @var string HTML to place between each option
	 */
	protected $between_options = '</p><p>';
	/**
	 * @var string HTML to place after the options
	 */
	protected $after_options = '</p>';

	/**
	 * @return string
	 */
	public function getAfterOptions() {
		return $this->after_options;
	}

	/**
	 * @param string $after_options
	 *
	 * @return $this
	 */
	public function setAfterOptions( $after_options ) {
		$this->after_options = $after_options;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getBeforeOptions() {
		return $this->before_options;
	}

	/**
	 * @param string $before_options
	 *
	 * @return $this
	 */
	public function setBeforeOptions( $before_options ) {
		$this->before_options = $before_options;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getOptions() {
		return $this->options;
	}

	/**
	 * @param array $options
	 *
	 * @return $this
	 */
	public function setOptions( $options ) {
		$this->options = $options;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDefaultValue() {
		return $this->default_value;
	}

	/**
	 * @param string $default_value
	 *
	 * @return $this
	 */
	public function setDefaultValue( $default_value ) {
		$this->default_value = $default_value;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getBetweenOptions() {
		return $this->between_options;
	}

	/**
	 * @param string $option_separator
	 *
	 * @return $this
	 */
	public function setBetweenOptions( $option_separator ) {
		$this->between_options = $option_separator;

		return $this;
	}

	/**
	 * Displays the input field for the setting on a settings page
	 *
	 * @param string $template
	 * @param string $current_value
	 * @param array $attributes
	 *
	 * @return void
	 */
	protected function display_input( $template, $current_value, array $attributes ) {

		$options = $this->getOptions();
		if ( is_null( $current_value ) ) {
			$current_value = $this->getDefaultValue();
		}

		$inputs = [ ];
		foreach ( $options as $option_value => $option_label ) {
			$inputs[] = $this->get_input_option( $template, $current_value, $attributes, $option_value, $option_label );
		}

		echo $this->getBeforeOptions();
		echo implode( $this->getBetweenOptions(), $inputs );
		echo $this->getAfterOptions();
	}

	/**
	 * @param $template
	 * @param $current_value
	 * @param array $attributes
	 * @param $option_value
	 * @param $option_args
	 *
	 * @return string HTML for a single radio button
	 */
	protected function get_input_option( $template, $current_value, array $attributes, $option_value, $option_args ) {
		return sprintf(
			$template,
			$this->get_input_name(),
			$this->get_input_id(),
			( ( $current_value === $option_value ) ? 'checked="checked"' : '' ),
			$this->helper_text,
			implode( ' ', $attributes ),
			$option_value,
			$option_args['label']
		);
	}
}