<?php // MyPlugin - Admin Menu

// disable direct file access
if (!defined('ABSPATH')) {

    exit;
}

// add top-level administrative menu
function myplugin_add_toplevel_menu()
{

    add_menu_page(
        esc_html__('Whydonate Plugin', 'myplugin'),
        esc_html__('Whydonate', 'myplugin'),
        'manage_options',
        'myplugin',
        'wdplugin_settings',
        'https://whydonate.com/cdn-cgi/imagedelivery/_0vgnXOEIHPwLg2E52a7gg/shared/whydonate_icon/w=10',
        100,
        array( 'width' => 16 ) 
    );
    

    add_submenu_page(
        'myplugin',
        __('Account Setup', 'myplugin'),
        __('Account Setup', 'whydonate-v2'),
        'manage_options',
        'myplugin',
        'wdplugin_settings'
    );

    global $wpdb;
    $table_name = $wpdb->prefix . "wdplugin_api_key";

    $query = $wpdb->prepare("SELECT id from $table_name WHERE `id` IS NOT NULL");
    $result = $wpdb->get_results($query);

    if (count($result) > 0) {
        global $wpdb;
        // $table_name = "wp_wdplugin_api_key";
        // $table_name = $wpdb->prefix . "wdplugin_api_key";
        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", 1);
        $retrieve_data = $wpdb->get_results($query);

        if (!empty($retrieve_data)) {
            $apiKey = '';
            foreach ($retrieve_data as $retrieved_data) {
                $apiKey = $retrieved_data->apiKey;
            }

            if (!empty($apiKey)) {

                add_submenu_page(
                    'myplugin',
                    __('Widgets', 'myplugin'),
                    __('Widgets', 'whydonate-v2'),
                    'manage_options',
                    'whydonate-widget-list',
                    'wdplugin_widget_list'
                );
            }
        }
    }
}

add_action('admin_menu', 'myplugin_add_toplevel_menu');
// add_action('admin_init', 'register_mysettings');
