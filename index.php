<?php if(!defined('IS_CMS')) die();

/**
 * Plugin:   fancyBox
 * @author:  HPdesigner (kontakt[at]devmount[dot]de)
 * @version: v0.0.2014-01-xx
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
		global $CatPage;

		// initialize mozilo gallery
		include_once($BASE_DIR . 'cms/' . 'GalleryClass.php');
		$this->gallery = new GalleryClass();
		$this->gallery->initial_Galleries();

		$this->cms_lang = new Language(PLUGIN_DIR_REL . 'fancyBox/lang/cms_language_' . $CMS_CONF->get('cmslanguage') . '.txt');

		// get language labels
		$label = $this->cms_lang->getLanguageValue('label');

		// get params
		$values = explode('|', $value);
		$param_gal = trim($values[0]);
		$param_img = trim($values[1]);

		// get conf
		$conf = array(
			'usemousewheel' => $this->settings->get('usemousewheel'),
		);

		// add jquery
		$syntax->insert_jquery_in_head('jquery');
		// add mousewheel plugin (optional)
		if($conf['usemousewheel'])
			$syntax->insert_in_head('<script type="text/javascript" src="' . URL_BASE . PLUGIN_DIR_NAME . '/fancyBox/lib/jquery.mousewheel.pack.js"></script>');
		// add fancyBox
		$syntax->insert_in_head('<link rel="stylesheet" href="' . URL_BASE . PLUGIN_DIR_NAME . '/fancyBox/source/jquery.fancybox.css" type="text/css" media="screen" />');
		$syntax->insert_in_head('<script type="text/javascript" src="' . URL_BASE . PLUGIN_DIR_NAME . '/fancyBox/source/jquery.fancybox.pack.js"></script>');

		// initialize return content and default class
		$content = '';
		$class = 'fancybox';

		// gallery with no image specified: load whole gallery
		if ($param_gal != '' and $param_img == '') {
			$images = $this->gallery->get_GalleryImagesArray($param_gal);
			// build image tag for every image
			foreach ($images as $image) {
				// build image paths
				$path_img = $this->gallery->get_ImageSrc($param_gal, $image, false);
				$path_thumb = $this->gallery->get_ImageSrc($param_gal, $image, true);
				$content .= $this->buildImgTag($class, $param_gal, $path_img, $path_thumb);
			}
		}

		// gallery with image specified: load single image from gallery
		if ($param_gal != '' and $param_img != '') {
			// build image paths
			$path_img = $this->gallery->get_ImageSrc($param_gal, $param_img, false);
			$path_thumb = $this->gallery->get_ImageSrc($param_gal, $param_img, true);
			// build single image tag
			$content .= $this->buildImgTag($class, $param_gal, $path_img, $path_thumb);
		}

		// no gallery but image specified: load single image from files
		if ($param_gal == '' and $param_img != '') {
			$param_img = explode('%3A', $param_img);
			$param_cat = urlencode($param_img[0]);
			$param_file = $param_img[1];
			// build image path
			$path_img =  URL_BASE .'kategorien/' . $param_cat . '/dateien/' . $param_file;
			print_r($path_img);
			// build single image tag
			$content .= $this->buildImgTag($class, $param_cat, $path_img, $path_img);
		}

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
			'<b>fancyBox</b> v0.0.2014-01-xx',
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