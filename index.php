<?php

/**
 * moziloCMS Plugin: fancyBox
 *
 * The fancyBox plugin offers a nice and elegant way to add zooming
 * functionality for images, inline, linked and html content.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_MoziloPlugins
 * @author   DEVMOUNT <mail@devmount.de>
 * @license  CC BY-NC 3.0
 * @version  GIT: v0.3.2014-08-17
 * @link     https://github.com/devmount-mozilo/fancyBox
 * @link     http://devmount.de/Develop/moziloCMS/Plugins/fancyBox.html
 * @see:     "For I know the plans I have for you" declares the LORD,
 *           "plans to prosper you and not to harm you, plans to give you hope
 *           and a future."
 *           - The Bible
 *
 * Plugin created by DEVMOUNT
 * www.devmount.de
 *
 */

// only allow moziloCMS environment
if (!defined('IS_CMS')) {
    die();
}

/**
 * fancyBox Class
 *
 * @category PHP
 * @package  PHP_MoziloPlugins
 * @author   DEVMOUNT <mail@devmount.de>
 * @license  CC BY-NC 3.0
 * @link     https://github.com/devmount-mozilo/fancyBox
 */
class fancyBox extends Plugin
{
    private $_admin_lang;
    private $_cms_lang;
    var $gallery;

    const PLUGIN_AUTHOR = 'DEVMOUNT';
    const PLUGIN_TITLE = 'fancyBox';
    const PLUGIN_VERSION = 'v0.3.2014-08-17';
    const MOZILO_VERSION = '2.0';
    const PLUGIN_DOCU
        = 'http://www.devmount.de/Develop/moziloCMS/Plugins/fancyBox.html';

    private $_plugin_tags = array(
        'image' => '{fancyBox|image|<gallery>|<file>|<remote>}',
        'inline' => '{fancyBox|inline|<text>|<content>|<title>}',
        'link' => '{fancyBox|link|<text>|<url>|<title>}',
    );

    const LOGO_URL = 'http://media.devmount.de/logo_pluginconf.png';

    /**
     * set configuration elements, their default values and their configuration
     * parameters
     *
     * @var array $_confdefault
     *      text     => default, type, maxlength, size, regex
     *      textarea => default, type, cols, rows, regex
     *      password => default, type, maxlength, size, regex, saveasmd5
     *      check    => default, type
     *      radio    => default, type, descriptions
     *      select   => default, type, descriptions, multiselect
     */
    private $_confdefault = array(
        'backgroundred' => array(
            '0',
            '',
            'text',
            '',
            '3',
            "/^[0-9]{1,3}$/",
        ),
        'backgroundgreen' => array(
            '0',
            '',
            'text',
            '',
            '3',
            "/^[0-9]{1,3}$/",
        ),
        'backgroundblue' => array(
            '0',
            '',
            'text',
            '',
            '3',
            "/^[0-9]{1,3}$/",
        ),
        'backgroundalpha' => array(
            '0.5',
            '',
            'text',
            '',
            '3',
            '', // TODO: regex float
        ),
        'padding' => array(
            '15',
            '# : |,',
            'text',
            '',
            '3',
            "/^[0-9]{1,3}$/",
        ),
        'margin' => array(
            '20',
            '# : |,',
            'text',
            '',
            '3',
            "/^[0-9]{1,3}$/",
        ),
        'width' => array(
            '800',
            '# : |,',
            'text',
            '',
            '3',
            "/^[0-9]{1,4}$/",
        ),
        'height' => array(
            '600',
            '# : |,',
            'text',
            '',
            '3',
            "/^[0-9]{1,4}$/",
        ),
        'minwidth' => array(
            '100',
            '# : |,',
            'text',
            '',
            '3',
            "/^[0-9]{1,4}$/",
        ),
        'minheight' => array(
            '100',
            '# : |,',
            'text',
            '',
            '3',
            "/^[0-9]{1,4}$/",
        ),
        'maxwidth' => array(
            '9999',
            '# : |,',
            'text',
            '',
            '3',
            "/^[0-9]{1,4}$/",
        ),
        'maxheight' => array(
            '9999',
            '# : |,',
            'text',
            '',
            '3',
            "/^[0-9]{1,4}$/",
        ),
        'thumbwidth' => array(
            '100',
            '# : |,',
            'text',
            '',
            '3',
            "/^[0-9]{1,4}$/",
        ),
        'autosize' => array(
            'true',
            '# : |,',
            'check',
        ),
        'autoresize' => array(
            '!isTouch',
            '# : "|",',
            'check',
        ),
        'autocenter' => array(
            '!isTouch',
            '# : "|",',
            'check',
        ),
        'fittoview' => array(
            'true',
            '# : |,',
            'check',
        ),
        'scrolling' => array(
            'auto',
            '# : "|",',
            'select',
            array(
                'auto',
                'yes',
                'no',
                'visible',
            ),
            false,
        ),
        'wrapcss' => array(
            '',
            '# : "|",',
            'text',
            '',
            '',
            '',
        ),
        'arrows' => array(
            'true',
            '# : |,',
            'check',
        ),
        'closebtn' => array(
            'true',
            '# : |,',
            'check',
        ),
        'closeclick' => array(
            'false',
            '# : |,',
            'check',
        ),
        'nextclick' => array(
            'false',
            '# : |,',
            'check',
        ),
        'mousewheel' => array(
            'true',
            '# : |,',
            'check',
        ),
        'autoplay' => array(
            'false',
            '# : |,',
            'check',
        ),
        'playspeed' => array(
            '3000',
            '# : |,',
            'text',
            '',
            '3',
            "/^[0-9]{1,5}$/",
        ),
        'preload' => array(
            '3',
            '# : |,',
            'text',
            '',
            '3',
            "/^[0-9]{1,2}$/",
        ),
        'loop' => array(
            'true',
            '# : |,',
            'check',
        ),
        'openeffect' => array(
            'fade',
            '# : "|",',
            'select',
            array(
                'fade',
                'elastic',
                'none',
            ),
            false,
        ),
        'closeeffect' => array(
            'fade',
            '# : "|",',
            'select',
            array(
                'fade',
                'elastic',
                'none',
            ),
            false,
        ),
        'nexteffect' => array(
            'elastic',
            '# : "|",',
            'select',
            array(
                'fade',
                'elastic',
                'none',
            ),
            false,
        ),
        'preveffect' => array(
            'elastic',
            '# : "|",',
            'select',
            array(
                'fade',
                'elastic',
                'none',
            ),
            false,
        ),
        'openspeed' => array(
            '250',
            '# : |,',
            'text',
            '',
            '',
            "/^[0-9]{1,4}$/",
        ),
        'closespeed' => array(
            '250',
            '# : |,',
            'text',
            '',
            '',
            "/^[0-9]{1,4}$/",
        ),
        'nextspeed' => array(
            '250',
            '# : |,',
            'text',
            '',
            '',
            "/^[0-9]{1,4}$/",
        ),
        'prevspeed' => array(
            '250',
            '# : |,',
            'text',
            '',
            '',
            "/^[0-9]{1,4}$/",
        ),
        'titlepos' => array(
            'default',
            'helpers : { title : { type : "|" },',
            'select',
            array('default',
                'over',
                'inside',
                'outside',
                'none',
            ),
            false,
        ),
    );

    /**
     * creates plugin content
     *
     * @param string $value Parameter divided by '|'
     *
     * @return string HTML output
     */
    function getContent($value)
    {
        global $CMS_CONF;
        global $syntax;
        global $CatPage;
        // initialize mozilo gallery
        include_once BASE_DIR_CMS . 'GalleryClass.php';
        $this->gallery = new GalleryClass();
        $this->gallery->initial_Galleries(false, false, false, true);

        $this->_cms_lang = new Language(
            $this->PLUGIN_SELF_DIR
            . 'lang/cms_language_'
            . $CMS_CONF->get('cmslanguage')
            . '.txt'
        );
        // get params
        list($param_typ, $param1, $param2, $param3)
            = $this->makeUserParaArray($value, false, "|");

        // get conf and set default
        $conf = array();
        foreach ($this->_confdefault as $elem => $default) {
            $conf[$elem] = array(($this->settings->get($elem) == '')
                ? $default[0]
                : $this->settings->get($elem),$default[1]);
        }

        // validate conf
        $conf['backgroundcolor'] = array(
            $conf['backgroundred'][0] . ', '
            . $conf['backgroundgreen'][0] . ', '
            . $conf['backgroundblue'][0] . ', '
            . $conf['backgroundalpha'][0],
            'overlay : { css : { "background" : "rgba(|)" } } },',
        );

        if ($conf['titlepos'][0] == 'default') {
            $conf['titlepos'][1] = 'helpers : { ';
        }

        $no_title = $conf['titlepos'][0] == 'none';

        // delete partial conf elements, contents are now in 'backgroundcolor'
        unset(
            $conf['backgroundred'],
            $conf['backgroundgreen'],
            $conf['backgroundblue'],
            $conf['backgroundalpha']
        );

        // add jquery
        $syntax->insert_jquery_in_head('jquery');
        // add mousewheel plugin (optional)
        if ($conf['mousewheel'][0] == 'true') {
            $syntax->insert_in_head(
                '<script type="text/javascript" src="'
                . $this->PLUGIN_SELF_URL
                . 'lib/jquery.mousewheel.pack.js"></script>'
            );
        }
        // add fancyBox
        $syntax->insert_in_head(
            '<link rel="stylesheet" href="'
            . $this->PLUGIN_SELF_URL
            . 'source/jquery.fancybox.css" type="text/css" media="screen" />'
        );
        $syntax->insert_in_head(
            '<script type="text/javascript" src="'
            . $this->PLUGIN_SELF_URL
            . 'source/jquery.fancybox.pack.js"></script>'
        );

        // initialize return content and default class
        $content = '<!-- BEGIN fancyBox plugin content --> ';
        $class = 'fancybox';

        // check if gallery or image should be launched by remote link
        $is_remote = trim($param_typ == 'image' and $param3 != '');

        if ($param_typ == 'image') {
            $class = $class . '_image';
            // check wether gallery exists
            $is_gallery = false;
            $is_image = false;
            if ($param1 != '') {
                if (!in_array($param1, $this->gallery->get_GalleriesArray())) {
                    return $this->throwError(
                        $this->_cms_lang->getLanguageValue(
                            'error_nonexisting_gallery',
                            $param1
                        )
                    );
                } else {
                    $is_gallery = true;
                }
            }
            // check wether image exists
            if ($is_gallery) {
                if ($param2 != '') {
                    if (!in_array(
                        $param2,
                        $this->gallery->get_GalleryImagesArray($param1)
                    )
                    ) {
                        return $this->throwError(
                            $this->_cms_lang->getLanguageValue(
                                'error_nonexisting_gallery_image',
                                $param2
                            )
                        );
                    } else {
                        $is_image = true;
                    }
                }
            } else {
                if ($param2 != '') {
                    // TODO: check image in files
                } else {
                    // no gallery and no image specified: throw error message
                    return $this->throwError(
                        $this->_cms_lang->getLanguageValue('error_no_image_param')
                    );
                }
            }

            // gallery with no image specified: load whole gallery
            if ($is_gallery and $param2 == '') {
                $images = $this->gallery->get_GalleryImagesArray($param1);
                $class = $class . '_' . str_replace('%20', '_', $param1);

                // build remote link and hide gallery
                if ($is_remote) {
                    $content
                        .= '<a href="'
                        . $this->gallery->get_ImageSrc($param1, $images[0], false)
                        . '" rel="' . $param1 . '" class="' . $class . '">'
                        . $param3 . '</a>';
                    unset($images[0]);
                    $content .= '<div style="display:none;">';
                }

                // build image tag for every image
                foreach ($images as $image) {
                    // build image paths
                    $path_img
                        = $this->gallery->get_ImageSrc($param1, $image, false);
                    $path_thumb
                        = $this->gallery->get_ImageSrc($param1, $image, true);
                    $title = $no_title
                        ? ''
                        : $this->gallery->get_ImageDescription(
                            $param1,
                            $image,
                            'html'
                        );
                    $content .= $this->buildImgTag(
                        $class,
                        $param1,
                        $path_img,
                        $path_thumb,
                        $title
                    );
                }

                if ($is_remote) {
                    $content .= '</div>';
                }
            }

            // gallery with image specified: load single image from gallery
            if ($is_gallery and $param2 != '') {
                // build image paths
                $path_img = $this->gallery->get_ImageSrc($param1, $param2, false);
                $path_thumb = $this->gallery->get_ImageSrc($param1, $param2, true);
                // build class and title
                $class = $class . '_' . str_replace('%20', '_', $param1);
                $title = $no_title
                    ? ''
                    : $this->gallery->get_ImageDescription(
                        $param1,
                        $param2,
                        'html'
                    );

                // build remote link and hide image
                if ($is_remote) {
                    $content
                        .= '<a href="' . $path_img
                        . '" rel="' . $param1 . '" class="' . $class . '">'
                        . $param3
                        . '</a>';
                    $content .= '<div style="display:none;">';
                }

                // build single image tag
                $content .= $this->buildImgTag(
                    $class,
                    $param1,
                    $path_img,
                    $path_thumb,
                    $title
                );

                if ($is_remote) {
                    $content .= '</div>';
                }
            }

            // no gallery but image specified: load single image from files
            if (!$is_gallery and $param2 != '') {
                list($param_cat, $param_file)
                    = $CatPage->split_CatPage_fromSyntax($param2, true);
                // build image path
                $path_img = $CatPage->get_srcFile($param_cat, $param_file);

                // build remote link and hide image
                if ($is_remote) {
                    $content
                        .= '<a href="' . $path_img
                        . '" rel="' . $param_cat . '" class="' . $class . '">'
                        . $param3
                        . '</a>';
                    $content .= '<div style="display:none;">';
                }

                // build single image tag
                $content .= $this->buildImgTag(
                    $class,
                    $param_cat,
                    $path_img,
                    $path_img,
                    '',
                    $conf['thumbwidth'][0]
                );

                if ($is_remote) {
                    $content .= '</div>';
                }
            }
        } else if ($param_typ == 'inline') {
            $class = $class . '_inline';
            $id = rand();
            // build inline content
            $content
                .= '<div id="' . $id . '" style="display:none;">'
                . $param2
                . '</div>';
            // build link
            if ($no_title) {
                $param3 = '';
            }
            $content
                .= '<a class="' . self::PLUGIN_TITLE . ' ' . $class
                . '" href="#' . $id . '" title="' . $param3 . '"> '
                . $param1
                . '</a>';
        } else if ($param_typ == 'link') {
            $class = $class . '_link';
            // build link
            if ($no_title) {
                $param3 = '';
            }
            $content
                .= '<a class="' . self::PLUGIN_TITLE . ' ' . $class .
                ' fancybox.iframe" href="' . $param2 . '" title="' . $param3 . '"> '
                . $param1
                . '</a>';
        } else {
            return $this->throwError(
                $this->_cms_lang->getLanguageValue('error_param_type')
            );
        }

        // attach fancyBox
        $fancyjs = '<!-- initialize fancyBox plugin: --> ';
        $fancyjs
            .= '<script type="text/javascript"> $(document).ready(function() { $(".'
            . $class
            . '").fancybox({';

        foreach ($conf as $key => $value) {
            $fancyjs .= $this->wrap($key, $value[0], $value[1]);
        }

        // fancyBox template
        $fancyjs .= 'tpl : {
            error : \'<p class="fancybox-error">'
            . $this->_cms_lang->getLanguageValue('fancy_error')
            . '</p>\',
            closeBtn : \'<a title="'
            . $this->_cms_lang->getLanguageValue('fancy_close')
            . '" class="fancybox-item fancybox-close" href="javascript:;"></a>\',
            next : \'<a title="'
            . $this->_cms_lang->getLanguageValue('fancy_next')
            . '" class="fancybox-nav fancybox-next" href="javascript:;">'
            . '<span></span></a>\',
            prev : \'<a title="'
            . $this->_cms_lang->getLanguageValue('fancy_prev')
            . '" class="fancybox-nav fancybox-prev" href="javascript:;">'
            . '<span></span></a>\'
        }';

        $fancyjs .= '});});</script>';
        $syntax->insert_in_head($fancyjs);

        $content .= '<!-- END fancyBox plugin content --> ';
        return $content;
    }

    /**
     * sets backend configuration elements and template
     *
     * @return Array configuration
     */
    function getConfig()
    {
        $config = array();

        // read configuration values
        foreach ($this->_confdefault as $key => $value) {
            // handle each form type
            switch ($value[2]) {
            case 'text':
                $config[$key] = $this->confText(
                    $this->_admin_lang->getLanguageValue('config_' . $key),
                    $value[3],
                    $value[4],
                    $value[5],
                    $this->_admin_lang->getLanguageValue(
                        'config_' . $key . '_error'
                    )
                );
                break;
            case 'check':
                $config[$key] = $this->confCheck(
                    $this->_admin_lang->getLanguageValue('config_' . $key)
                );
                break;
            case 'select':
                $descriptions = array();
                foreach ($value[3] as $desc) {
                    $descriptions[$desc] = $this->_admin_lang->getLanguageValue(
                        'config_' . $key . '_' . $desc
                    );
                }
                $config[$key] = $this->confSelect(
                    $this->_admin_lang->getLanguageValue('config_' . $key),
                    $descriptions,
                    $value[4]
                );
                break;
            default:
                break;
            }
        }

        // Template CSS
        $css_admin_header = '
            margin: -0.4em -0.8em -5px -0.8em;
            padding: 10px;
            background-color: #234567;
            color: #fff;
            text-shadow: #000 0 1px 3px;
        ';
        $css_admin_header_span = '
            font-size:20px;
            vertical-align: top;
            padding-top: 3px;
            display: inline-block;
        ';
        $css_admin_subheader = '
            margin: -0.4em -0.8em 5px -0.8em;
            padding: 5px 9px;
            background-color: #ddd;
            color: #111;
            text-shadow: #fff 0 1px 2px;
        ';
        $css_admin_li = '
            background: #eee;
        ';
        $css_admin_four_col = '
            width:15%;
            display:inline-block;
            vertical-align:top;
            padding-right:10px;
        ';
        $css_admin_default = '
            color: #aaa;
            padding-left: 6px;
        ';

        // build Template
        $config['--template~~'] = '
                <div style="' . $css_admin_header . '">'
                . '<span style="' . $css_admin_header_span . '">'
                . $this->_admin_lang->getLanguageValue(
                    'admin_header',
                    self::PLUGIN_TITLE
                )
                . '</span><a href="' . self::PLUGIN_DOCU . '" target="_blank">'
                . '<img style="float:right;" src="' . self::LOGO_URL . '" />'
                . '</a></div>
            </li>
            <li class="mo-in-ul-li ui-widget-content" style="' . $css_admin_li . '">
                <div style="' . $css_admin_subheader . '">'
                . $this->_admin_lang->getLanguageValue('admin_rgba')
                . '</div>
                {backgroundred_text} {backgroundred_description} &nbsp; &nbsp;
                {backgroundgreen_text} {backgroundgreen_description} &nbsp; &nbsp;
                {backgroundblue_text} {backgroundblue_description} &nbsp; &nbsp;
                {backgroundalpha_text} {backgroundalpha_description}
            </li>
            <li class="mo-in-ul-li ui-widget-content" style="' . $css_admin_li . '">
                <div style="' . $css_admin_subheader . '">'
                . $this->_admin_lang->getLanguageValue('admin_spacing') . '</div>
                <div style="margin-bottom:5px;">
                    {padding_text} {padding_description}
                    <span style="' . $css_admin_default .'">
                        [' . $this->_confdefault['margin'][0] .']
                    </span>
                </div>
                <div style="margin-bottom:5px;">
                    {margin_text} {margin_description}
                    <span style="' . $css_admin_default .'">
                        [' . $this->_confdefault['padding'][0] .']
                    </span>
                </div>
            </li>
            <li class="mo-in-ul-li ui-widget-content" style="' . $css_admin_li . '">
                <div style="' . $css_admin_subheader . '">'
                . $this->_admin_lang->getLanguageValue('admin_dimension')
                . '</div>
                <div style="width:24%;display:inline-block;vertical-align:top;">
                    <div style="margin-bottom:5px;">
                        {width_text} {width_description}
                        <span style="' . $css_admin_default .'">
                            [' . $this->_confdefault['width'][0] .']
                        </span>
                    </div>
                    <div style="margin-bottom:5px;">
                        {height_text} {height_description}
                        <span style="' . $css_admin_default .'">
                            [' . $this->_confdefault['height'][0] .']
                        </span>
                    </div>
                </div>
                <div style="width:24%;display:inline-block;vertical-align:top;">
                    <div style="margin-bottom:5px;">
                        {minwidth_text} {minwidth_description}
                        <span style="' . $css_admin_default .'">
                            [' . $this->_confdefault['minwidth'][0] .']
                        </span>
                    </div>
                    <div style="margin-bottom:5px;">
                        {minheight_text} {minheight_description}
                        <span style="' . $css_admin_default .'">
                            [' . $this->_confdefault['minheight'][0] .']
                        </span>
                    </div>
                </div>
                <div style="width:24%;display:inline-block;vertical-align:top;">
                    <div style="margin-bottom:5px;">
                        {maxwidth_text} {maxwidth_description}
                        <span style="' . $css_admin_default .'">
                            [' . $this->_confdefault['maxwidth'][0] .']
                        </span>
                    </div>
                    <div style="margin-bottom:5px;">
                        {maxheight_text} {maxheight_description}
                        <span style="' . $css_admin_default .'">
                            [' . $this->_confdefault['maxheight'][0] .']
                        </span>
                    </div>
                </div>
                <div style="width:24%;display:inline-block;vertical-align:top;">
                    <div style="margin-bottom:5px;">
                        {thumbwidth_text} {thumbwidth_description}
                        <span style="' . $css_admin_default .'">
                            [' . $this->_confdefault['thumbwidth'][0] .']
                        </span>
                    </div>
                </div>
            </li>
            <li class="mo-in-ul-li ui-widget-content" style="' . $css_admin_li . '">
                <div style="' . $css_admin_subheader . '">'
                . $this->_admin_lang->getLanguageValue('admin_size_position')
                . '</div>
                <div style="margin-bottom:5px;">
                    {autosize_checkbox} {autosize_description}
                    <span style="' . $css_admin_default .'">
                        [' . $this->_confdefault['autosize'][0] .']
                    </span>
                </div>
                <div style="margin-bottom:5px;">
                    {autoresize_checkbox} {autoresize_description}
                    <span style="' . $css_admin_default .'">
                        [' . $this->_confdefault['autoresize'][0] .']
                    </span>
                </div>
                <div style="margin-bottom:5px;">
                    {autocenter_checkbox} {autocenter_description}
                    <span style="' . $css_admin_default .'">
                        [' . $this->_confdefault['autocenter'][0] .']
                    </span>
                </div>
                <div style="margin-bottom:5px;">
                    {fittoview_checkbox} {fittoview_description}
                    <span style="' . $css_admin_default .'">
                        [' . $this->_confdefault['fittoview'][0] .']
                    </span>
                </div>
            </li>
            <li class="mo-in-ul-li ui-widget-content" style="' . $css_admin_li . '">
                <div style="' . $css_admin_subheader . '">'
                . $this->_admin_lang->getLanguageValue('admin_scrollbar')
                . '</div>
                <div style="width:32%;display:inline-block;margin-right:5px;">
                    {scrolling_select}
                </div>
                {scrolling_description}
                <span style="' . $css_admin_default .'">
                    [' . $this->_confdefault['scrolling'][0] .']
                </span>
            </li>
            <li class="mo-in-ul-li ui-widget-content" style="' . $css_admin_li . '">
                <div style="' . $css_admin_subheader . '">'
                . $this->_admin_lang->getLanguageValue('admin_cssclass')
                . '</div>
                <div style="width:32%;display:inline-block;margin-right:5px;">
                    {wrapcss_text}
                </div>
                {wrapcss_description}
            <li class="mo-in-ul-li ui-widget-content" style="' . $css_admin_li . '">
                <div style="' . $css_admin_subheader . '">'
                . $this->_admin_lang->getLanguageValue('admin_navigation')
                . '</div>
                <div style="margin-bottom:5px;">
                    {arrows_checkbox} {arrows_description}
                    <span style="' . $css_admin_default .'">
                        [' . $this->_confdefault['arrows'][0] .']
                    </span>
                </div>
                <div style="margin-bottom:5px;">
                    {closebtn_checkbox} {closebtn_description}
                    <span style="' . $css_admin_default .'">
                        [' . $this->_confdefault['closebtn'][0] .']
                    </span>
                </div>
                <div style="margin-bottom:5px;">
                    {closeclick_checkbox} {closeclick_description}
                    <span style="' . $css_admin_default .'">
                        [' . $this->_confdefault['closeclick'][0] .']
                    </span>
                </div>
                <div style="margin-bottom:5px;">
                    {nextclick_checkbox} {nextclick_description}
                    <span style="' . $css_admin_default .'">
                        [' . $this->_confdefault['nextclick'][0] .']
                    </span>
                </div>
                <div style="margin-bottom:5px;">
                    {mousewheel_checkbox} {mousewheel_description}
                    <span style="' . $css_admin_default .'">
                        [' . $this->_confdefault['mousewheel'][0] .']
                    </span>
                </div>
            <li class="mo-in-ul-li ui-widget-content" style="' . $css_admin_li . '">
                <div style="' . $css_admin_subheader . '">'
                . $this->_admin_lang->getLanguageValue('admin_title')
                . '</div>
                <div style="width:32%;display:inline-block;margin-right:5px;">
                    {titlepos_select}
                </div>
                {titlepos_description}
            <li class="mo-in-ul-li ui-widget-content" style="' . $css_admin_li . '">
                <div style="' . $css_admin_subheader . '">'
                . $this->_admin_lang->getLanguageValue('admin_slides')
                . '</div>
                <div style="margin-bottom:5px;">
                    {autoplay_checkbox} {autoplay_description}
                    <span style="' . $css_admin_default .'">
                        [' . $this->_confdefault['autoplay'][0] .']
                    </span>
                </div>
                <div style="margin-bottom:5px;">
                    {loop_checkbox} {loop_description}
                    <span style="' . $css_admin_default .'">
                        [' . $this->_confdefault['loop'][0] .']
                    </span>
                </div>
                <div style="margin-bottom:5px;">
                    {playspeed_text} {playspeed_description}
                    <span style="' . $css_admin_default .'">
                        [' . $this->_confdefault['playspeed'][0] .']
                    </span>
                </div>
                <div style="margin-bottom:5px;">
                    {preload_text} {preload_description}
                    <span style="' . $css_admin_default .'">
                        [' . $this->_confdefault['preload'][0] .']
                    </span>
                </div>
            <li class="mo-in-ul-li ui-widget-content" style="' . $css_admin_li . '">
                <div style="' . $css_admin_subheader . '">'
                . $this->_admin_lang->getLanguageValue('admin_animation')
                . '</div>
                <div style="' . $css_admin_four_col .'">{openeffect_description}
                    <span style="' . $css_admin_default .'">
                        [' . $this->_confdefault['openeffect'][0] .']
                    </span> {openeffect_select}</div>
                <div style="' . $css_admin_four_col .'">
                    {closeeffect_description}
                    <span style="' . $css_admin_default .'">
                        [' . $this->_confdefault['closeeffect'][0] .']
                    </span> {closeeffect_select}</div>
                <div style="' . $css_admin_four_col .'">
                    {nexteffect_description}
                    <span style="' . $css_admin_default .'">
                        [' . $this->_confdefault['nexteffect'][0] .']
                    </span> {nexteffect_select}</div>
                <div style="' . $css_admin_four_col .'">
                    {preveffect_description}
                    <span style="' . $css_admin_default .'">
                        [' . $this->_confdefault['preveffect'][0] .']
                    </span> {preveffect_select}</div>
            <li class="mo-in-ul-li ui-widget-content" style="' . $css_admin_li . '">
                <div style="' . $css_admin_subheader . '">'
                . $this->_admin_lang->getLanguageValue('admin_duration')
                . '</div>
                <div style="' . $css_admin_four_col .'">
                    {openspeed_description}
                    <span style="' . $css_admin_default .'">
                        [' . $this->_confdefault['openspeed'][0] .']
                    </span><br />{openspeed_text}</div>
                <div style="' . $css_admin_four_col .'">
                    {closespeed_description}
                    <span style="' . $css_admin_default .'">
                        [' . $this->_confdefault['closespeed'][0] .']
                    </span><br />{closespeed_text}</div>
                <div style="' . $css_admin_four_col .'">
                    {nextspeed_description}
                    <span style="' . $css_admin_default .'">
                        [' . $this->_confdefault['nextspeed'][0] .']
                    </span><br />{nextspeed_text}</div>
                <div style="' . $css_admin_four_col .'">
                    {prevspeed_description}
                    <span style="' . $css_admin_default .'">
                        [' . $this->_confdefault['prevspeed'][0] .']
                    </span><br />{prevspeed_text}
        ';

        return $config;
    }

    /**
     * sets default backend configuration elements, if no plugin.conf.php is
     * created yet
     *
     * @return Array configuration
     */
    function getDefaultSettings()
    {
        $def_set = array('active' => 'true');
        foreach ($this->_confdefault as $elem => $default) {
            $def_set[$elem] = $default[0];
        }
        return $def_set;
    }

    /**
     * sets backend plugin information
     *
     * @return Array information
     */
    function getInfo()
    {
        global $ADMIN_CONF;
        $this->_admin_lang = new Language(
            $this->PLUGIN_SELF_DIR
            . 'lang/admin_language_'
            . $ADMIN_CONF->get('language')
            . '.txt'
        );

        // build plugin tags
        $tags = array();
        foreach ($this->_plugin_tags as $key => $tag) {
            $tags[$tag] = $this->_admin_lang->getLanguageValue('tag_' . $key);
        }

        $info = array(
            '<b>' . self::PLUGIN_TITLE . '</b> ' . self::PLUGIN_VERSION,
            self::MOZILO_VERSION,
            $this->_admin_lang->getLanguageValue('description'),
            self::PLUGIN_AUTHOR,
            array(
                self::PLUGIN_DOCU,
                self::PLUGIN_TITLE . ' '
                . $this->_admin_lang->getLanguageValue('on_devmount')
            ),
            $tags
        );

        return $info;
    }

    /**
     * creates an html image tag
     *
     * @param string $class attribute
     * @param string $rel   attribute
     * @param string $href  attribute
     * @param string $src   attribute
     * @param string $title attribute
     * @param string $width attribute
     *
     * @return string        html image tag
     */
    protected function buildImgTag($class, $rel, $href, $src, $title='', $width='')
    {
        $html = '<a ';
        $html .=    'class="' . self::PLUGIN_TITLE . ' ' . $class . '" ';
        $html .=    'rel="' . $rel . '" ';
        $html .=    'href="' . $href . '" ';
        if ($title != '') {
            $html .=  'title="' . $title . '" ';
        }
        $html .= '>';
        $html .=    '<img src="' . $src .'" ';
        if ($width != '') {
            $html .= 'width="' . $width . '" ';
        }
        $html .= ' />';
        $html .= '</a>';
        return $html;
    }

    /**
     * creates configuration for checkboxes
     *
     * @param string $description Label
     *
     * @return Array  Configuration
     */
    protected function confCheck($description)
    {
        // required properties
        return array(
            'type' => 'checkbox',
            'description' => $description,
        );
    }

    /**
     * creates configuration for text fields
     *
     * @param string $description Label
     * @param string $maxlength   Maximum number of characters
     * @param string $size        Size
     * @param string $regex       Regular expression for allowed input
     * @param string $regex_error Wrong input error message
     *
     * @return Array  Configuration
     */
    protected function confText(
        $description,
        $maxlength = '',
        $size = '',
        $regex = '',
        $regex_error = ''
    ) {
        // required properties
        $conftext = array(
            'type' => 'text',
            'description' => $description,
        );
        // optional properties
        if ($maxlength != '') {
            $conftext['maxlength'] = $maxlength;
        }
        if ($size != '') {
            $conftext['size'] = $size;
        }
        if ($regex != '') {
            $conftext['regex'] = $regex;
        }
        if ($regex_error != '') {
            $conftext['regex_error'] = $regex_error;
        }
        return $conftext;
    }

    /**
     * creates configuration for select fields
     *
     * @param string  $description  Label
     * @param string  $descriptions Array Single item labels
     * @param boolean $multiple     Enable multiple item selection
     *
     * @return Array   Configuration
     */
    protected function confSelect($description, $descriptions, $multiple = false)
    {
        // required properties
        return array(
            'type' => 'select',
            'description' => $description,
            'descriptions' => $descriptions,
            'multiple' => $multiple,
        );
    }

    /**
     * inserts configuration key and value into a wraper text
     *
     * @param string $key   configuration id
     * @param string $value configuration value
     * @param string $wrap  wrapper text
     *
     * @return string        wrapped text with content
     */
    protected function wrap($key, $value, $wrap)
    {
        $wrapped = str_replace(array('#','|'), array($key, $value), $wrap);
        return $wrapped;
    }

    /**
     * throws styled error message
     *
     * @param string $text Content of error message
     *
     * @return string HTML content
     */
    protected function throwError($text)
    {
        return '<div class="' . self::PLUGIN_TITLE . 'Error">'
            . '<div>' . $this->_cms_lang->getLanguageValue('error') . '</div>'
            . '<span>' . $text. '</span>'
            . '</div>';
    }

}

?>