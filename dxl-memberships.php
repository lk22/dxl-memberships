<?php 

/**
 * Plugin Name: DXL Membership manager
 * Description: Modul til håndtering af medlemmer
 * Author: Leo Knudsen
 * Version: 1.0.0
 */

if( !defined('ABSPATH') ) {exit;}

require_once(plugin_dir_path(__FILE__) . 'vendor/autoload.php');
require_once ABSPATH . "wp-content/plugins/dxl-memberships/PhpSpreadsheet/vendor/autoload.php";

use DxlMembership\Classes\Actions\MemberAction;
use DxlMembership\Classes\Actions\MembershipAction;
use DXL\Classes\Core;

if( !class_exists('DXLMemberships') ) 
{
    /**
     * Main DXL Membership plugin class
     */
    class DXLMemberships 
    {
        /**
         * Plugin constructor
         */
        public function __construct()
        {
            $this->member = new MemberAction();
            $this->membership = new MembershipAction();
            add_action( 'admin_menu', [$this, 'registerModuleMenu']);
            add_action('admin_enqueue_scripts', [$this, 'enqueueAdminmMmberScripts']);
            add_shortcode('dxlMembershipForm', [$this, 'enqueueMembershipForm']);
            add_action('wp_before_admin_bar_render', [$this, 'registerAdminTopBarNavigation']);
            $this->validate_requirements();

            add_action( 'wp_dashboard_setup', [$this, 'register_meta_boxes']);
        }

        public function register_meta_boxes()
        {
            wp_add_dashboard_widget('dxl_latest_members_widget', esc_html__('Seneste medlemsskaber', ''), [$this, 'register_latest_members_widget']);
            wp_add_dashboard_widget('dxl_awaiting_members_widget', esc_html__('Potentielle Medlemsskaber', ''), [$this, 'register_awaiting_members_widget']);
        }

        /**
         * Validate requirements
         *
         * @return void
         */
        public function validate_requirements()
        {
            if( !version_compare(Core::DXL_CORE_VERSION, '1.0.0', '>=') )
            {
                add_action('admin_notices', [$this, 'admin_notice_minimum_dxl_core_version']);
                return false;
            }

            return true;
        }

        /**
         * add notice for minimum core version
         *
         * @return void
         */
        public function admin_notice_minimum_dxl_core_version()
        {
            if( isset($_GET["activate"]) ) unset($_GET["activate"]);

            $message = sprintf(
                esc_html__('"%1$s" require "%2$s" to be installed and activated.', 'webto-elementor'),
                '<strong>' . esc_html__('DXL Memberships', 'dxl-memberships') . '</strong>',
                '<strong>' . esc_html__('DXL Core', 'dxl-core-plugin') . '</strong>',
            );

            printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
        }

        /**
         * Enqueue all admin member scripts
         *
         * @return void
         */
        public function enqueueAdminmMmberScripts()
        {
            wp_enqueue_script('dxl-admin-member', plugins_url('dxl-memberships/src/admin/assets/js/admin-member.js'), array('jquery'));
            wp_register_style('dxl-admin-member-style', plugins_url('/dxl-memberships/src/admin/assets/css/admin-member.css', 'dxl-memberships'));
            wp_enqueue_style('dxl-admin-member-style');

            wp_localize_script('dxl-admin-member', 'dxl_member_vars', [
                'plugins' => array('members' => 'dxl-members', 'memberships' => 'dxl-memberships')
            ]);
        }

        /**
         * Enqueue all frontend member scripts
         *
         * @return void
         */
        public function enqueueFrontendMemberScripts()
        {
            wp_enqueue_script('dxl-frontend-member', plugins_url('dxl-memberships/src/frontend/assets/js/frontend-memberships.js'), array('jquery'));
            wp_localize_script('dxl-frontend-member', 'dxl_member_vars', [
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'dxl_member_nonce' => wp_create_nonce('dxl-member-nonce'),
                'action' => [
                    "dxl_add_member"
                ]
            ]);
        }

        public function registerAdminTopBarNavigation()
        {
            global $wp_admin_bar;
            $wp_admin_bar->add_menu([
                'id' => "dxl-members",
                'title' => "Medlemmer",
                "href" => admin_url("admin.php?page=dxl-members")
            ]);
        }

        /**
         * Register admin module menu
         *
         * @return void
         */
        public function registerModuleMenu()
        {
            add_menu_page(
				'Medlemmer',
				'Medlemmer',
				'manage_options',
				'dxl-members',
				array($this, 'members_manager'),
                'dashicons-admin-users',
                2
			);

            add_submenu_page(
				'dxl-members',
				'Kontingenter',
				'Kontingenter',
				'manage_options',
				'dxl-memberships',
				array($this, 'memberships_manager')
			);
        }
        
        /**
         * Registering latest members widget to show in dashboard
         *
         * @return void
         */
        public function register_latest_members_widget()
        {
            $members = $this->member->getLatest();
            require_once ABSPATH . "wp-content/plugins/dxl-memberships/src/admin/views/widget/latest.php";
        }

        /**
         * Registering awaiting memberships dashboard widget
         *
         * @return void
         */
        public function register_awaiting_members_widget()
        {
            $members = $this->member->getAwaitingMembers();
            require_once ABSPATH . "wp-content/plugins/dxl-memberships/src/admin/views/widget/awaiting.php";
        }

        /**
         * enqueue membership form 
         *
         * @return void
         */
        public function enqueueMembershipForm()
        {
            add_action('wp_enqueue_scripts', [$this, 'enqueueFrontendMemberScripts']);
            do_action('wp_enqueue_scripts');
            $this->member->renderMembershipForm();
        }

        /**
         * listen for members manager views
         *
         * @return void
         */
        public function members_manager()
        {
            $this->member->manage();
        }

        /**
         * listen for memberships manager views
         *
         * @return void
         */
        public function memberships_manager()
        {
            $this->membership->manage();
        }
    }
}

new DXLMemberships();

?>