<?php

/**
 * Settings class for use when standard templates are not enough
 *
 * To use, pass the custom template or callback to CM_WP_Element_SettingFactory::build() as the first argument, or
 * instantiate an object of this class directly, then call it's setInputTemplate() method with the required template
 * string or callback
 */
class CM_WP_Element_Setting_CustomSetting extends CM_WP_Element_Setting {
	/**
	 * @param array $input_template
	 */
	public function setInputTemplate( $input_template ) {
		$this->input_template = $input_template;
	}

	/**
	 * @return string
	 */
	protected function getInputTemplate() {
		if ( ! isset( $this->input_template ) ) {
			throw new \RuntimeException( '$input_template not yet set.  You need to set this using the ' .
			                             'setInputTemplate() method before the input is rendered.' );
		}
	}
}