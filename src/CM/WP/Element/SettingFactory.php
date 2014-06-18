<?php

/**
 * Created by PhpStorm.
 * User: toby
 * Date: 18/06/2014
 * Time: 10:18
 */
class CM_WP_Element_SettingFactory {

	/**
	 * Builds the relevant CM_WP_Element_Setting_*Setting object based on passed parameters
	 *
	 * @param $type
	 * @param $id
	 * @param $label
	 * @param $page
	 * @param $section
	 * @param string $helper_text
	 * @param array $attributes
	 *
	 * @return CM_WP_Element_Setting
	 */
	static public function build( $type, $id, $label, $page, $section, $helper_text = '', $attributes = array() ) {

		switch ( $type ) {
			case 'text':
				$setting = new CM_WP_Element_Setting_TextSetting( $id, $label, $page, $section, $helper_text,
					$attributes );
				break;
			case 'radio':
				$setting = new CM_WP_Element_Setting_RadioSetting( $id, $label, $page, $section, $helper_text,
					$attributes );
				break;
			default:
				$setting = new CM_WP_Element_Setting_CustomSetting( $id, $label, $page, $section, $helper_text,
					$attributes );
				$setting->setInputTemplate( $type );
		}

		return $setting;
	}
} 