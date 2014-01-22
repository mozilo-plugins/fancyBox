<?php if(!defined('IS_CMS')) die();

/**
 * Plugin:   fancyBox
 * @author:  HPdesigner (kontakt[at]devmount[dot]de)
 * @version: v0.0.2014-01-21
 * @license: GPL
 * @see:     "For I know the plans I have for you" declares the LORD, "plans to prosper you and not to harm you, plans to give you hope and a future."
 *           - The Bible
 *
 * Plugin created by DEVMOUNT
 * www.devmount.de
 *
**/

class fancyBox extends Plugin {

	public $admin_lang;
	private $cms_lang;
	var $gallery;

	function getContent($value) {

		global $CMS_CONF;
		global $syntax;

		// initialize mozilo gallery
		include_once($BASE_DIR . 'cms/' . 'GalleryClass.php');
		$this->gallery = new GalleryClass();
		$this->gallery->initial_Galleries();

		$this->cms_lang = new Language(PLUGIN_DIR_REL . 'fancyBox/lang/cms_language_' . $CMS_CONF->get('cmslanguage') . '.txt');

		// get language labels
		$label = $this->cms_lang->getLanguageValue('label');

		// get params
		$values = explode('|', $value);
		$param_gal = $values[0];
		$param_img = $values[1];

		// get conf
		$conf = array(
			'usemousewheel' => $this->settings->get('usemousewheel'),
		);

		// add jquery
		$syntax->insert_jquery_in_head('jquery');
		// add mousewheel plugin (optional)
		id($conf['usemousewheel'])
			$syntax->insert_in_head('<script type="text/javascript" src="' . URL_BASE . PLUGIN_DIR_NAME . '/fancyBox/lib/jquery.mousewheel.pack.js"></script>');
		// add fancyBox
		$syntax->insert_in_head('<link rel="stylesheet" href="' . URL_BASE . PLUGIN_DIR_NAME . '/fancyBox/source/jquery.fancybox.css" type="text/css" media="screen" />');
		$syntax->insert_in_head('<script type="text/javascript" src="' . URL_BASE . PLUGIN_DIR_NAME . '/fancyBox/source/jquery.fancybox.pack.js"></script>');

		// initialize return content and default class
		$content = '';
		$class = 'fancybox';

		// build image paths
		$path_img = $this->gallery->get_ImageSrc($param_gal, $param_img, false);
		$path_thumb = $this->gallery->get_ImageSrc($param_gal, $param_img, true);

		// build image tag
		$content .= $this->buildImgTag($class, $param_gal, $path_img, $path_thumb);

		// attach fancyBox
		$syntax->insert_in_head('<script type="text/javascript">
									$(document).ready(function() {
										$(".fancybox").fancybox(
											
										);
									});
								</script>');

		return $content;
	}


	function getConfig() {

		$config = array();

		// use mousewheel
		$config['usemousewheel']  = array(
			'type' => 'checkbox',
			'description' => $this->admin_lang->getLanguageValue('config_usemousewheel')
		);

		// // text
		// $config['text']  = array(
		// 	'type' => 'text',
		// 	'description' => $this->admin_lang->getLanguageValue('config_text'),
		// 	'maxlength' => '100',
		// 	'size' => '5',
		// 	'regex' => "/^[0-9]{1,2}$/",
		// 	'regex_error' => $this->admin_lang->getLanguageValue('config_text_error')
		// );

		// // textarea
		// $config['textarea']  = array(
		// 	'type' => 'textarea',
		// 	'description' => $this->admin_lang->getLanguageValue('config_textarea'),
		// 	'cols' => '10',
		// 	'rows' => '10',
		// 	'regex' => "/^[0-9]{1,2}$/",
		// 	'regex_error' => $this->admin_lang->getLanguageValue('config_textarea_error')
		// );

		// // password
		// $config['password']  = array(
		// 	'type' => 'password',
		// 	'description' => $this->admin_lang->getLanguageValue('config_password'),
		// 	'maxlength' => '100',
		// 	'size' => '5',
		// 	'regex' => "/^[0-9]{3,5}$/",
		// 	'regex_error' => $this->admin_lang->getLanguageValue('config_password_error'),
		// 	'saveasmd5' => true
		// );


		// // radio
		// $config['radio']  = array(
		// 	'type' => 'radio',
		// 	'description' => $this->admin_lang->getLanguageValue('config_radio'),
		// 	'descriptions' => array(
		// 		'blau' => 'Blau',
		// 		'rot' => 'Rot',
		// 		'gruen' => 'Grün'
		// 	)
		// );

		// // select
		// $config['select']  = array(
		// 	'type' => 'select',
		// 	'description' => $this->admin_lang->getLanguageValue('config_select'),
		// 	'descriptions' => array(
		// 		'blau' => 'Blau',
		// 		'rot' => 'Rot',
		// 		'gruen' => 'Grün'
		// 	),
		// 	'multiple' => false
		// );

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


	protected function buildImgTag($class, $rel, $href, $src) {
		$html .= '<a ';
		$html .= 	'class="' . $class . '" ';
		$html .= 	'rel="' . $rel . '" ';
		$html .= 	'href="' . $href . '" ';
		$html .= '>';
		$html .= 	'<img src="' . $src . '" alt="" />';
		$html .= '</a>';
		return $html;
	}
}

?>