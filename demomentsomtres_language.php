<?php

/**
 * @package QuBic_Idioma
 */
/*
  Plugin Name: DeMomentSomTres Language
  Plugin URI: http://www.DeMomentSomTres.com/catala
  Description: DeMomentSomTres Language allows to have different instances of a blog using different languages on a network installation.
  Version: 1.4
  Author: DeMomentSomTres
  Author URI: http://www.DeMomentSomTres.com
  License: GPLv2 or later
 */

/*
  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

define('QBC_IDIOMA_VERSION', '1.3');
define('QBC_IDIOMA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('QBC_IDIOMA_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('QBC_IDIOMA_TEXT_DOMAIN', 'QuBic_Idioma');
define('QBC_IDIOMA_OPTIONS', 'QuBicIdioma_options');
define('QBC_IDIOMA_PREFIX', 'QuBicIdioma_relation-');

require_once QBC_IDIOMA_PLUGIN_PATH . 'functions.php';
require_once QBC_IDIOMA_PLUGIN_PATH . 'admin.php';
require_once QBC_IDIOMA_PLUGIN_PATH . 'widgets.php';
require_once QBC_IDIOMA_PLUGIN_PATH . 'language-detection.php';

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
    exit;
}
//add_action('template_redirect', 'demomentsomtres_language_redirect');//12.23s
add_action('plugins_loaded', 'demomentsomtres_language_redirect', 0); //10.91s

load_plugin_textdomain(QBC_IDIOMA_TEXT_DOMAIN, false, QBC_IDIOMA_PLUGIN_URL . '/languages');
add_action('init', 'QuBicIdioma_init');
if (is_admin()):
    add_action('admin_menu', 'QuBicIdioma_admin');
else:
    wp_register_script('QuBic_Idioma_selectmenu', plugin_dir_url(__FILE__) . 'jquery.ui.selectmenu.js', array('jquery', 'jquery-ui-core', 'jquery-ui-widget'), '', true);
    wp_register_script('QuBic_Idioma_widgets', plugin_dir_url(__FILE__) . 'widgets.js', array('QuBic_Idioma_selectmenu'), '', true);
    wp_register_style('QuBic_Idioma_UI', plugin_dir_url(__FILE__) . 'jqueryUI.css', '', '');
    wp_register_style('QuBic_Idioma_Widgets', plugin_dir_url(__FILE__) . 'widgets.css', '', '');
endif;
add_action('widgets_init', 'QuBicIdioma_widgets_init');
add_action('add_meta_boxes', 'QuBicIdioma_activar_relacions');
add_action('save_post', 'QuBicIdioma_relacions_save_meta');
if (!demomentsomtres_shortcode_mode()):
    add_filter('the_content', 'QuBicIdioma_print_links', 1000);
endif;
add_shortcode('DeMomentSomTres-Language','demomentsomtres_language_shortcode');
add_action('widgets_init', create_function('', 'return register_widget("DeMomentSomTres_Post_Translations");'));

/**
 * Plugin init
 * @since 0.1
 */
function QuBicIdioma_init() {
    
}

?>