<?php if(!defined('IS_CMS')) die();

/**
 * Plugin:   fancyBox
 * @author:  HPdesigner (kontakt[at]devmount[dot]de)
 * @version: v0.1.2014-02-04
 * @license: GPL v3
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

	const plugin_author = 'HPdesigner';
	const plugin_docu = 'http://www.devmount.de/Develop/Mozilo%20Plugins/fancyBox.html';
	const plugin_title = 'fancyBox';
	const plugin_version = 'v0.1.2014-02-04';
	const mozilo_version = '2.0';
	private $plugin_tags = array(
		'image' => '{fancyBox|image|<gallery>|<file>}',
		'inline' => '{fancyBox|inline|<text>|<content>}',
		'link' => '{fancyBox|link|<text>|<url>}',
	);
	
	// set configuration elements, their default values and their configuration parameters
	// element => default, toquote, wrap (# -> key, | -> value), type, maxlength/descriptions, size/multiselect, regex
	private $confdefault = array(
		'backgroundred'		=> array('0',false,'','text','','3',"/^[0-9]{1,3}$/"),
		'backgroundgreen'	=> array('0',false,'','text','','3',"/^[0-9]{1,3}$/"),
		'backgroundblue'	=> array('0',false,'','text','','3',"/^[0-9]{1,3}$/"),
		'backgroundalpha'	=> array('0.5',false,'','text','','3',''), // TODO: regex float
		'padding'			=> array('15',false,'# : |,','text','','3',"/^[0-9]{1,3}$/"),
		'margin'			=> array('20',false,'# : |,','text','','3',"/^[0-9]{1,3}$/"),
		'width'				=> array('800',false,'# : |,','text','','3',"/^[0-9]{1,4}$/"),
		'height'			=> array('600',false,'# : |,','text','','3',"/^[0-9]{1,4}$/"),
		'minwidth'			=> array('100',false,'# : |,','text','','3',"/^[0-9]{1,4}$/"),
		'minheight'			=> array('100',false,'# : |,','text','','3',"/^[0-9]{1,4}$/"),
		'maxwidth'			=> array('9999',false,'# : |,','text','','3',"/^[0-9]{1,4}$/"),
		'maxheight'			=> array('9999',false,'# : |,','text','','3',"/^[0-9]{1,4}$/"),
		'thumbwidth'		=> array('100',false,'# : |,','text','','3',"/^[0-9]{1,4}$/"),
		'autosize'			=> array('true',false,'# : |,','check'),
		'autoresize'		=> array('!isTouch',false,'# : |,','check'),
		'autocenter'		=> array('!isTouch',false,'# : |,','check'),
		'fittoview'			=> array('true',false,'# : |,','check'),
		'scrolling'			=> array('auto',true,'# : |,','select',array('auto','yes','no','visible'),false),
		'wrapcss'			=> array('',true,'# : |,','text','','',''),
		'arrows'			=> array('true',false,'# : |,','check'),
		'closebtn'			=> array('true',false,'# : |,','check'),
		'closeclick'		=> array('false',false,'# : |,','check'),
		'nextclick'			=> array('false',false,'# : |,','check'),
		'mousewheel'		=> array('true',false,'# : |,','check'),
		'autoplay'			=> array('false',false,'# : |,','check'),
		'playspeed'			=> array('3000',false,'# : |,','text','','3',"/^[0-9]{1,5}$/"),
		'preload'			=> array('3',false,'# : |,','text','','3',"/^[0-9]{1,2}$/"),
		'loop'				=> array('true',false,'# : |,','check'),
		'openeffect'		=> array('fade',true,'# : |,','select',array('fade','elastic','none'),false),
		'closeeffect'		=> array('fade',true,'# : |,','select',array('fade','elastic','none'),false),
		'nexteffect'		=> array('elastic',true,'# : |,','select',array('fade','elastic','none'),false),
		'preveffect'		=> array('elastic',true,'# : |,','select',array('fade','elastic','none'),false),
		'openspeed'			=> array('250',false,'# : |,','text','','',"/^[0-9]{1,4}$/"),
		'closespeed'		=> array('250',false,'# : |,','text','','',"/^[0-9]{1,4}$/"),
		'nextspeed'			=> array('250',false,'# : |,','text','','',"/^[0-9]{1,4}$/"),
		'prevspeed'			=> array('250',false,'# : |,','text','','',"/^[0-9]{1,4}$/"),
	);


	function getContent($value) {

		global $CMS_CONF;
		global $syntax;
		global $CatPage;
		// initialize mozilo gallery
		include_once($BASE_DIR . 'cms/' . 'GalleryClass.php');
		$this->gallery = new GalleryClass();
		$this->gallery->initial_Galleries(false,false,false,true);

		$this->cms_lang = new Language(PLUGIN_DIR_REL . 'fancyBox/lang/cms_language_' . $CMS_CONF->get('cmslanguage') . '.txt');

		// get params
		$values = explode('|', $value);
		$param_typ = trim($values[0]);
		$param_gal = trim($values[1]);
		$param_img = trim($values[2]);

		// get conf and set default
		$conf = array();
		foreach ($this->confdefault as $elem => $default) {
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

		if ($param_typ == 'image') {
			$class = $class . '_image';
			// check wether gallery exists
			$is_gallery = false;
			$is_image = false;
			if ($param_gal != '') {
				if (!in_array($param_gal, $this->gallery->get_GalleriesArray()))
					return $this->throwError($this->cms_lang->getLanguageValue('error_nonexisting_gallery',$param_gal));
				else
					$is_gallery = true;
			}
			// check wether image exists
			if ($is_gallery) {
				if ($param_img != '') {
					if (!in_array($param_img, $this->gallery->get_GalleryImagesArray($param_gal))) {
						return $this->throwError($this->cms_lang->getLanguageValue('error_nonexisting_gallery_image',$param_img));
					} else
						$is_image = true;
				}
			} else {
				if ($param_img != '') {
					// TODO: check image in files
				} else
					// no gallery and no image specified: throw error message
					return $this->throwError($this->cms_lang->getLanguageValue('error_no_image_param'));
			}

			// gallery with no image specified: load whole gallery
			if ($is_gallery and $param_img == '') {
				$images = $this->gallery->get_GalleryImagesArray($param_gal);
				$class = $class . '_' . str_replace('%20', '_', $param_gal);
				// build image tag for every image
				foreach ($images as $image) {
					// build image paths
					$path_img = $this->gallery->get_ImageSrc($param_gal, $image, false);
					$path_thumb = $this->gallery->get_ImageSrc($param_gal, $image, true);
					$title = $this->gallery->get_ImageDescription($param_gal, $image, 'html');
					$content .= $this->buildImgTag($class, $param_gal, $path_img, $path_thumb, $title);
				}
			}

			// gallery with image specified: load single image from gallery
			if ($is_gallery and $param_img != '') {
				// build image paths
				$path_img = $this->gallery->get_ImageSrc($param_gal, $param_img, false);
				$path_thumb = $this->gallery->get_ImageSrc($param_gal, $param_img, true);
				// build single image tag
				$class = $class . '_' . $param_gal;
				$title = $this->gallery->get_ImageDescription($param_gal, $param_img, 'html');
				$content .= $this->buildImgTag($class, $param_gal, $path_img, $path_thumb, $title);
			}

			// no gallery but image specified: load single image from files
			if (!$is_gallery and $param_img != '') {
				$param_img = explode('%3A', $param_img);
				$param_cat = urlencode($param_img[0]);
				$param_file = $param_img[1];
				// build image path
				$path_img =  URL_BASE .'kategorien/' . $param_cat . '/dateien/' . $param_file;
				// build single image tag
				$content .= $this->buildImgTag($class, $param_cat, $path_img, $path_img, '', $conf['thumbwidth'][0]);
			}
		}
		else if ($param_typ == 'inline') {
			$class = $class . '_inline';
			$id = rand();
			// build inline content
			$content .= '<div id="' . $id . '" style="display:none;">' . $param_img . '</div>';
			// build link
			$content .= '<a class="' . self::plugin_title . ' ' . $class . '" href="#' . $id . '"> ' . $param_gal . '</a>';
		}
		else if ($param_typ == 'link') {
			$class = $class . '_link';
			// build link
			$content .= '<a class="' . self::plugin_title . ' ' . $class . ' fancybox.iframe" href="' . $param_img . '"> ' . $param_gal . '</a>';
		} else {
			return $this->throwError($this->cms_lang->getLanguageValue('error_param_typ'));
		}

		// attach fancyBox
		$fancyjs = '<!-- initialize fancyBox plugin: --> ';
		$fancyjs .= '<script type="text/javascript"> $(document).ready(function() { $(".' . $class . '").fancybox({';

		foreach ($conf as $key => $value)
			$fancyjs .= $this->wrap($key, $value[0], $value[1], $value[2]);

		$fancyjs .=	'});});</script>';
		$syntax->insert_in_head($fancyjs);

		$content .= '<!-- END fancyBox plugin content --> ';
		return $content;
	}


	function getConfig() {

		$config = array();

		// read config values
		foreach ($this->confdefault as $key => $value) {
			switch ($value[3]) {
				case 'text': $config[$key] = $this->confText($this->admin_lang->getLanguageValue('config_' . $key), $value[4], $value[5], $value[6], $this->admin_lang->getLanguageValue('config_' . $key . '_error')); break;
				case 'check': $config[$key] = $this->confCheck($this->admin_lang->getLanguageValue('config_' . $key)); break;
				case 'select': 
					$descriptions = array();
					foreach ($value[4] as $desc) $descriptions[$desc] = $this->admin_lang->getLanguageValue('config_' . $desc);
					$config[$key] = $this->confSelect($this->admin_lang->getLanguageValue('config_' . $key),$descriptions,$value[5]); break;
				default: break;
			}
		}

		// Template CSS
		$css_admin_header = 'margin: -0.4em -0.8em -5px -0.8em; padding: 10px; background-color: #234567; color: #fff; text-shadow: #000 0 1px 3px;';
		$css_admin_subheader = 'margin: -0.4em -0.8em 5px -0.8em; padding: 5px 9px; background-color: #ddd; color: #111; text-shadow: #fff 0 1px 2px;';
		$css_admin_li = 'background: #eee;';
		$css_admin_default = 'color: #aaa;padding-left: 6px;';

		// build Template
		$config['--template~~'] = '
				<div style="' . $css_admin_header . '"><span style="font-size:20px;vertical-align: top;padding-top: 3px;display: inline-block;">'
				. $this->admin_lang->getLanguageValue('admin_header',self::plugin_title)
				. '</span><a href="' . self::plugin_docu . '" target="_blank"><img style="float:right;" src="http://media.devmount.de/logo_pluginconf.png" /></a></div>
			</li>
			<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix" style="' . $css_admin_li . '">
				<div style="' . $css_admin_subheader . '">' . $this->admin_lang->getLanguageValue('admin_rgba') . '</div>
				{backgroundred_text} {backgroundred_description} &nbsp; &nbsp; {backgroundgreen_text} {backgroundgreen_description} &nbsp; &nbsp; {backgroundblue_text} {backgroundblue_description} &nbsp; &nbsp; {backgroundalpha_text} {backgroundalpha_description}
			</li>
			<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix" style="' . $css_admin_li . '">
				<div style="' . $css_admin_subheader . '">' . $this->admin_lang->getLanguageValue('admin_spacing') . '</div>
				<div style="margin-bottom:5px;">{padding_text} {padding_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['margin'][0] .']</span></div>
				<div style="margin-bottom:5px;">{margin_text} {margin_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['padding'][0] .']</span></div>
			</li>
			<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix" style="' . $css_admin_li . '">
				<div style="' . $css_admin_subheader . '">' . $this->admin_lang->getLanguageValue('admin_dimension') . '</div>
				<div style="width:24%;display:inline-block;vertical-align:top;">
					<div style="margin-bottom:5px;">{width_text} {width_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['width'][0] .']</span></div>
					<div style="margin-bottom:5px;">{height_text} {height_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['height'][0] .']</span></div>
				</div>
				<div style="width:24%;display:inline-block;vertical-align:top;">
					<div style="margin-bottom:5px;">{minwidth_text} {minwidth_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['minwidth'][0] .']</span></div>
					<div style="margin-bottom:5px;">{minheight_text} {minheight_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['minheight'][0] .']</span></div>
				</div>
				<div style="width:24%;display:inline-block;vertical-align:top;">
					<div style="margin-bottom:5px;">{maxwidth_text} {maxwidth_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['maxwidth'][0] .']</span></div>
					<div style="margin-bottom:5px;">{maxheight_text} {maxheight_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['maxheight'][0] .']</span></div>
				</div>
				<div style="width:24%;display:inline-block;vertical-align:top;">
					<div style="margin-bottom:5px;">{thumbwidth_text} {thumbwidth_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['thumbwidth'][0] .']</span></div>
				</div>
			</li>
			<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix" style="' . $css_admin_li . '">
				<div style="' . $css_admin_subheader . '">' . $this->admin_lang->getLanguageValue('admin_size_position') . '</div>
				<div style="margin-bottom:5px;">{autosize_checkbox} {autosize_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['autosize'][0] .']</span></div>
				<div style="margin-bottom:5px;">{autoresize_checkbox} {autoresize_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['autoresize'][0] .']</span></div>
				<div style="margin-bottom:5px;">{autocenter_checkbox} {autocenter_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['autocenter'][0] .']</span></div>
				<div style="margin-bottom:5px;">{fittoview_checkbox} {fittoview_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['fittoview'][0] .']</span></div>
			</li>
			<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix" style="' . $css_admin_li . '">
				<div style="' . $css_admin_subheader . '">' . $this->admin_lang->getLanguageValue('admin_scrollbar') . '</div>
				<div style="width:32%;display:inline-block;margin-right:5px;">{scrolling_select}</div> {scrolling_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['scrolling'][0] .']</span>
			</li>
			<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix" style="' . $css_admin_li . '">
				<div style="' . $css_admin_subheader . '">' . $this->admin_lang->getLanguageValue('admin_cssclass') . '</div>
				<div style="width:32%;display:inline-block;margin-right:5px;">{wrapcss_text}</div> {wrapcss_description}
			<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix" style="' . $css_admin_li . '">
				<div style="' . $css_admin_subheader . '">' . $this->admin_lang->getLanguageValue('admin_navigation') . '</div>
				<div style="margin-bottom:5px;">{arrows_checkbox} {arrows_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['arrows'][0] .']</span></div>
				<div style="margin-bottom:5px;">{closebtn_checkbox} {closebtn_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['closebtn'][0] .']</span></div>
				<div style="margin-bottom:5px;">{closeclick_checkbox} {closeclick_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['closeclick'][0] .']</span></div>
				<div style="margin-bottom:5px;">{nextclick_checkbox} {nextclick_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['nextclick'][0] .']</span></div>
				<div style="margin-bottom:5px;">{mousewheel_checkbox} {mousewheel_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['mousewheel'][0] .']</span></div>
			<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix" style="' . $css_admin_li . '">
				<div style="' . $css_admin_subheader . '">' . $this->admin_lang->getLanguageValue('admin_slides') . '</div>
				<div style="margin-bottom:5px;">{autoplay_checkbox} {autoplay_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['autoplay'][0] .']</span></div>
				<div style="margin-bottom:5px;">{loop_checkbox} {loop_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['loop'][0] .']</span></div>
				<div style="margin-bottom:5px;">{playspeed_text} {playspeed_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['playspeed'][0] .']</span></div>
				<div style="margin-bottom:5px;">{preload_text} {preload_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['preload'][0] .']</span></div>
			<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix" style="' . $css_admin_li . '">
				<div style="' . $css_admin_subheader . '">' . $this->admin_lang->getLanguageValue('admin_animation') . '</div>
				<div style="width:15%;display:inline-block;vertical-align:top;padding-right:10px;">{openeffect_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['openeffect'][0] .']</span> {openeffect_select}</div>
				<div style="width:15%;display:inline-block;vertical-align:top;padding-right:10px;">{closeeffect_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['closeeffect'][0] .']</span> {closeeffect_select}</div>
				<div style="width:15%;display:inline-block;vertical-align:top;padding-right:10px;">{nexteffect_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['nexteffect'][0] .']</span> {nexteffect_select}</div>
				<div style="width:15%;display:inline-block;vertical-align:top;padding-right:10px;">{preveffect_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['preveffect'][0] .']</span> {preveffect_select}</div>
			<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix" style="' . $css_admin_li . '">
				<div style="' . $css_admin_subheader . '">' . $this->admin_lang->getLanguageValue('admin_duration') . '</div>
				<div style="width:15%;display:inline-block;vertical-align:top;padding-right:10px;">{openspeed_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['openspeed'][0] .']</span><br />{openspeed_text}</div>
				<div style="width:15%;display:inline-block;vertical-align:top;padding-right:10px;">{closespeed_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['closespeed'][0] .']</span><br />{closespeed_text}</div>
				<div style="width:15%;display:inline-block;vertical-align:top;padding-right:10px;">{nextspeed_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['nextspeed'][0] .']</span><br />{nextspeed_text}</div>
				<div style="width:15%;display:inline-block;vertical-align:top;padding-right:10px;">{prevspeed_description} <span style="' . $css_admin_default .'">[' . $this->confdefault['prevspeed'][0] .']</span><br />{prevspeed_text}
		';

		return $config;
	}  


	function getInfo() {

		global $ADMIN_CONF;

		$this->admin_lang = new Language(PLUGIN_DIR_REL . 'fancyBox/lang/admin_language_' . $ADMIN_CONF->get('language') . '.txt');

		// build plugin tags
		$tags = array();
		foreach ($this->plugin_tags as $key => $tag) $tags[$tag] = $this->admin_lang->getLanguageValue('tag_' . $key);

		$info = array(
			// plugin name and version
			'<b>' . self::plugin_title . '</b> ' . self::plugin_version,
			// moziloCMS version
			self::mozilo_version,
			// short description, only <span> and <br /> are allowed
			$this->admin_lang->getLanguageValue('description'), 
			// author
			self::plugin_author,
			// documentation url
			self::plugin_docu,
			// plugin tag for select box when editing a page, can be emtpy
			$tags
		);

		return $info;
	}


	protected function buildImgTag($class, $rel, $href, $src, $title='', $width='') {
		$html .= '<a ';
		$html .= 	'class="' . self::plugin_title . ' ' . $class . '" ';
		$html .= 	'rel="' . $rel . '" ';
		$html .= 	'href="' . $href . '" ';
		if ($title != '') $html .= 	'title="' . $title . '" ';
		$html .= '>';
		$html .= 	'<img src="' . $src .'" ';
		if ($width != '') $html .= 'width="' . $width . '" ';
		$html .= ' />';
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

	protected function throwError($text) {
		return '<div class="' . self::plugin_title . 'Error">'
				. '<div>' . $this->cms_lang->getLanguageValue('error') . '</div>'
				. '<span>' . $text. '</span>'
				. '</div>';
	}

}

?>