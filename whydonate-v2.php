<?php
/*
Plugin Name:  Whydonate
Plugin URI:   https://wordpress.org/plugins/wp-whydonate/
Description:  Donatie button voor je eigen website. Zamel geld in via iDeal, Creditcard, PayPal, VISA, Sofort en Bancontact. Binnen een paar minuten opgezet en veilig!
Author:       Whydonate
Author URI:   https://profiles.wordpress.org/whydonate/
Version:      4.0.10
License:      GPL v2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.txt
*/


// In README.txt Tested up to: 6.3.1 (It means that we have to check the wordpress version before every push) 

// disable direct file access
if (!defined('ABSPATH')) {

    exit;
}

define('VERSION', '1.1');

function version_id()
{
    if (WP_DEBUG)
        return time();
    return VERSION;
}

global $wdplugin_db_version;
$wdplugin_db_version = '1.0';

global $wdplugin_fundraiser_worker_url;
$wdplugin_fundraiser_worker_url = 'https://fundraiser.whydonate.dev';

global $wdplugin_donation_worker_url;
$wdplugin_donation_worker_url = 'https://donation.whydonate.dev';

global $wdplugin_account_worker_url;
$wdplugin_account_worker_url = 'https://account.whydonate.dev';

global $wdplugin_account_worker_url;
$wdplugin_home_url = 'https://whydonate.com/';

// create db
function create_style_table()
{
    global $wpdb;
    global $wdplugin_db_version;

    // $table_name = 'wp_wdplugin_style';
    $table_name = $wpdb->prefix . "wdplugin_style";

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE " . $table_name . " (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		styleName text DEFAULT '' NOT NULL,
		oneTimeCheck int DEFAULT 1 NOT NULL,
		monthlyCheck int DEFAULT 2 NOT NULL,
		yearlyCheck int DEFAULT 3 NOT NULL,
        firstAmountCheck int DEFAULT 1 NOT NULL,
		secondAmountCheck int DEFAULT 2 NOT NULL,
		thirdAmountCheck int DEFAULT 3 NOT NULL,
        forthAmountCheck int DEFAULT 4 NOT NULL,
        firstAmount int DEFAULT 25 NOT NULL,
        secondAmount int DEFAULT 50 NOT NULL,
        thirdAmount int DEFAULT 75 NOT NULL,
        forthAmount int DEFAULT 100 NOT NULL,
        otherChecked int DEFAULT 1 NOT NULL,
        showDonateButton int DEFAULT 1 NOT NULL,
        showProgressBar int DEFAULT 2 NOT NULL,
        showRaisedAmount int DEFAULT 3 NOT NULL,
        showEndDate int DEFAULT 4 NOT NULL,
        showDonationFormOnly int DEFAULT 0 NOT NULL,
        doNotShowBox int DEFAULT 0 NOT NULL,
        colorCode varchar(8) DEFAULT '#2828d6' NOT NULL,
        font text DEFAULT '' NOT NULL,
        buttonRadius int DEFAULT 30 NOT NULL,
        donationTitle varchar(50) DEFAULT 'My Safe Donation' NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);

    add_option('wd_style_db_version', $wdplugin_db_version);
}

function create_api_table()
{
    global $wpdb;
    global $wdplugin_db_version;

    // $table_name = 'wp_wdplugin_api_key';
    $table_name = $wpdb->prefix . "wdplugin_api_key";

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE " . $table_name . " (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		apiKey text DEFAULT '' NOT NULL,
        username text DEFAULT '' NOT NULL,
        email text DEFAULT '' NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);

    add_option('wd_apikey_db_version', $wdplugin_db_version);
}

function create_config_widget_table()
{
    global $wpdb;
    global $wdplugin_db_version;

    // $table_name = 'wp_wdplugin_config_widget';
    $table_name = $wpdb->prefix . "wdplugin_config_widget";

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE " . $table_name . " (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
        styleId int DEFAULT 0 NOT NULL,
		pluginStyle text DEFAULT '' NOT NULL,
		fundraiserName text DEFAULT '' NOT NULL,
        slugName text DEFAULT '' NOT NULL,
        slugId int DEFAULT 0 NOT NULL,
        shortcode text DEFAULT '' NOT NULL,
        successUrl text DEFAULT '' NOT NULL,
        failureUrl text DEFAULT '' NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);

    add_option('wd_config_widget_db_version', $wdplugin_db_version);
}

function upgrade_database_set_transition($upgrader_object, $options)
{
    // The path to our plugin's main file
    $our_plugin = plugin_basename(__FILE__);
    // If an update has taken place and the updated type is plugins and the plugins element exists
    if ($options['action'] == 'update' && $options['type'] == 'plugin' && isset($options['plugins'])) {
        // Iterate through the plugins being updated and check if ours is there
        foreach ($options['plugins'] as $plugin) {
            if ($plugin == $our_plugin) {
                // Set a transient to record that our plugin has just been updated
                set_transient('wp_upe_updated', 1);
            }
        }
    }
}

add_action('upgrader_process_complete', 'upgrade_database_set_transition', 10, 2);

function wp_upe_display_update_notice()
{
    // Check the transient to see if we've just updated the plugin
    if (get_transient('wp_upe_updated')) {
        global $wpdb, $plugin_version;

        $table_name = $wpdb->prefix . 'wdplugin_config_widget';

        try {
            $wpdb->query(
                "ALTER TABLE $table_name
                 ADD COLUMN `successUrl` TEXT DEFAULT '' NOT NULL
                "
            );
        } catch (exception $e) {
            var_dump('successUrl column already existed');
        }


        try {
            $wpdb->query("ALTER TABLE $table_name
                 ADD COLUMN `failureUrl` TEXT DEFAULT '' NOT NULL
                ");
        } catch (exception $e) {
            var_dump('failureUrl column already existed');
        }

        try {
            $wpdb->query("ALTER TABLE $table_name
                DROP COLUMN `redirectUrl`
                ");
        } catch (exception $e) {
            var_dump('redirectUrl column already removed');
        }

        echo '<div class="notice notice-success">' . __('Thanks for updating. If you see any database error messages, please ignore them and refresh the page.', 'whydonate-v2') . '</div>';
        delete_transient('wp_upe_updated');
    }
}
add_action('admin_notices', 'wp_upe_display_update_notice');


function wpse_enqueue_datepicker()
{

    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
    // wp_enqueue_script('html2canvas', 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js');
    wp_enqueue_script('wp-styling-script', 'https://plugin.whydonate.com/wp_styling.js');
    wp_enqueue_style('shortcode_style', 'https://plugin.whydonate.com/wdplugin-style.css', false, version_id());
    // wp_enqueue_script('wp-styling-script', 'http://localhost:4200/assets/wp_styling_development.js');
    
}
add_action('init', 'wpse_enqueue_datepicker');

function remove_database_tables()
{
    global $wpdb;
    // $table_name = 'wp_wdplugin_api_key';
    $table_name = $wpdb->prefix . "wdplugin_api_key";
    $sql = "DROP TABLE IF EXISTS " . $table_name . "";
    $wpdb->query($sql);

    // $table_name = 'wp_wdplugin_config_widget';
    $table_name = $wpdb->prefix . "wdplugin_config_widget";
    $sql = "DROP TABLE IF EXISTS " . $table_name . "";
    $wpdb->query($sql);

    // $table_name = 'wp_wdplugin_style';
    $table_name = $wpdb->prefix . "wdplugin_style";
    $sql = "DROP TABLE IF EXISTS " . $table_name . "";
    $wpdb->query($sql);

    delete_option("wd_apikey_db_version");
}

function my_plugin_remove_database_and_others()
{

    // Delete installation entry from our database
    global $wpdb;
    $table_name = $wpdb->prefix . "wdplugin_api_key";

    $query = $wpdb->prepare("SELECT * FROM $table_name");
    $result = $wpdb->get_results($query, ARRAY_A);

    foreach ($result as $row) {
        $api_key = $row['apiKey'];
    }
    $site_url = get_site_url();
    $domain_part = explode("//", $site_url)[1];
    $domain = explode("/", $domain_part)[0];

    global $wdplugin_account_worker_url;
    $productionApi = $wdplugin_account_worker_url."/account/check/installations";


    $myObj = array("url" => $domain);
    $myJSON = json_encode((object) $myObj);

    $curl = curl_init($productionApi);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(
        $curl,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json',
            'API-KEY: ' . $api_key
        )
    );
    curl_setopt($curl, CURLOPT_POSTFIELDS, $myJSON);
    $result = curl_exec($curl);
    curl_close($curl);
    echo $result;
}

register_activation_hook(__FILE__, 'create_style_table');
register_activation_hook(__FILE__, 'create_api_table');
register_activation_hook(__FILE__, 'create_config_widget_table');
register_deactivation_hook(__FILE__, 'my_plugin_remove_database_and_others');

// load text domain
function myplugin_load_textdomain()
{

    load_plugin_textdomain('myplugin', false, plugin_dir_path(__FILE__) . 'languages/');
}
add_action('plugins_loaded', 'myplugin_load_textdomain');

// load default styles
function wdplugin_enqueue_scripts()
{
    wp_enqueue_style('myplugin', plugin_dir_url(__FILE__) . '/admin/css/wdplugin-style.css', false, version_id());

    wp_enqueue_style('material-icon', "https://fonts.googleapis.com/icon?family=Material+Icons", false);

    wp_enqueue_style('google-fonts', "https://fonts.googleapis.com/css?family=Abel|Abril+Fatface|Acme|Alegreya|Alegreya+Sans|Anton|Archivo|Archivo+Black|Archivo+Narrow|Arimo|Arvo|Asap|Asap+Condensed|Bitter|Bowlby+One+SC|Bree+Serif|Cabin|Cairo|Catamaran|Crete+Round|Crimson+Text|Cuprum|Dancing+Script|Dosis|Droid+Sans|Droid+Serif|EB+Garamond|Exo|Exo+2|Faustina|Fira+Sans|Fjalla+One|Francois+One|Gloria+Hallelujah|Hind|Inconsolata|Indie+Flower|Josefin+Sans|Julee|Karla|Lato|Libre+Baskerville|Libre+Franklin|Lobster|Lora|Mada|Manuale|Maven+Pro|Merriweather|Merriweather+Sans|Montserrat|Montserrat+Subrayada|Mukta+Vaani|Muli|Noto+Sans|Noto+Serif|Nunito|Open+Sans|Open+Sans+Condensed:300|Oswald|Oxygen|PT+Sans|PT+Sans+Caption|PT+Sans+Narrow|PT+Serif|Pacifico|Passion+One|Pathway+Gothic+One|Play|Playfair+Display|Poppins|Questrial|Quicksand|Raleway|Ranga|Roboto|Roboto+Condensed|Roboto+Mono|Roboto+Slab|Ropa+Sans|Rubik|Saira|Saira+Condensed|Saira+Extra+Condensed|Saira+Semi+Condensed|Sedgwick+Ave|Sedgwick+Ave+Display|Shadows+Into+Light|Signika|Slabo+27px|Source+Code+Pro|Source+Sans+Pro|Spectral|Titillium+Web|Ubuntu|Ubuntu+Condensed|Varela+Round|Vollkorn|Work+Sans|Yanone+Kaffeesatz|Zilla+Slab|Zilla+Slab+Highlight", false);

    wp_enqueue_script('script', plugin_dir_url(__FILE__) . '/admin/js/wdplugin.js', array('jquery'), version_id(), true);

    $arr = array(
        'ajaxurl' => plugin_dir_url(__DIR__) . 'whydonate-v2/admin/setup/delete.php',
    );
    wp_localize_script('main-ajax', 'obj', $arr);
    wp_localize_script('jquery', 'ajaxurl', admin_url('admin-ajax.php'));

    //NONCES FOR SECURITY	
    $nonce = wp_create_nonce('check_api_key');
    $nonce_api_key = wp_create_nonce('api_key');
    $nonce_plugin_remove_button = wp_create_nonce('my_action');
    $nonce_remove_fundraiser_button = wp_create_nonce('remove_widget_action');
    $nonce_create_fundraiser = wp_create_nonce('create_fundraiser');
    $nonce_edit_fundraiser = wp_create_nonce('edit_fundraiser');
    $nonce_update_config = wp_create_nonce('update_config_widget');
    $nonce_edit_config = wp_create_nonce('edit_config_widget');
    $nonce_set_new_style = wp_create_nonce('set_new_style');
    $nonce_transfer_styling = wp_create_nonce('transfer_styling');
    $nonce_check_database = wp_create_nonce('check_database');
    $nonce_fundraiser_shortcodes_array = wp_create_nonce('fundraiser_shortcodes_array');

    $localized_array = array(
        'nonce' => $nonce,
        'nonce_api_key' => $nonce_api_key,
        'nonce_plugin_remove_button' => $nonce_plugin_remove_button,
        'nonce_remove_fundraiser_button' => $nonce_remove_fundraiser_button,
        'nonce_create_fundraiser' => $nonce_create_fundraiser,
        'nonce_edit_fundraiser' => $nonce_edit_fundraiser,
        'nonce_update_config' => $nonce_update_config,
        'nonce_edit_config' => $nonce_edit_config,
        'nonce_set_new_style' => $nonce_set_new_style,
        'nonce_transfer_styling' => $nonce_transfer_styling,
        'nonce_check_database' => $nonce_check_database,
        'nonce_fundraiser_shortcodes_array' => $nonce_fundraiser_shortcodes_array
    );
    wp_localize_script('script', 'my_script_vars', $localized_array);
    // wp_localize_script('script', 'my_script_vars_api_key', array('nonce_api_key' => $nonce_api_key,));
}

add_action('plugins_loaded', 'whydonate_load_text_domain');
function whydonate_load_text_domain()
{
    load_plugin_textdomain('whydonate-v2', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

add_action('admin_enqueue_scripts', 'wdplugin_enqueue_scripts', "", false, false);

add_filter('admin_footer_text', '__return_empty_string', 11);
add_filter('update_footer', '__return_empty_string', 11);

// include plugin dependencies: admin only
if (is_admin()) {

    require_once plugin_dir_path(__FILE__) . 'admin/admin-menu.php';
    require_once plugin_dir_path(__FILE__) . 'admin/settings-page.php';
}

require_once plugin_dir_path(__FILE__) . 'includes/core-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcode-app-view.php';


// default plugin options
function myplugin_options_default()
{

    return array(
        'custom_url' => 'https://wordpress.org/',
        'custom_title' => esc_html__('Powered by WordPress', 'myplugin'),
        'custom_style' => 'disable',
        'custom_message' => '<p class="custom-message">' . esc_html__('My custom message', 'myplugin') . '</p>',
        'custom_footer' => esc_html__('Special message for users', 'myplugin'),
        'custom_toolbar' => false,
        'custom_scheme' => 'default',
    );
}

function my_plugin_action_links($links)
{
    $title = __('Account Setup', 'whydonate-v2');
    $links = array_merge(
        array(
            '<a href="' . esc_url(admin_url('?page=myplugin')) . '">' . $title . '</a>'
        ), $links);
    return $links;
}
add_action('plugin_action_links_' . plugin_basename(__FILE__), 'my_plugin_action_links');

function remove_script_version( $src ) {
    if ( strpos( $src, 'wp_styling.js' ) !== false ) {
        $src = remove_query_arg( 'ver', $src );
    }
    return $src;
}
add_filter( 'script_loader_src', 'remove_script_version', 15, 1 );
add_filter( 'style_loader_src', 'remove_script_version', 15, 1 );
