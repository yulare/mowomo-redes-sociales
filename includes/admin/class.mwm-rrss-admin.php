<?php
/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt.
 */

/**
 * Detects if the plugin has been entered directly.
 *
 * @since 1.3.0
 */
if (!defined('ABSPATH') || !defined('MWM_RRSS_VERSION')) {
    exit; // Exit if accessed directly.
}

/**
 * Check if exists the class 'mwm_rrss_admin'.
 *
 * @since 1.3.0
 */
if (!class_exists('mwm_rrss_admin')) {
    /**
     * Implements the mwm_rrss_admins class.
     * 
     * This is the class that controls the entire plugin.
     *
     * @since 1.3.0
     */
    class mwm_rrss_admin
    {
        /**
         * Single instance of the class.
         *
         * @var \mwm_rrss_admin
         *
         * @since 1.3.0
         */
        protected static $instance;

        /**
         * Returns single instance of the class.
         *
         * @since 1.3.0
         * 
         * @return \mwm_rrss_admin
         */
        public static function get_instance()
        {
            if (is_null(self::$instance)) :
                self::$instance = new self();
            endif;

            return self::$instance;
        }

        /**
         * Constructor.
         *
         * Initialice plugin and registers actions and filters to be used.
         *
         * @since 1.3.0
         * 
         * @return \mwm_rrss_admin
         */
        public function __construct()
        {
            // Check if the plugin has been updated
            $this->check_version();

            // Adding scripts
            add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

            // Initializing plugin information
            add_action('admin_init', array($this, 'init') );

            // Showing admin page
            add_action('admin_menu', array($this, 'show_page'));
            add_filter('admin_footer_text', array($this, 'admin_footer'));
        }

        /**
         * Check if the plugin has been updated and update the plugin information then.
         *
         * @since 1.3.1
         * 
         * @return void
         */
        public function check_version() {
            if (MWM_RRSS_VERSION !== get_option(MWM_RRSS_SLUG.'-version')) {
                update_option(MWM_RRSS_SLUG.'-version', MWM_RRSS_VERSION);

                if (get_option('mwm_rrss_actives') !== false) {
                    update_option(MWM_RRSS_SLUG.'-actives', get_option('mwm_rrss_actives'));
                }

                if (get_option('mwm_rrss_posicion') !== false) {
                    update_option(MWM_RRSS_SLUG.'-posicion', get_option('mwm_rrss_posicion'));
                }

                if (get_option('mwm_rrss_twitter') !== false) {
                    update_option(MWM_RRSS_SLUG.'-twitter', get_option('mwm_rrss_twitter'));
                }
            }
        }

        /**
         * Enqueue scripts and styles.
         *
         * @since 1.3.0
         * 
         * @return void
         */
        public function enqueue_scripts()
        {
            // Enqueuing scripts in the admin page
            if (isset($_GET['page']) && $_GET['page'] == MWM_RRSS_SLUG) {
                wp_register_script('mwm_rrss_admin_scripts', MWM_RRSS_ASS.'js/admin_scripts.js', array('jquery'), '1.0.0', true);
                wp_register_style('mwm_rrss_admin_styles', MWM_RRSS_ASS.'css/admin_styles.min.css', array());
                wp_enqueue_script('mwm_rrss_admin_scripts');
                wp_enqueue_style('mwm_rrss_admin_styles');

                // Adding info to scripts
                wp_localize_script( 'mwm_rrss_admin_scripts', 'mwm_rrss_admin_data', array(
                    'ajax_url' => admin_url( 'admin-ajax.php' )
                ));
            }
        }

        /**
         * Register settings for the plugin.
         *
         * @since 1.3.0
         * 
         * @return void
         */
        public function init()
        {
            register_setting(MWM_RRSS_SLUG.'-options', MWM_RRSS_SLUG.'-init');
            register_setting(MWM_RRSS_SLUG.'-options', MWM_RRSS_SLUG.'-actives');
            register_setting(MWM_RRSS_SLUG.'-options', MWM_RRSS_SLUG.'-posicion');
            register_setting(MWM_RRSS_SLUG.'-options', MWM_RRSS_SLUG.'-twitter');
            // register_setting(MWM_RRSS_SLUG.'-options', MWM_RRSS_SLUG.'-appearance');
            // register_setting(MWM_RRSS_SLUG.'-options', MWM_RRSS_SLUG.'-border-type');
            // register_setting(MWM_RRSS_SLUG.'-options', MWM_RRSS_SLUG.'-size');
            // register_setting(MWM_RRSS_SLUG.'-options', MWM_RRSS_SLUG.'-alignment');
            // register_setting(MWM_RRSS_SLUG.'-options', MWM_RRSS_SLUG.'-orientation');

            // $update_message = 'Mensaje de actualización';
            // $update_url = 'https://www.mowomo.com/';
            // $plugin_data = array(
            //     MWM_RRSS_SLUG => array(
            //         'name' => __('mowomo Redes Sociales', MWM_RRSS_SLUG),
            //         'update_message' => __($update_message, MWM_RRSS_SLUG), 
            //         'update_url' => $update_url,
            //         'pro' => MWM_RRSS_PRO
            //     )
            // );
            // if (!get_option('mwm-plugins')) {
            //     update_option('mwm-plugins', $plugin_data);
            // } else {
            //     $plugins = get_option('mwm-plugins');
            //     $plugins = array_merge($plugins, $plugin_data);
            //     update_option('mwm-plugins', $plugins);
            // }

            // $plugin_notices = mwm_plugin_notices();
            // $plugin_to_notice = array();
            
            // if (get_option(MWM_RRSS_SLUG.'-twitter') == "") {
            //     $plugin_to_notice = array_merge($plugin_to_notice, array(
            //         MWM_RRSS_SLUG.'-twitter' => array(
            //             'name' => __('Twitter profile', MWM_RRSS_SLUG),
            //             'message' => __('There is no twitter profile configured', MWM_RRSS_SLUG), 
            //             'slug' => MWM_RRSS_SLUG,
            //         )
            //     ));
            // }

            // update_option('mwm-plugin-notices', array_merge($plugin_notices, $plugin_to_notice));
        }

        /**
         * Add the plugin page in the WordPress administrator.
         *
         * @since 1.3.0
         * 
         * @return void
         */
        public function show_page()
        {
            add_menu_page( 'mowomo', __('mowomo Redes Sociales', MWM_RRSS_SLUG), 'manage_options', MWM_RRSS_SLUG, array($this, 'get_page'), MWM_FRA_ASS.'images/logo/logo-mowomo-white.svg' );
        }

        /**
         * Load the administration page template.
         *
         * @since 1.3.0
         * 
         * @return void
         */
        public function get_page()
        {
            // Configure the administrator page
            $admin_config = array(
                'title' => __('mowomo Redes Sociales', MWM_RRSS_SLUG),
                'page_slug' => MWM_RRSS_SLUG,
                'tabs' => array(
                    __('Configuración general') => array('admin/admin', 'general-configuration', array())
                    // __('Configuración Premium') => array('admin/admin', 'premium-configuration', array())
                ),
                'plugin_slug' => MWM_RRSS_SLUG
            );
            
            set_query_var( 'admin_config', $admin_config );

            // Load the base template
            mwm_template_admin();
        }

        /**
         * mowomo-redessociales
         *
         * @since      1.0.0
         *
         * Reemplaza el footer de WordPress en la pagina de mowomo RRSS
         *
        */
        public function admin_footer($footer_text)
        {
            if ( isset($_GET['page']) && $_GET['page'] == MWM_RRSS_SLUG ) :
                $footer_text = __( 'Thanks for using mowomo Redes Sociales, plugin made by ', MWM_RRSS_SLUG) . '<a href="https://mowomo.com" target="_blank" rel="nofollow">' . __('mowomo team', MWM_RRSS_SLUG). '</a>.';
            endif;
            return $footer_text;
        }

    }
}

/**
 * Unique access to instance of mwm_rrss_admin class.
 * 
 * @since 1.3.0
 *
 * @return \mwm_rrss_admin
 */
function mwm_rrss_admin()
{
    return mwm_rrss_admin::get_instance();
}
