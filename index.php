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
			'backgroundcolor' => $this->settings->get('backgroundred') . ', ' . $this->settings->get('backgroundgreen') . ', ' . $this->settings->get('backgroundblue') . ', ' . $this->settings->get('backgroundalpha'),
			'padding' => $this->settings->get('padding'),
			'margin' => $this->settings->get('margin'),
			'width' => $this->settings->get('width'),
			'height' => $this->settings->get('height'),
			'minwidth' => $this->settings->get('minwidth'),
			'minheight' => $this->settings->get('minheight'),
			'maxwidth' => $this->settings->get('maxwidth'),
			'maxheight' => $this->settings->get('maxheight'),
			'autosize' => $this->settings->get('autosize'),
			'autoresize' => $this->settings->get('autoresize'),
			'autocenter' => $this->settings->get('autocenter'),
			'fittoview' => $this->settings->get('fittoview'),
			'scrolling' => $this->settings->get('scrolling'),
		);
		// validate conf
		if ($this->settings->get('backgroundred') == '' and $this->settings->get('backgroundgreen') == '' and $this->settings->get('backgroundblue') == '' and $this->settings->get('backgroundalpha') == '') $conf['backgroundcolor'] = '';

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
			// build single image tag
			$content .= $this->buildImgTag($class, $param_cat, $path_img, $path_img);
		}

		// attach fancyBox
		$fancyjs = '<script type="text/javascript">
						$(document).ready(function() {
							$(".fancybox").fancybox({';
		// set background-color
		if($conf['backgroundcolor'] != '') $fancyjs .= 'helpers : { overlay : { css : { "background" : "rgba(' . $conf['backgroundcolor'] . ')" } } },';
		// set padding
		if($conf['padding'] != '') $fancyjs .= 'padding : ' . $conf['padding'] . ',';
		// set margin
		if($conf['margin'] != '') $fancyjs .= 'margin : ' . $conf['margin'] . ',';
		// set width
		if($conf['width'] != '') $fancyjs .= 'width : ' . $conf['width'] . ',';
		// set height
		if($conf['height'] != '') $fancyjs .= 'height : ' . $conf['height'] . ',';
		// set minwidth
		if($conf['minwidth'] != '') $fancyjs .= 'minWidth : ' . $conf['minwidth'] . ',';
		// set minheight
		if($conf['minheight'] != '') $fancyjs .= 'minHeight : ' . $conf['minheight'] . ',';
		// set maxwidth
		if($conf['maxwidth'] != '') $fancyjs .= 'maxWidth : ' . $conf['maxwidth'] . ',';
		// set maxheight
		if($conf['maxheight'] != '') $fancyjs .= 'maxHeight : ' . $conf['maxheight'] . ',';

		// set autosize
		if($conf['autosize'] != '') $fancyjs .= 'autoSize : ' . $conf['autosize'] . ',';
		// set autoresize
		if($conf['autoresize'] != '') $fancyjs .= 'autoReSize : ' . $conf['autoresize'] . ',';
		// set autocenter
		if($conf['autocenter'] != '') $fancyjs .= 'autoCenter : ' . $conf['autocenter'] . ',';
		// set fittoview
		if($conf['fittoview'] != '') $fancyjs .= 'fitToView : ' . $conf['fittoview'] . ',';

		// select scrolling
		if($conf['scrolling'] != '') $fancyjs .= 'scrolling : "' . $conf['scrolling'] . '",';
		
		$fancyjs .=			'});
						});
					</script>';
		$syntax->insert_in_head($fancyjs);

		return $content;
	}


	function getConfig() {

		$config = array();

		// use mousewheel
		$config['usemousewheel'] = $this->confCheck($this->admin_lang->getLanguageValue('config_usemousewheel'));

		// background color red
		$config['backgroundred'] = $this->confText('', '100', '3', "/^[0-9]{1,3}$/", $this->admin_lang->getLanguageValue('config_backgroundred_error'));
		// background color green
		$config['backgroundgreen'] = $this->confText('', '100', '3', "/^[0-9]{1,3}$/", $this->admin_lang->getLanguageValue('config_backgroundgreen_error'));
		// background color blue
		$config['backgroundblue'] = $this->confText('', '100', '3', "/^[0-9]{1,3}$/", $this->admin_lang->getLanguageValue('config_backgroundblue_error'));
		// background color alpha
		$config['backgroundalpha'] = $this->confText($this->admin_lang->getLanguageValue('config_rgba'), '100', '3'); // TODO regex for floating point

		// padding
		$config['padding'] = $this->confText($this->admin_lang->getLanguageValue('config_padding'), '100', '3', "/^[0-9]{1,3}$/", $this->admin_lang->getLanguageValue('config_padding_error'));
		// margin
		$config['margin'] = $this->confText($this->admin_lang->getLanguageValue('config_margin'), '100', '3', "/^[0-9]{1,3}$/", $this->admin_lang->getLanguageValue('config_margin_error'));

		// width
		$config['width'] = $this->confText($this->admin_lang->getLanguageValue('config_width'), '100', '3', "/^[0-9]{1,4}$/", $this->admin_lang->getLanguageValue('config_width_error'));
		// height
		$config['height'] = $this->confText($this->admin_lang->getLanguageValue('config_height'), '100', '3', "/^[0-9]{1,4}$/", $this->admin_lang->getLanguageValue('config_height_error'));
		// minwidth
		$config['minwidth'] = $this->confText($this->admin_lang->getLanguageValue('config_minwidth'), '100', '3', "/^[0-9]{1,4}$/", $this->admin_lang->getLanguageValue('config_minwidth_error'));
		// minheight
		$config['minheight'] = $this->confText($this->admin_lang->getLanguageValue('config_minheight'), '100', '3', "/^[0-9]{1,4}$/", $this->admin_lang->getLanguageValue('config_minheight_error'));
		// maxwidth
		$config['maxwidth'] = $this->confText($this->admin_lang->getLanguageValue('config_maxwidth'), '100', '3', "/^[0-9]{1,4}$/", $this->admin_lang->getLanguageValue('config_maxwidth_error'));
		// maxheight
		$config['maxheight'] = $this->confText($this->admin_lang->getLanguageValue('config_maxheight'), '100', '3', "/^[0-9]{1,4}$/", $this->admin_lang->getLanguageValue('config_maxheight_error'));

		// set autosize
		$config['autosize'] = $this->confCheck($this->admin_lang->getLanguageValue('config_autosize'));
		// set autoresize
		$config['autoresize'] = $this->confCheck($this->admin_lang->getLanguageValue('config_autoresize'));
		// set autocenter
		$config['autocenter'] = $this->confCheck($this->admin_lang->getLanguageValue('config_autocenter'));
		// set fittoview
		$config['fittoview'] = $this->confCheck($this->admin_lang->getLanguageValue('config_fittoview'));

		// select scrolling
		$descriptions = array(
			'auto' => $this->admin_lang->getLanguageValue('config_scrolling_auto'),
			'yes' => $this->admin_lang->getLanguageValue('config_scrolling_yes'),
			'no' => $this->admin_lang->getLanguageValue('config_scrolling_no'),
			'visible' => $this->admin_lang->getLanguageValue('config_scrolling_visible')
		);
		$config['scrolling'] = $this->confSelect($this->admin_lang->getLanguageValue('config_scrolling'), $descriptions, false);

		// Template
		$config['--template~~'] = '
				<div>{usemousewheel_checkbox} {usemousewheel_description}</div>
			</li>
			<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix">
				<div><div style="margin-bottom:5px;">{backgroundalpha_description}</div>R {backgroundred_text} &nbsp; G {backgroundgreen_text} &nbsp; B {backgroundblue_text} &nbsp; &alpha; {backgroundalpha_text}</div>
			</li>
			<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix">
				<div><div style="margin-bottom:5px;">{padding_text} {padding_description}</div>{margin_text} {margin_description}</div>
			</li>
			<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix">
				<div style="width:32%;display:inline-block;vertical-align:top;"><div style="margin-bottom:5px;">{width_text} {width_description}</div>{height_text} {height_description}</div>
				<div style="width:32%;display:inline-block;vertical-align:top;"><div style="margin-bottom:5px;">{minwidth_text} {minwidth_description}</div>{minheight_text} {minheight_description}</div>
				<div style="width:32%;display:inline-block;vertical-align:top;"><div style="margin-bottom:5px;">{maxwidth_text} {maxwidth_description}</div>{maxheight_text} {maxheight_description}</div>
			</li>
			<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix">
				<div>
					<div style="margin-bottom:5px;">{autosize_checkbox} {autosize_description}</div>
					<div style="margin-bottom:5px;">{autoresize_checkbox} {autoresize_description}</div>
					<div style="margin-bottom:5px;">{autocenter_checkbox} {autocenter_description}</div>
					<div style="margin-bottom:5px;">{fittoview_checkbox} {fittoview_description}</div>
				</div>
			</li>
			<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix">
				<div><div style="width:32%;display:inline-block;margin-right:5px;">{scrolling_select}</div> {scrolling_description}
		';

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

	protected function confCheck($description) {
		// required properties
		return array(
			'type' => 'checkbox',
			'description' => $description,
		);
	}

	protected function confText($description, $maxlength='', $size='', $regex='', $regex_error='') {
		// required properties
		$conftext = array(
			'type' => 'text',
			'description' => $description,
		);
		// optional properties
		if ($maxlength != '') $conftext['maxlength'] = $maxlength;
		if ($size != '') $conftext['size'] = $size;
		if ($regex != '') $conftext['regex'] = $regex;
		if ($regex_error != '') $conftext['regex_error'] = $regex_error;
		return $conftext;
	}

	protected function confSelect($description, $descriptions, $multiple=false) {
		// required properties
		return array(
			'type' => 'select',
			'description' => $description,
			'descriptions' => $descriptions,
			'multiple' => $multiple,
		);
	}

}

?>