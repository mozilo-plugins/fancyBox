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
		// $label = $this->cms_lang->getLanguageValue('label');

		// get params
		$values = explode('|', $value);
		$param_gal = trim($values[0]);
		$param_img = trim($values[1]);

		// set configuration elements and their default values
		// element => default, toquote, wrap
		// wrap: # -> key, | -> value
		$confelements = array(
			'backgroundred'		=> array('0',false,''),
			'backgroundgreen'	=> array('0',false,''),
			'backgroundblue'	=> array('0',false,''),
			'backgroundalpha'	=> array('0.5',false,''),
			'padding'			=> array('15',false,'# : |,'),
			'margin'			=> array('20',false,'# : |,'),
			'width'				=> array('800',false,'# : |,'),
			'height'			=> array('600',false,'# : |,'),
			'minwidth'			=> array('100',false,'# : |,'),
			'minheight'			=> array('100',false,'# : |,'),
			'maxwidth'			=> array('9999',false,'# : |,'),
			'maxheight'			=> array('9999',false,'# : |,'),
			'autosize'			=> array('true',false,'# : |,'),
			'autoresize'		=> array('!isTouch',false,'# : |,'),
			'autocenter'		=> array('!isTouch',false,'# : |,'),
			'fittoview'			=> array('true',false,'# : |,'),
			'scrolling'			=> array('auto',true,'# : |,'),
			'wrapcss'			=> array('',true,'# : |,'),
			'arrows'			=> array('true',false,'# : |,'),
			'closebtn'			=> array('true',false,'# : |,'),
			'closeclick'		=> array('false',false,'# : |,'),
			'nextclick'			=> array('false',false,'# : |,'),
			'mousewheel'		=> array('true',false,'# : |,'),
			'autoplay'			=> array('false',false,'# : |,'),
			'playspeed'			=> array('3000',false,'# : |,'),
			'preload'			=> array('3',false,'# : |,'),
			'loop'				=> array('true',false,'# : |,'),
			'openeffect'		=> array('fade',true,'# : |,'),
			'closeeffect'		=> array('fade',true,'# : |,'),
			'nexteffect'		=> array('elastic',true,'# : |,'),
			'preveffect'		=> array('elastic',true,'# : |,'),
			'openspeed'			=> array('250',false,'# : |,'),
			'closespeed'		=> array('250',false,'# : |,'),
			'nextspeed'			=> array('250',false,'# : |,'),
			'prevspeed'			=> array('250',false,'# : |,'),
		);

		// get conf and set default
		$conf = array();
		foreach ($confelements as $elem => $default) {
			$conf[$elem] = array(($this->settings->get($elem) == '') ? $default[0] : $this->settings->get($elem),$default[1],$default[2]);
		}

		// validate conf
		$conf['backgroundcolor'] = array(
			$this->settings->get('backgroundred') . ', ' . 
			$this->settings->get('backgroundgreen') . ', ' . 
			$this->settings->get('backgroundblue') . ', ' . 
			$this->settings->get('backgroundalpha'),
			false,
			'helpers : { overlay : { css : { "background" : "rgba(|)" } } },',
		);

		// delete partial conf elements, contents are now in 'backgroundcolor'
		unset($conf['backgroundred'], $conf['backgroundgreen'], $conf['backgroundblue'], $conf['backgroundalpha']);

		// add jquery
		$syntax->insert_jquery_in_head('jquery');
		// add mousewheel plugin (optional)
		if($conf['mousewheel'][0] == 'true')
			$syntax->insert_in_head('<script type="text/javascript" src="' . URL_BASE . PLUGIN_DIR_NAME . '/fancyBox/lib/jquery.mousewheel.pack.js"></script>');
		// add fancyBox
		$syntax->insert_in_head('<link rel="stylesheet" href="' . URL_BASE . PLUGIN_DIR_NAME . '/fancyBox/source/jquery.fancybox.css" type="text/css" media="screen" />');
		$syntax->insert_in_head('<script type="text/javascript" src="' . URL_BASE . PLUGIN_DIR_NAME . '/fancyBox/source/jquery.fancybox.pack.js"></script>');

		// initialize return content and default class
		$content = '<!-- BEGIN fancyBox plugin content --> ';
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
		$fancyjs = '<!-- initialize fancyBox plugin: --> ';
		$fancyjs .= '<script type="text/javascript"> $(document).ready(function() { $(".fancybox").fancybox({';

		foreach ($conf as $key => $value)
			$fancyjs .= $this->wrap($key, $value[0], $value[1], $value[2]);

		$fancyjs .=	'});});</script>';
		$syntax->insert_in_head($fancyjs);

		$content .= '<!-- END fancyBox plugin content --> ';
		return $content;
	}


	function getConfig() {

		$config = array();

		// background color red
		$config['backgroundred'] = $this->confText($this->admin_lang->getLanguageValue('config_backgroundred'), '100', '3', "/^[0-9]{1,3}$/", $this->admin_lang->getLanguageValue('config_backgroundred_error'));
		// background color green
		$config['backgroundgreen'] = $this->confText($this->admin_lang->getLanguageValue('config_backgroundgreen'), '100', '3', "/^[0-9]{1,3}$/", $this->admin_lang->getLanguageValue('config_backgroundgreen_error'));
		// background color blue
		$config['backgroundblue'] = $this->confText($this->admin_lang->getLanguageValue('config_backgroundblue'), '100', '3', "/^[0-9]{1,3}$/", $this->admin_lang->getLanguageValue('config_backgroundblue_error'));
		// background color alpha
		$config['backgroundalpha'] = $this->confText($this->admin_lang->getLanguageValue('config_backgroundalpha'), '100', '3'); // TODO regex for floating point

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

		// set wrapcss
		$config['wrapcss'] = $this->confText($this->admin_lang->getLanguageValue('config_wrapcss'));

		// set arrows
		$config['arrows'] = $this->confCheck($this->admin_lang->getLanguageValue('config_arrows'));
		// set closebtn
		$config['closebtn'] = $this->confCheck($this->admin_lang->getLanguageValue('config_closebtn'));
		// set closeclick
		$config['closeclick'] = $this->confCheck($this->admin_lang->getLanguageValue('config_closeclick'));
		// set nextclick
		$config['nextclick'] = $this->confCheck($this->admin_lang->getLanguageValue('config_nextclick'));
		// use mousewheel
		$config['mousewheel'] = $this->confCheck($this->admin_lang->getLanguageValue('config_mousewheel'));

		// set autoplay
		$config['autoplay'] = $this->confCheck($this->admin_lang->getLanguageValue('config_autoplay'));
		// set playspeed
		$config['playspeed'] = $this->confText($this->admin_lang->getLanguageValue('config_playspeed'), '100', '3', "/^[0-9]{1,5}$/", $this->admin_lang->getLanguageValue('config_playspeed_error'));
		// set preload
		$config['preload'] = $this->confText($this->admin_lang->getLanguageValue('config_preload'), '100', '3', "/^[0-9]{1,2}$/", $this->admin_lang->getLanguageValue('config_preload_error'));
		// set loop
		$config['loop'] = $this->confCheck($this->admin_lang->getLanguageValue('config_loop'));

		// select animation effect
		$descriptions = array(
			'fade' => $this->admin_lang->getLanguageValue('config_effect_fade'),
			'elastic' => $this->admin_lang->getLanguageValue('config_effect_elastic'),
			'none' => $this->admin_lang->getLanguageValue('config_effect_none'),
		);
		// set openeffect
		$config['openeffect'] = $this->confSelect($this->admin_lang->getLanguageValue('config_openeffect'), $descriptions, false);
		// set closeeffect
		$config['closeeffect'] = $this->confSelect($this->admin_lang->getLanguageValue('config_closeeffect'), $descriptions, false);
		// set nexteffect
		$config['nexteffect'] = $this->confSelect($this->admin_lang->getLanguageValue('config_nexteffect'), $descriptions, false);
		// set preveffect
		$config['preveffect'] = $this->confSelect($this->admin_lang->getLanguageValue('config_preveffect'), $descriptions, false);

		// set openspeed
		$config['openspeed'] = $this->confText($this->admin_lang->getLanguageValue('config_openspeed'), '100', '3', "/^[0-9]{1,4}$/", $this->admin_lang->getLanguageValue('config_openspeed_error'));
		// set closespeed
		$config['closespeed'] = $this->confText($this->admin_lang->getLanguageValue('config_closespeed'), '100', '3', "/^[0-9]{1,4}$/", $this->admin_lang->getLanguageValue('config_closespeed_error'));
		// set nextspeed
		$config['nextspeed'] = $this->confText($this->admin_lang->getLanguageValue('config_nextspeed'), '100', '3', "/^[0-9]{1,4}$/", $this->admin_lang->getLanguageValue('config_nextspeed_error'));
		// set prevspeed
		$config['prevspeed'] = $this->confText($this->admin_lang->getLanguageValue('config_prevspeed'), '100', '3', "/^[0-9]{1,4}$/", $this->admin_lang->getLanguageValue('config_prevspeed_error'));

		// Template
		$config['--template~~'] = '
				<div><div style="margin-bottom:5px;">' . $this->admin_lang->getLanguageValue('admin_rgba') . '</div>{backgroundred_text} {backgroundred_description} &nbsp; &nbsp; {backgroundgreen_text} {backgroundgreen_description} &nbsp; &nbsp; {backgroundblue_text} {backgroundblue_description} &nbsp; &nbsp; {backgroundalpha_text} {backgroundalpha_description}</div>
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
				<div style="margin-bottom:5px;">{autosize_checkbox} {autosize_description}</div>
				<div style="margin-bottom:5px;">{autoresize_checkbox} {autoresize_description}</div>
				<div style="margin-bottom:5px;">{autocenter_checkbox} {autocenter_description}</div>
				<div style="margin-bottom:5px;">{fittoview_checkbox} {fittoview_description}</div>
			</li>
			<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix">
				<div><div style="width:32%;display:inline-block;margin-right:5px;">{scrolling_select}</div> {scrolling_description}</div>
			</li>
			<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix">
				<div><div style="width:32%;display:inline-block;margin-right:5px;">{wrapcss_text}</div> {wrapcss_description}</div>
			<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix">
				<div style="margin-bottom:5px;">{arrows_checkbox} {arrows_description}</div>
				<div style="margin-bottom:5px;">{closebtn_checkbox} {closebtn_description}</div>
				<div style="margin-bottom:5px;">{closeclick_checkbox} {closeclick_description}</div>
				<div style="margin-bottom:5px;">{nextclick_checkbox} {nextclick_description}</div>
				<div style="margin-bottom:5px;">{mousewheel_checkbox} {mousewheel_description}</div>
			<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix">
				<div style="margin-bottom:5px;">{autoplay_checkbox} {autoplay_description}</div>
				<div style="margin-bottom:5px;">{loop_checkbox} {loop_description}</div>
				<div style="margin-bottom:5px;">{playspeed_text} {playspeed_description}</div>
				<div style="margin-bottom:5px;">{preload_text} {preload_description}</div>
			<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix">
				<div style="width:24%;display:inline-block;vertical-align:top;">{openeffect_select} {openeffect_description}</div>
				<div style="width:24%;display:inline-block;vertical-align:top;">{closeeffect_select} {closeeffect_description}</div>
				<div style="width:24%;display:inline-block;vertical-align:top;">{nexteffect_select} {nexteffect_description}</div>
				<div style="width:24%;display:inline-block;vertical-align:top;">{preveffect_select} {preveffect_description}</div>
			<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix">
				<div style="width:24%;display:inline-block;vertical-align:top;">{openspeed_text} {openspeed_description}</div>
				<div style="width:24%;display:inline-block;vertical-align:top;">{closespeed_text} {closespeed_description}</div>
				<div style="width:24%;display:inline-block;vertical-align:top;">{nextspeed_text} {nextspeed_description}</div>
				<div style="width:24%;display:inline-block;vertical-align:top;">{prevspeed_text} {prevspeed_description}
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

	protected function wrap($key, $value, $toquote, $wrap) {
		if ($toquote) $value = '"' . $value . '"';
		$wrapped = str_replace(array('#','|'), array($key, $value), $wrap);
		return $wrapped;
	}

}

?>