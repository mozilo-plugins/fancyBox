<?php if(!defined('IS_CMS')) die();

/**
 * Plugin:   fancyBox
 * @author:  HPdesigner (kontakt[at]devmount[dot]de)
 * @version: v0.0.2014-01-21
 * @license: GPL
 * @see:     Verse
 *           - The Bible
 *
 * Plugin created by DEVMOUNT
 * www.devmount.de
 *
**/

class fancyBox extends Plugin {

	public $admin_lang;
	private $cms_lang;

	function getContent($value) {

		global $CMS_CONF;
		global $syntax;

		$this->cms_lang = new Language(PLUGIN_DIR_REL . 'fancyBox/lang/cms_language_' . $CMS_CONF->get('cmslanguage') . '.txt');

		// get language labels
		$label = $this->cms_lang->getLanguageValue('label');

		// get params
		$values = explode('|', $value);

		// get conf
		$conf = array(
			'text' => $this->settings->get('text'),
			'textarea' => $this->settings->get('textarea'),
			'password' => $this->settings->get('password'),
			'check' => $this->settings->get('check'),
			'radio' => $this->settings->get('radio'),
			'select' => $this->settings->get('select')
		);

		// include jquery and fancyBox javascript
		$syntax->insert_jquery_in_head('jquery');
		$syntax->insert_in_head('<script type="text/javascript" src="' . URL_BASE . PLUGIN_DIR_NAME . '/fancyBox/js/fancyBox.js"></script>');

		// initialize return content
		$content = '';

		// do something awesome here!

		return $content;
	}


	function getConfig() {

		$config = array();

		// text
		$config['text']  = array(
			'type' => 'text',
			'description' => $this->admin_lang->getLanguageValue('config_text'),
			'maxlength' => '100',
			'size' => '5',
			'regex' => "/^[0-9]{1,2}$/",
			'regex_error' => $this->admin_lang->getLanguageValue('config_text_error')
		);

		// textarea
		$config['textarea']  = array(
			'type' => 'textarea',
			'description' => $this->admin_lang->getLanguageValue('config_textarea'),
			'cols' => '10',
			'rows' => '10',
			'regex' => "/^[0-9]{1,2}$/",
			'regex_error' => $this->admin_lang->getLanguageValue('config_textarea_error')
		);

		// password
		$config['password']  = array(
			'type' => 'password',
			'description' => $this->admin_lang->getLanguageValue('config_password'),
			'maxlength' => '100',
			'size' => '5',
			'regex' => "/^[0-9]{3,5}$/",
			'regex_error' => $this->admin_lang->getLanguageValue('config_password_error'),
			'saveasmd5' => true
		);

		// checkbox
		$config['checkbox']  = array(
			'type' => 'checkbox',
			'description' => $this->admin_lang->getLanguageValue('config_checkbox')
		);

		// radio
		$config['radio']  = array(
			'type' => 'radio',
			'description' => $this->admin_lang->getLanguageValue('config_radio'),
			'descriptions' => array(
				'blau' => 'Blau',
				'rot' => 'Rot',
				'gruen' => 'Grün'
			)
		);

		// select
		$config['select']  = array(
			'type' => 'select',
			'description' => $this->admin_lang->getLanguageValue('config_select'),
			'descriptions' => array(
				'blau' => 'Blau',
				'rot' => 'Rot',
				'gruen' => 'Grün'
			),
			'multiple' => false
		);

		return $config;
	}  


	function getInfo() {

		global $ADMIN_CONF;

		$this->admin_lang = new Language(PLUGIN_DIR_REL . 'fancyBox/lang/admin_language_' . $ADMIN_CONF->get('language') . '.txt');

		$info = array(
			// plugin name and version
			'<b>fancyBox</b> v0.0.2014-01-21',
			// moziloCMS version
			'2.0',
			// short description, only <span> and <br /> are allowed
			$this->admin_lang->getLanguageValue('description'), 
			// author
			'HPdesigner',
			// documentation url
			'http://www.devmount.de/Develop/Mozilo%20Plugins/fancyBox.html',
			// plugin tag for select box when editing a page, can be emtpy
			array(
				'{fancyBox|parameter}' => $this->admin_lang->getLanguageValue('placeholder'),
			)
		);

		return $info;
	}
}

?>