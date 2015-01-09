<?php
/*
  Plugin Name: DeMomentSomTres Language
  Plugin URI: http://demomentsomtres.com/english/wordpress-plugins/demomentsomtres-language/
  Description: DeMomentSomTres Language allows to have different instances of a blog using different languages on a network installation.
  Version: 2.0.1
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

define('DMS3_LANGUAGE_TEXT_DOMAIN', 'QuBic_Idioma');

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
    exit;
}

if (!function_exists('is_plugin_active'))
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
if ((!is_plugin_active('demomentsomtres-tools/demomentsomtres-tools.php')) && (!is_plugin_active_for_network('demomentsomtres-tools/demomentsomtres-tools.php'))):
    add_action('admin_notices', 'demomentsomtres_language_noTools');
else:
    $dms3Language = new DeMomentSomTresLanguage();
endif;

function demomentsomtres_Language_noTools() {
    ?>
    <div class="error">
        <p><?php _e('The plugin DeMomentSomTres Language requires the free DeMomentSomTres Tools plugin.', DMS3_LANGUAGE_TEXT_DOMAIN); ?>
            <br/>
            <a href="http://demomentsomtres.com/english/wordpress-plugins/demomentsomtres-tools/?utm_source=web&utm_medium=wordpress&utm_campaign=adminnotice&utm_term=dms3Restaurant" target="_blank"><?php _e('Download it here', DMS3_RESTAURANT_TEXT_DOMAIN); ?></a>
            <br/>
        </p>
    </div>
    <?php
}

class DeMomentSomTresLanguage {

    const MENU_SLUG = 'dms3Language';
    const TEXT_DOMAIN = DMS3_LANGUAGE_TEXT_DOMAIN;
    const OPTIONS = 'QuBicIdioma_options';
    const PAGE = "QuBicIdioma";
    const SECTION1 = 'QuBicIdioma_mode';
    const SECTION2 = 'QuBicIdioma_main';
    const SECTION3 = 'QuBicIdioma_types';
    const SECTION4 = 'QuBicIdioma_configuracio';
    const SECTION5 = 'DMS3Language_grups';
    const FIELDPOSTTYPE = 'QuBicIdioma_type_';
    const FIELDMODE = 'landing_mode';
    const FIELDLITERAL = 'literal';
    const FIELDORDRE = 'ordre';
    const FIELDBROWSERLANGUAGE = 'browser_langs';
    const FIELDDEFAULTSITE = 'default_site';
    const FIELDBODYCLASSES = 'body_classes';
    const IDIOMA_PREFIX = 'QuBicIdioma_relation-';
    const NETWORKOPTIONS = "dms3Language";
    const NETWORKGROUPS = "groups";
    const NETWORKFIELDUSEGROUPS = "useGroups";
    const NETWORKSUBMIT = "dms3LanguageSubmit";

    private $pluginURL;
    private $pluginPath;
    private $langDir;

    /**
     * @since 2.0
     */
    function DeMomentSomTresLanguage() {
        $this->pluginURL = plugin_dir_url(__FILE__);
        $this->pluginPath = plugin_dir_path(__FILE__);
        $this->langDir = dirname(plugin_basename(__FILE__)) . '/languages';

        add_action('plugins_loaded', array($this, 'redirect'), 0);
        add_action('plugins_loaded', array(&$this, 'plugin_init'));
    }

    /**
     * @since 2.0
     */
    function plugin_init() {
        load_plugin_textdomain(DMS3_LANGUAGE_TEXT_DOMAIN, false, $this->langDir);
        add_filter('get_blogs_of_user', array($this, 'mysites'));
        add_action('admin_menu', array(&$this, 'admin_menu'));
        add_action('network_admin_menu', array($this, 'network_admin_register_settings_page'));
        add_action('admin_init', array(&$this, 'admin_init'));
        add_filter('body_class', array($this, 'bodyClasses'));
        add_action('widgets_init', array($this, 'widgets_init'));
        add_action('add_meta_boxes', array($this, 'addRelationship'));
        add_action('save_post', array($this, 'metaboxRelationshipSave'));
        add_shortcode('DeMomentSomTres-Language', array($this, 'shortcode'));
    }

    /**
     * @since 2.0
     */
    function admin_menu() {
        add_options_page(__('DeMomentSomTres Language', self::TEXT_DOMAIN), __('DeMomentSomTres Language', self::TEXT_DOMAIN), 'manage_options', self::MENU_SLUG, array(&$this, 'admin_page'));
    }

    /**
     * @since 2.0
     */
    function admin_init() {
        register_setting(self::OPTIONS, self::OPTIONS, array(&$this, 'admin_validate_options'));
        add_settings_section(self::SECTION1, __('Mode', self::TEXT_DOMAIN), array($this, 'admin_section_mode_text'), self::PAGE);
        add_settings_section(self::SECTION2, __('Language', self::TEXT_DOMAIN), array($this, 'admin_section_main_text'), self::PAGE);
        add_settings_section(self::SECTION3, __('Post Types', self::TEXT_DOMAIN), array($this, 'admin_section_types_text'), self::PAGE);
        add_settings_section(self::SECTION4, __('Current Settings', self::TEXT_DOMAIN), array($this, 'admin_section_config_text'), self::PAGE);
        add_settings_section(self::SECTION5, __('Groups Configuration', self::TEXT_DOMAIN), array($this, 'admin_section_groups'), self::PAGE);
        add_settings_field(self::FIELDMODE, __('Landing mode', self::TEXT_DOMAIN), array($this, 'admin_field_landing_mode'), self::PAGE, self::SECTION1);
        add_settings_field(self::FIELDLITERAL, __('Language text', self::TEXT_DOMAIN), array($this, 'admin_field_literal'), self::PAGE, self::SECTION2);
        add_settings_field(self::FIELDORDRE, __('Order', self::TEXT_DOMAIN), array($this, 'admin_field_ordre'), self::PAGE, self::SECTION2);
        add_settings_field(self::FIELDBROWSERLANGUAGE, __('Browser Languages', self::TEXT_DOMAIN), array($this, 'admin_field_browser_languages'), self::PAGE, self::SECTION2);
        add_settings_field(self::FIELDDEFAULTSITE, __('Default Site', self::TEXT_DOMAIN), array($this, 'admin_field_default_site'), self::PAGE, self::SECTION2);
        add_settings_field(self::FIELDBODYCLASSES, __('Body classes', self::TEXT_DOMAIN), array($this, 'admin_field_body_classes'), self::PAGE, self::SECTION2);
        $array = $this->getPostTypesToTranslateCandidates();
        foreach ($array as $key => $value):
            $etiqueta = $value->labels->name;
            $nom = $this->postTypeOptionName($key);
            $valor = $this->isPostTypeToTranslate($key);
            $args = array(
                'nom' => $nom,
                'valor' => $valor,
            );
            add_settings_field(self::FIELDPOSTTYPE . $key, $etiqueta, array($this, 'admin_fields_types'), self::PAGE, self::SECTION3, $args);
        endforeach;
    }

    /**
     * @since 2.0
     */
    function admin_page() {
        echo '<div class="wrap">';
        screen_icon();
        echo '<h2>' . __('MultiLanguage', self::TEXT_DOMAIN) . '</h2>';
        echo '<form action="options.php" method="post">';
        settings_fields(self::OPTIONS);
        do_settings_sections(self::PAGE);
        echo '<input class="button button-primary" name="Submit" type="submit" value="' . __('Save Changes', self::TEXT_DOMAIN) . '"/>';
        echo '</form>';
        echo '</div>';
        ?>
        <div style="background-color:#eee;/*display:none;*/">
            <h2><?php _e('Options', self::TEXT_DOMAIN); ?></h2>
            <pre style="font-size:0.8em;"><?php print_r(get_option(self::OPTIONS)); ?></pre>
        </div>
        <?php
    }

    /**
     * Writes the mode section text
     * @since 2.0
     */
    function admin_section_mode_text() {
        echo '<ul>';
        echo '<li><strong>' . __("Landing site", self::TEXT_DOMAIN) . '</strong>: ' . __("Site redirects based on browser language.", self::TEXT_DOMAIN) . '</li>';
        echo '<li><strong>' . __("Language site", self::TEXT_DOMAIN) . '</strong>: ' . __("Site shows specific language", self::TEXT_DOMAIN) . '</li>';
        echo '</ul>';
    }

    /**
     * Writes the main section text
     * @since 2.0
     */
    function admin_section_main_text() {
        echo '<p>' . __("Language settings for current blog. Ignored for landing site.", self::TEXT_DOMAIN) . '</p>';
    }

    /**
     * @since 2.0
     */
    function admin_section_types_text() {
        echo '<p>' . __("Post types that can be translated.", self::TEXT_DOMAIN) . '</p>';
    }

    /**
     * Writes the configuration text
     * @since 0.2
     * @updated 0.5 simplify output
     */
    function admin_section_config_text() {
        $isLanding = $this->isLanding();
        echo "<p>";
        if (is_multisite()):
            _e('Multisite set.', self::TEXT_DOMAIN);
        else:
            _e('Multisite not set.', self::TEXT_DOMAIN);
            echo '<br/>';
            _e('This plugin needs a network set in order to work.', self::TEXT_DOMAIN);
        endif;
        echo "</p>";
        if ($isLanding):
            echo "<p>" . _e('This is a landing site', self::TEXT_DOMAIN) . "</p>";
        else:
            echo "<p>" . _e('This is not a landing site.', self::TEXT_DOMAIN) . "</p>";
        endif;
        $lang = $this->getDefaultLanguage();
        echo "<p>" . _e('Your browser default language: ', self::TEXT_DOMAIN) . $lang . "</p>";
        $destination = $this->destination();
        echo "<p>" . _e('You would be redirected to: ', self::TEXT_DOMAIN) . $destination . ".</p>";
    }

    function admin_section_groups() {
        if ($this->isGroupsEnabled()):
            echo '<p>' . __('Group configuration is enabled', self::TEXT_DOMAIN) . '</p>';
            $group = $this->getBlogGroup();
            if ($group === false):
                echo '<p>' . __('ERROR: No group set for this site.', self::TEXT_DOMAIN) . '</p>';
            else:
                echo '<p>' . __('This blog belongs to site group', self::TEXT_DOMAIN) . '&nbsp;<strong>' . $group . '</strong>&nbsp';
                echo __('whose members are:', self::TEXT_DOMAIN) . '</p>';
                echo '<table><tbody>';
                echo '<tr>';
                echo '<th>';
                _e('ID', self::TEXT_DOMAIN);
                echo '</th>';
                echo '<th>';
                _e('Type', self::TEXT_DOMAIN);
                echo '</th>';
                echo '<th>';
                _e('URL', self::TEXT_DOMAIN);
                echo '</th>';
                echo '<th>';
                _e('Default site', self::TEXT_DOMAIN);
                echo '</th>';
                echo '<th>';
                _e('Browser languages', self::TEXT_DOMAIN);
                echo '</th>';
                echo '</tr>';
                $allBlogs = $this->getBlogs();
                foreach ($allBlogs as $b):
                    if ($b['landing']):
                        $prefix = __('LANDING', self::TEXT_DOMAIN);
                    else:
                        $prefix = $b['language'];
                    endif;
                    echo '<tr><th>';
                    echo $b['blog_id'];
                    echo '</th>';
                    echo '<td>';
                    echo $prefix;
                    echo '</td>';
                    echo '<td>';
                    echo $b['details']->siteurl;
                    echo '</td>';
                    echo '<td>';
                    echo $b['default_site']==1?__('Yes',self::TEXT_DOMAIN):'';
                    echo '</td>';
                    echo '<td>';
                    echo $b['browser_langs'];
                    echo '</td>';
                    echo '</tr>';
                endforeach;
                echo '</tbody></table>';
            endif;
        else:
            echo '<p>' . __('Group configuration is not enabled',self::TEXT_DOMAIN) . '</p>';
        endif;
    }

    function admin_field_landing_mode() {
        $nom = self::FIELDMODE;
        $valor = DeMomentSomTresTools::get_option(self::OPTIONS, $nom, '');
        DeMomentsomtresTools::adminHelper_inputArray(self::OPTIONS, $nom, $valor, array('type' => 'checkbox'));
    }

    function admin_field_literal() {
        $nom = self::FIELDLITERAL;
        $valor = DeMomentSomTresTools::get_option(self::OPTIONS, $nom, '');
        DeMomentsomtresTools::adminHelper_inputArray(self::OPTIONS, $nom, $valor);
    }

    function admin_field_ordre() {
        $nom = self::FIELDORDRE;
        $valor = DeMomentSomTresTools::get_option(self::OPTIONS, $nom, '');
        DeMomentsomtresTools::adminHelper_inputArray(self::OPTIONS, $nom, $valor);
    }

    function admin_field_browser_languages() {
        $nom = self::FIELDBROWSERLANGUAGE;
        $valor = DeMomentSomTresTools::get_option(self::OPTIONS, $nom, '');
        DeMomentsomtresTools::adminHelper_inputArray(self::OPTIONS, $nom, $valor);
        echo '<p class="small">' . __('Comma separated list', self::TEXT_DOMAIN) . '</p>';
    }

    function admin_field_default_site() {
        $nom = self::FIELDDEFAULTSITE;
        $valor = DeMomentSomTresTools::get_option(self::OPTIONS, $nom, '');
        DeMomentsomtresTools::adminHelper_inputArray(self::OPTIONS, $nom, $valor, array('type' => 'checkbox'));
    }

    function admin_field_body_classes() {
        $nom = self::FIELDBODYCLASSES;
        $valor = DeMomentSomTresTools::get_option(self::OPTIONS, $nom, '');
        DeMomentsomtresTools::adminHelper_inputArray(self::OPTIONS, $nom, $valor);
        echo '<p class="small">' . __('Comma separated list of classes to add', self::TEXT_DOMAIN) . '</p>';
    }

    /**
     * Outputs the HTML for the checkbox
     * @since 0.4
     * @updated 0.5 Change on parameter structure
     * @uses QuBicIdioma_admin_check
     */
    function admin_fields_types($args) {
        if (isset($args['nom'])):
            if (!isset($args['valor'])):
                $args['valor'] = 0;
            endif;
            DeMomentsomtresTools::adminHelper_inputArray(self::OPTIONS, $nom, $valor, array('type' => 'checkbox'));
        endif;
    }

    /**
     * @param array $input
     * @return array
     * @since 2.0
     */
    function admin_validate_options($input = array()) {
        return $input;
    }

    function network_admin_register_settings_page() {
        add_menu_page(__('DeMomentSomTres Language', self::TEXT_DOMAIN) . ' ' . __('MultiSite Settings', self::TEXT_DOMAIN), __('Language', self::TEXT_DOMAIN), 'delete_users', 'dms3_language', array($this, 'network_admin_page'));
    }

    function network_admin_page() {
        $options = get_site_option(self::NETWORKOPTIONS);
        if (isset($options[self::NETWORKFIELDUSEGROUPS])):
            $useGroups = 'on';
        else:
            $useGroups = '';
        endif;
        $blogs = $this->getBlogs();
        echo "<div class='wrap'>";
        echo '<h2>' . __('DeMomentSomTres Language', self::TEXT_DOMAIN) . ' ' . __('MultiSite Settings', self::TEXT_DOMAIN) . '</h2>';
        if (isset($_POST[self::NETWORKSUBMIT])):
            check_admin_referer(self::NETWORKOPTIONS);
            $options = array();
            if (isset($_POST[self::NETWORKOPTIONS][self::NETWORKFIELDUSEGROUPS])):
                $options[self::NETWORKFIELDUSEGROUPS] = 'on';
            endif;
            if (isset($_POST[self::NETWORKOPTIONS][self::NETWORKGROUPS])):
                $options[self::NETWORKGROUPS] = $_POST[self::NETWORKOPTIONS][self::NETWORKGROUPS];
            endif;
            update_site_option(self::NETWORKOPTIONS, $options);
        endif;
        echo '<form method="post" accept-charset="' . esc_attr(get_bloginfo('charset')) . '">';
        echo wp_nonce_field(self::NETWORKOPTIONS, '_wpnonce', true, false);
        echo "<h3>" . __('General Configuration', self::TEXT_DOMAIN) . '</h3>';
        echo '<table class="form-table"><tbody>';
        echo '<tr><th scope="row">';
        echo __('Use Groups', self::TEXT_DOMAIN);
        echo '</th><td>';
        DeMomentSomTresTools::adminHelper_inputArray(self::NETWORKOPTIONS, self::NETWORKFIELDUSEGROUPS, $useGroups, array('type' => 'checkbox'));
        echo "<p class='description'>" . __("If this field is not checked any other configuration parameter in this screen will be ignored", self::TEXT_DOMAIN) . "</p>";
        echo '</td></tr>';
        echo '</tbody></table>';
        echo "<h3>" . __('Group definition', self::TEXT_DOMAIN) . '</h3>';
        echo "<p>" . __("Create groups in order to manage redirection. Groups can be any letter or number combination. Each group must contain one and only one Landing site.", self::TEXT_DOMAIN) . "</p>";
        echo '<table class="form-table"><tbody>';
        foreach ($blogs as $b):
            echo '<tr><th scope="row">';
            DeMomentSomTresTools::adminHelper_inputArray(self::NETWORKOPTIONS . "[" . self::NETWORKGROUPS . "]", $b['blog_id'], $options[self::NETWORKGROUPS][$b['blog_id']]);
            echo '</th><td>';
            if ($b['landing']):
                echo 'LANDING';
            else:
                echo $b['language'];
            endif;
            echo ' - ' . $b['details']->blogname;
            echo '</td></tr>';
        endforeach;
        echo '</tbody></table>';
        echo '<input type="submit" name="' . self::NETWORKSUBMIT . '" class="button-primary" value="' . __('Save MultiSite Settings', self::TEXT_DOMAIN) . '"/>';
        echo '</form>';
        echo "</div>";
        echo '<pre style="display:none;">$options:' . print_R($options, true) . '</pre>';
    }

    /**
     * Detects if Group configuration is enabled
     * @return boolean
     */
    function isGroupsEnabled() {
        $options = get_site_option(self::NETWORKOPTIONS);
        return isset($options[self::NETWORKFIELDUSEGROUPS]);
    }

    /**
     * Gets the groups configuration.
     * @return array the configuration of groups of the form blog_id=>group
     */
    function getGroupsConfiguration() {
        $options = get_site_option(self::NETWORKOPTIONS);
        return $options[self::NETWORKGROUPS];
    }

    /**
     * Returns the group to which the blog belongs. If not set or blank returns false
     * @global integer $blog_id
     * @return boolean/string
     */
    function getBlogGroup() {
        global $blog_id;
        $groups = $this->getGroupsConfiguration();
        if (!isset($groups[$blog_id])):
            return false;
        elseif (trim($groups[$blog_id]) == ''):
            return false;
        else:
            return $groups[$blog_id];
        endif;
    }

    /**
     * Redirects based on browser language and page configuration
     * Checks if the website is in landing mode and also checks if it is a protected URL
     * @since 2.0
     */
    function redirect() {
        $isProtected = false;
        $isLanding = (DeMomentsomtresTools::get_option(self::OPTIONS, 'landing_mode', '') != ''); //checks if is landing
        $url = $_SERVER["REQUEST_URI"];
        $protectedPrefixs = array(
            "wp-login",
            "wp-admin",
            "wp-content",
        );
        foreach ($protectedPrefixs as $prefix):
            if (!(strpos($url, $prefix) === false)):
                $isProtected = true;
            endif;
        endforeach;
        if (!$isProtected):
            if ($isLanding):
                wp_redirect($this->destination(), 301);
                exit;
            endif;
        endif;
    }

    /**
     * Checks if the web site is configured in landing mode
     * The function is cloned into redirect() because it 
     * @return boolean
     */
    function isLanding() {
        return (DeMomentsomtresTools::get_option(self::OPTIONS, 'landing_mode', '') != '');
    }

    /**
     * Gets the redirection destination based on default browser language
     * @return string
     */
    function destination() {
        $defaultSite = $this->getDefaultSite();
        $blocsActius = $this->getActiveBlogs();
        $browserLang = $this->getDefaultLanguage();
        $destination = $defaultSite;
        $urlServer = $_SERVER['SERVER_NAME'];
        $found = false;
        foreach ($blocsActius as $blog):
            $langs = explode(",", $blog['browser_langs']);
            if (in_array($browserLang, $langs, true)):
                $destination = $blog['details']->siteurl;
                $found = true;
                break;
            endif;
        endforeach;
        if (!$found):
            $browserLang = substr($browserLang, 0, 2);
            foreach ($blocsActius as $blog):
                $langs = explode(",", $blog['browser_langs']);
                if (in_array($browserLang, $langs, true)):
                    $destination = $blog['details']->siteurl;
                    $found = true;
                    break;
                endif;
            endforeach;
        endif;
        $cua = str_replace(strtolower(site_url()), '', $_SERVER['SCRIPT_URI']);
        $cua = $_SERVER['SCRIPT_URL'];
        $parseURL = explode('://', $_SERVER['SCRIPT_URI']);
        $parseSiteURL = explode('://', site_url());
        $parseDest = explode('://', $destination);
        $cua = str_ireplace($parseSiteURL[1], '', $parseURL[1]);
        $destination = $parseSiteURL[0] . '://' . $parseDest[1] . $cua;
//        echo '<pre>parseURL:' . print_r($parseURL, true) . '</pre>';
//        echo '<pre>parseDest:' . print_r($parseDest, true) . '</pre>';
//        echo '<pre>$_SERVER:' . print_r($_SERVER, true) . '</pre>';
//        echo '<pre>$_REQUEST:' . print_r($_REQUEST, true) . '</pre>';
//        echo '<pre>site_url():' . site_url() . '</pre>';
//        echo '<pre>url_server:' . $urlServer . '</pre>';
//        echo '<pre>cua:' . $cua . '</pre>';
//        echo '<pre>destination:' . $destination . '</pre>';
//        exit;
        return $destination;
    }

    /**
     * Gets the default site
     * @return type
     * @since 2.0
     */
    function getDefaultSite() {
        $defaultSite = '';
        $blocsActius = $this->getActiveBlogs();
//    echo '<pre>' . print_r($blocsActius, true) . '</pre>';
//    exit;
        foreach ($blocsActius as $blog):
//        echo '<pre>' . print_r($blog, true) . '</pre>';
            if (1 == $blog['default_site']) {
//            echo '<pre>' . print_r($blog, true) . '</pre>';
                $defaultSite = $blog['details']->siteurl;
                break;
            }
        endforeach;
        return $defaultSite;
    }

    /**
     * Gets blog and language information with actiu true and order by $criteri
     * @since 2.0
     * @param string $criteri
     */
    public function getActiveBlogs($criteria = 'ordre') {
        $info = $this->getBlogs($criteria);
        $info = array_filter($info, array($this, 'isActiveBlog'));
        return $info;
    }

    /**
     * Purgue the blogs array throwing all blogs in other groups
     * @global integer $blog_id
     * @param type $blogs
     * @return type
     */
    function groupPurge($blogs) {
        global $blog_id;
        $group = $this->getBlogGroup();
        if ($group === false || trim($group) == ''):
            return $blogs;
        endif;
        $groups = $this->getGroupsConfiguration();
        $result = array();
        foreach ($blogs as $b):
            if ($groups[$b['blog_id']] == $group):
                $result[] = $b;
            endif;
        endforeach;
        return $result;
    }

    /**
     * Gets blog and language information sort based on $criteri
     * @since 2.0
     * @param string $criteri order field used to sort
     * @return array contains a record for each blog with fields ID,name,address,order
     * @uses QuBicIdioma_obtenir_opcions_bloc
     * @uses get_blog_list deprecated
     * @uses wp_get_sites() since version 1.4 and optimization to get only public sites 
     * Uses public=2 and public=1 in order to prevent problems
     */
    function getBlogs($criteria = 'ordre') {
        $info = array();
        $blocs = wp_get_sites(array(
            'public' => 2,
        ));
        $blocs1 = wp_get_sites(array(
            'public' => 1,
        ));
        $blocs = array_merge($blocs1, $blocs);
        foreach ($blocs as $bloc):
            $opcions = $this->getBlogOptions($bloc['blog_id']);
            $detalls = get_blog_details($bloc['blog_id']);
            $info[] = array(
                'blog_id' => $bloc['blog_id'],
                'domain' => $bloc['domain'],
                'path' => $bloc['path'],
                'actiu' => isset($opcions['literal']),
                'language' => $opcions['literal'],
                'ordre' => isset($opcions['ordre']) ? $opcions['ordre'] : 99,
                'details' => $detalls,
                'default_site' => isset($opcions['default_site']) ? true : false,
                'browser_langs' => isset($opcions['browser_langs']) ? $opcions['browser_langs'] : '',
                'landing' => isset($opcions['landing_mode']) ? true : false, /* 1.1.8 */
            );
        endforeach;
        if ($this->isGroupsEnabled()):
            $info = $this->groupPurge($info);
        endif;
        $compare = $this->makeSortFunction($criteria);
        usort($info, $compare);
        return $info;
    }

    /**
     * Determines than a blog is active
     * @param array $a
     * @return boolean
     * @since 2.0
     */
    function isActiveBlog($a) {
        return $a['actiu'] && !$a['landing'];
    }

    /**
     * Gets blog multiidioma options
     * @since 0.2
     * @param integer $blog_id
     * @return array a record for each of the multilanguage options of the blog
     */
    function getBlogOptions($blog_id) {
        return get_blog_option($blog_id, self::OPTIONS);
    }

    /**
     * Create a sort by field function
     * @param string $field the field used to sort
     * @return function
     * @since 0.2
     */
    function makeSortFunction($field) {
        $code = "return strnatcmp(\$a['$field'], \$b['$field']);";
        return create_function('$a,$b', $code);
    }

    /**
     * Get browser default language
     * Based on the works of  http://www.dyeager.org
     * @return string|null
     * @since 2.0
     */
    function getDefaultLanguage() {
        if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]))
            return $this->parseDefaultLanguage($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
        else
            return NULL;
    }

    /**
     * Based on the works of  http://www.dyeager.org
     * @param type $http_accept
     * @param type $deflang
     * @return string
     */
    function parseDefaultLanguage($http_accept, $deflang = "") {
        if (isset($http_accept) && strlen($http_accept) > 1) {
# Split possible languages into array
            $x = explode(",", $http_accept);
            foreach ($x as $val) {
#check for q-value and create associative array. No q-value means 1 by rule
                if (preg_match("/(.*);q=([0-1]{0,1}\.\d{0,4})/i", $val, $matches))
                    $lang[$matches[1]] = (float) $matches[2];
                else
                    $lang[$val] = 1.0;
            }

#return default language (highest q-value)
            $qval = 0.0;
            foreach ($lang as $key => $value) {
                if ($value > $qval) {
                    $qval = (float) $value;
                    $deflang = $key;
                }
            }
        }
        return strtolower($deflang);
    }

    /**
     * Adds language and additional information to blogs 
     * @param array $blogs
     * @return array
     * @since 1.6
     */
    function mysites($blogs) {
        foreach ($blogs as $blog):
            $info = $this->getBlogOptions($blog->userblog_id);
            if (isset($info['landing_mode'])):
                $blog->blogname = __('LANDING', self::TEXT_DOMAIN) . ' - ' . $blog->blogname;
            else:
                $blog->blogname = $info['literal'] . ' - ' . $blog->blogname;
            endif;
            $blog->order = (int) $info['ordre'];
        endforeach;
        $f = create_function('$a,$b', 'return ($a->order>$b->order);');
        uasort($blogs, $f);
        return $blogs;
    }

    /**
     * @since 2.0
     */
    function bodyClasses($classes) {
        $body_classes = DeMomentSomTresTools::get_option(self::OPTIONS, 'body_classes');
        if ('' != $body_classes):
            $newClasses = explode(',', $body_classes);
            foreach ($newClasses as $c):
                $classes[] = 'dms3-language-' . trim($c);
            endforeach;
        endif;
        return $classes;
    }

    /**
     * Inits all widgets
     * @since 0.2
     */
    function widgets_init() {
        register_widget('QuBicIdioma_Chooser_Text_Widget');
        register_widget("DeMomentSomTres_Post_Translations");
    }

    /**
     * Activate metafields used to store relationships between elements
     * @since 2.0
     */
    function addRelationship() {
        $tipus = $this->postTypesToTranslate();
        foreach ($tipus as $tp):
            add_meta_box(
                    "QuBicIdioma_relation", __('Translations', self::TEXT_DOMAIN), array($this, 'metaboxRelationship'), $tp, 'normal', 'default');
        endforeach;
    }

    /**
     * Retrieves the list ob post types that can be translated.
     * @return array
     */
    function postTypesToTranslate() {
        $result = array();
        $post_types = get_post_types();
        foreach ($post_types as $pt):
            if ($this->isPostTypeToTranslate($pt)):
                $result[] = $pt;
            endif;
        endforeach;
        return $result;
    }

    /**
     * Retrieves QuBicIdioma option to decide if a posttype can be translated
     * @since 2.0
     */
    function isPostTypeToTranslate($post_type) {
        $opcions = get_option(self::OPTIONS);
        $nom_opcio = $this->postTypeOptionName($post_type);
        $valor = '';
        if (is_array($opcions)):
            if (array_key_exists($nom_opcio, $opcions)):
                $valor = $opcions[$nom_opcio];
            endif;
        endif;
        $result = ('on' == $valor);
        return $result;
    }

    /**
     * Generates the option name based on post_type
     * @since 2.0
     * @param string $post_type
     * @return string
     */
    function postTypeOptionName($post_type) {
        return 'post_type_' . $post_type;
    }

    /**
     * Writes content for relation meta box
     * @since 2.0
     * @param post_id post identifier
     */
    function metaboxRelationship($post) {
        global $blog_id;
        $llista = $this->getActiveBlogs('blog_id');
        $output = '<p>' . __('You can set the relationship between the content and its translations.', self::TEXT_DOMAIN) . '</p>';
        $output.='<table class="widefat"><tbody>';
        foreach ($llista as $bloc):
            $current = $this->metaboxRelationshipRetrieve($post->ID, $this->metaboxRelationshipFieldName($bloc['blog_id']));
            $output.= '<tr><td>';
            $output.='<label for="' . $this->metaboxRelationshipFieldName($bloc['blog_id']) . '">' . $bloc['language'] . ':</label>';
            $output.='</td><td><select name="' . $this->metaboxRelationshipFieldName($bloc['blog_id']) . '">';
            $output.=$this->metaboxRelationshipSelectBox($bloc['blog_id'], $current, $post->ID, $post->post_type);
            $output.= '</select>';
            $output.= '</td></tr>';
        endforeach;
        $output .= '</tbody></table>';
        $output .= '<p><label for="QuBicIdiomaReciprocal">' . __('Reciprocal update?', self::TEXT_DOMAIN) . '</label>';
        $output .= '<input type="checkbox" name="QuBicIdiomaReciprocal" checked="checked"/>';
        $output .= '<span class="description">' . __('If you select this option, all refered translations will be also linked to the current one. You would not need to update the relationship in other blogs.', self::TEXT_DOMAIN) . '</span></p>';
        echo $output;
    }

    /**
     * Generate field name for metabox field
     * @param integer $blog_id
     * @return string field name for data entry
     */
    function metaboxRelationshipFieldName($blog_id) {
        return self::IDIOMA_PREFIX . $blog_id;
    }

    /**
     * Generates a select with all the titles of type especified
     * @param integer $blog_id
     * @param string $current current value
     * @param string $type post type considered
     * @param string $post_id current post id
     * @return string
     */
    function metaboxRelationshipSelectBox($blogid, $current, $post_id, $type = 'post') {
        global $blog_id;
        if ($blog_id == $blogid):
            return '<option value="' . $post_id . '" selected="selected">' . __('N/A', self::TEXT_DOMAIN) . '</option>';
        endif;
        switch_to_blog($blogid);
        $llista = get_posts(
                array(
                    'numberposts' => -1,
                    'post_type' => $type,
                    'order_by' => 'post_title',
                    'order' => 'ASC'
                )
        );
        $output = '<option value="">' . __('No translation', self::TEXT_DOMAIN) . '</option>';
        foreach ($llista as $post):
            $output.='<option value="';
            $output.=$post->ID;
            $output.='"';
            $output.=selected($post->ID, $current, false);
            $output.='>';
            $output.=$post->post_title;
            $output.='</option>';
        endforeach;
        restore_current_blog();
        return $output;
    }

    /**
     * Retrieves the meta data in $camp
     * @param integer $post_id
     * @param string $camp
     * @return string
     * @since 0.3
     * @uses get_post_meta
     */
    function metaboxRelationshipRetrieve($post_id, $camp) {
        return get_post_meta($post_id, $camp, true);
    }

    /**
     * Saves relationship information
     * @param integer $post_id
     * @since 2.0
     */
    function metaboxRelationshipSave($post_id) {
        global $blog_id;
        $blocs = $this->getActiveBlogs('blog_id');
        foreach ($blocs as $bloc):
            $camp = $this->metaboxRelationshipFieldName($bloc['blog_id']);
            if (array_key_exists($camp, $_POST)):
                update_post_meta($post_id, $camp, $_POST[$camp]);
            endif;
        endforeach;
        $this->metaboxRelationshipReciprocalUpdate($post_id);
    }

    /**
     * Prepares links related to the content in other languages in a div
     * @param string class class for the div
     * @return string
     * @since 2.0
     */
    function printLinks($class = 'qibdip-idioma-post-translations') {
        global $post;
        if (is_front_page()):
            $blogs = $this->getActiveBlogs;
            $links = '';
            $blog_id = get_current_blog_id();
            foreach ($blogs as $blog):
                if ($blog['blog_id'] != $blog_id):
                    $detalls = $blog['details'];
                    $links.='<li>';
                    $links.='<a href="';
                    $links.=$detalls->siteurl;
                    $links.='" title="';
                    $links.=$detalls->blogname;
                    $links.='">';
                    $links.=$blog['language'];
                    $links.='</a>';
                    $links.='</li>';
                endif;
            endforeach;
        else:
            if (!$this->isPostTypeToTranslate($post->post_type)):
                return '';
            endif;
            $traduccions = $this->getTranslations($post->ID);
            $links = '';
            foreach ($traduccions as $traduccio) {
                $links.='<li>';
                $links.='<a href="';
                $links.=$traduccio['post_url'];
                $links.='" title="'; //TODO afegir-hi "Traducció al XXX de YYY
                $links.=$traduccio['post_title'];
                $links.='">';
                $links.=$traduccio['post_language'];
                $links.='</a>';
                $links.='</li>';
            }
        endif;
        $content = '<ul class="' . $class . '">' . $links . '</ul>';
        $content .= '<pre style="display:none">' . print_r($blogs, true) . '</pre>';
        return $content;
    }

    /**
     * Retrieves an array with all translations of post identified by post_id
     * @param integer $post_id
     * @return array
     * @since 2.0
     */
    function getTranslations($post_id) {
        $llista = $this->getOtherActiveBlogs();
        $traduccions = array();
        foreach ($llista as $bloc):
            $traduccio_id = $this->metaboxRelationshipRetrieve($post_id, $this->metaboxRelationshipFieldName($bloc['blog_id']));
            if ($traduccio_id != ''):
                $nom = $this->getLanguageName($bloc['blog_id']);
                switch_to_blog($bloc['blog_id']);
                $post = get_post($traduccio_id);
                $titol = $post->post_title;
                $url = get_permalink($traduccio_id);
                if ('publish' == $post->post_status):
                    $traduccions[] = array(
                        'post_id' => $traduccio_id,
                        'post_language' => $nom,
                        'post_title' => $titol,
                        'post_url' => $url,
                        'blog_id' => $bloc['blog_id'],
                        'post_id_original' => $post_id,
                        'camp_a_llegir' => $this->metaboxRelationshipFieldName($bloc['blog_id']),
                        'post' => $post,
                    );
                endif;
                restore_current_blog();
            endif;
        endforeach;
        return $traduccions;
    }

    /**
     * Gets language name used in the blog
     * @param integer $blog_id
     * @return string
     */
    function getLanguageName($blog_id) {
        $opcions = $this->getBlogOptions($blog_id);
        return $opcions['literal'];
    }

    /**
     * Retrieves all active blocs except the current one
     * @return array
     */
    function getOtherActiveBlogs() {
        global $blog_id;
        $tots = $this->getActiveBlogs();
        $blocs = array();
        foreach ($tots as $bloc):
            if ($bloc['blog_id'] != $blog_id):
                $blocs[] = $bloc;
            endif;
        endforeach;
        return $blocs;
    }

    /**
     * Torna els post types susceptibles de ser traduïts
     * @return array
     */
    function getPostTypesToTranslateCandidates() {
        $types = get_post_types(array('public' => 1), 'objects');
        $result = $types;
        return $result;
    }

    /**
     * Updates the relationship on the blog identified by $bloc_id with info on $_POST
     * @param integer $post_id
     */
    function metaboxRelationshipReciprocalUpdate($post_id) {
        global $blog_id;
        $post = get_post($post_id);
        if (isset($_POST['QuBicIdiomaReciprocal'])):
            $traduccions = array();
            $blocs = QuBicIdioma_obtenir_blocs($criteri = 'blog_id');
            foreach ($blocs as $b):
                $camp = $this->metaboxRelationshipFieldName($b['blog_id']);
                if (isset($_POST[$camp])):
                    $valor = $_POST[$camp];
                else:
                    $valor = '';
                endif;
                $traduccions[$b['blog_id']] = $valor;
            endforeach;
            foreach ($blocs as $b):
                if ($blog_id != $b['blog_id']):
                    switch_to_blog($b['blog_id']);
                    foreach ($traduccions as $key => $nou_post):
                        $camp = $this->metaboxRelationshipFieldName($key);
                        update_post_meta($traduccions[$b['blog_id']], $camp, $nou_post);
                    endforeach;
                    restore_current_blog();
                endif;
            endforeach;
        endif;
    }

    /**
     * Returns the links based on shortcode
     * @return string the links of translated contents
     * @since 1.2
     */
    function shortcode($attr) {
        if (isset($attr['class'])):
            $class = $attr['class'];
            $output = $this->printLinks($class);
        else:
            $output = $this->printLinks();
        endif;
        return $output;
    }

    /**
     * Creates the URL that corresponds to a blog homepage based on its domain and path info
     * @param string $domain blog's domain
     * @param string $path blog's path
     * @return string
     * @since 2.0
     */
    public static function createURL($domain, $path) {
        return '//' . $domain . $path;
    }

    public function isTranslated($id) {
        $traduccions = $this->getTranslations($id);
        return count($traduccions) > 0;
    }

}

/**
 * Text Only Widget
 * @since 0.7 
 */
class QuBicIdioma_Chooser_Text_Widget extends WP_Widget {

    /**
     * processes the widget
     */
    function QuBicIdioma_Chooser_Text_Widget() {
        $widget_ops = array(
            'classname' => 'QuBic_Idioma_Text',
            'description' => __('Shows text links to different languages homepages', DeMomentSomTresLanguage::TEXT_DOMAIN)
        );
        $this->WP_Widget('QuBic_Idioma_Text', __('Language Home Links', DeMomentSomTresLanguage::TEXT_DOMAIN), $widget_ops);
    }

    function form($instance) {
        parent::form($instance);
    }

    function update($new_instance, $old_instance) {
        parent::update($new_instance, $old_instance);
    }

    function widget($args, $instance) {
        global $blog_id;
        global $dms3Language;
        $llista = $dms3Language->getActiveBlogs();
        $output = '<div class="qibdip_Idioma_Text">';
        foreach ($llista as $linia):
            if ($linia['blog_id'] == $blog_id):
                $output_inici = '<span class="qibdip_idioma_actual">';
                $output_fi = '</span>';
            else:
                $output_inici = '<a href="' . DeMomentSomTresLanguage::createURL($linia['domain'], $linia['path']) . '">';
                $output_fi = '</a>';
            endif;
            $output.='&nbsp;';
            $output.=$output_inici;
            $output.=$linia['language'];
            $output.=$output_fi;
        endforeach;
        $output.='</div>';
        echo $output;
    }

}

class DeMomentSomTres_Post_Translations extends WP_Widget {

    function DeMomentSomTres_Post_Translations() {
        $widget_ops = array(
            'classname' => 'DeMomentSomTres_Post_Translations',
            'description' => __('Shows the translations of the main content', DeMomentSomTresLanguage::TEXT_DOMAIN)
        );
        $this->WP_Widget('DeMomentSomTres_Post_Translations', __('Language: post translations', DeMomentSomTresLanguage::TEXT_DOMAIN), $widget_ops);
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', DeMomentSomTresLanguage::TEXT_DOMAIN); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
        <p><label for="<?php echo $this->get_field_id('always'); ?>"><?php _e('Always shown:', DeMomentSomTresLanguage::TEXT_DOMAIN); ?> <input class="widefat" id="<?php echo $this->get_field_id('always'); ?>" name="<?php echo $this->get_field_name('always'); ?>" type="checkbox" <?php checked(isset($instance['always']) ? 1 : 0); ?>/></label></p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $new_instance['title'] = strip_tags($new_instance['title']);
        return $new_instance;
    }

    function widget($args, $instance) {
        global $post;
        global $dms3Language;
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        $always = isset($instance['always']);
        if ($dms3Language->isTranslated($post->ID)):
            echo $before_widget;
            if ($title)
                echo $before_title . $title . $after_title;
            echo $dms3Language->printLinks();
            echo $after_widget;
        else:
            if ($always):
                echo $before_widget;
                if ($title)
                    echo $before_title . $title . $after_title;
                echo $after_widget;
            endif;
        endif;
    }

}
?>