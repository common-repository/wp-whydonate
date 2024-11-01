<?php // MyPlugin - Core Functionality

// disable direct file access

if (!defined('ABSPATH')) {

    exit;
}

add_shortcode('whydonate', 'shortcode_func');

function fetchDataWithId()
{
    //print_r("hello Shuvo " . $_POST["fundraiser-id-input"]);

    $GLOBALS['dataVariables']['whydonate_id'] = $_POST["fundraiser-id-input"];
    if ($GLOBALS['dataVariables']['whydonate_id'] == null) {
        return;
    } else {
        $GLOBALS['dataVariables']['fundraiser_list'] = ["Option one", "Option Two", "Option Three"];
        // foreach ($GLOBALS['fundraiser_list'] as $item) {
        //     echo $item;
        // }
    }
}


function remove_row()
{
    $id = isset($_POST['id']) ? absint($_POST['id']) : 0; // Sanitize the 'id' input as an integer.

    // Check if the 'id' is a valid positive integer.
    if (!$id || $id <= 0) {
        wp_send_json_error('Invalid ID.');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . "wdplugin_style";
    $response = $wpdb->delete($table_name, array('id' => $id), array('%d'));

    if ($response === false) {
        wp_send_json_error('Error deleting row.');
    }

    wp_die();
}

add_action('wp_ajax_remove_row', 'remove_row');
add_action('wp_ajax_nopriv_remove_row', 'remove_row');


function showData($values)
{
?>
    <div style="margin-left: 500px; margin-top: 20px;">
        <ul><?php foreach ($values as $value) {
                echo '<li>' . esc_html($value) . '</li>'; // Escaping $value
            }
            ?></ul>
    </div>
<?php
}

add_action('wp_ajax_check_api_key', 'check_api_key');

function check_api_key()
{

    // Verify the nonce.    
    $nonce = $_POST['nonce'];    
    if (!wp_verify_nonce($nonce, 'check_api_key')) {    
        // wp_send_json_error('Invalid or expired nonce.'); // Handle the invalid nonce.    
        echo 'api key not verified';    
        exit;    
    }
    // try {

    else if ((isset($_POST['payload'])) && isset($_POST['api_key'])) {
        $data = $_POST['payload'];
        $apiKey = $_POST['api_key'];

        // $url = 'https://whydonate-development.appspot.com/api/v1/account/apikey/user/?client=whydonate-staging';
        // $url = 'https://whydonate-production-api.appspot.com/api/v1/account/apikey/user/?client=whydonate-production';
        global $wdplugin_account_worker_url;
        $url = $wdplugin_account_worker_url."/account/wordpress/apikey";

        // $myJSON = json_encode($data);

        // $curl = curl_init($url);
        // curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        // curl_setopt($curl, CURLOPT_POSTFIELDS, $myJSON);
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt(
        //     $curl,
        //     CURLOPT_HTTPHEADER,
        //     array(
        //         'Content-Type: application/json',
        //         'API-KEY: ' . $apiKey
        //     )
        // );

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $curl, CURLOPT_HTTPHEADER, array(
            'API-KEY: ' . $apiKey
            )
        );

        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;
    }
    // } catch (\Throwable $exception) {
    //     Sentry\captureException($exception);
    // }
}

add_action('wp_ajax_api_key', 'api_key');

function api_key()
{
    //Verify Nonce
    $nonce_api_key = $_POST['nonce_api_key'];	
    // $nonce = $_POST['nonce'];	
    if(!wp_verify_nonce($nonce_api_key,'api_key')){	
        echo 'wrong api key';	
        exit;	
    }else{
    // try {
    $api_key = '';
    $username = '';
    $email = '';

    if (isset($_POST['api_key'])) {
        $api_key = $_POST['api_key'];
    }

    if (isset($_POST['username'])) {
        $username = $_POST['username'];
    }

    if (isset($_POST['email'])) {
        $email = $_POST['email'];
    }



    updateApiKey($api_key, $username, $email);
    }
    wp_die();
    // } catch (\Throwable $exception) {
    //     Sentry\captureException($exception);
    // }
}

function updateApiKey($key, $username, $email)
{
    // try {
    global $wpdb;
    // $table_name = "wp_wdplugin_api_key";
    // $table_name = $wpdb->prefix . 'wdplugin_api_key';
    $table_name = $wpdb->prefix . "wdplugin_api_key";
    $query = $wpdb->prepare("SELECT id FROM $table_name WHERE id IS NOT NULL");
    $result = $wpdb->get_results($query);
    if (count($result) == 0) {
        // echo "not filled";
        if (!empty($key)) {

            $success = $wpdb->insert($table_name, array(
                "apiKey" => $key,
                "username" => $username,
                "email" => $email
            ), array('%s', '%s', '%s'));
            //echo $success;
            if ($success) {
                // var_dump($success);
                echo 'Insert success';
            } else {
                echo 'Insert not success';
            }
        }
    } else {
        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE apiKey = '$key'"));

        if ($count > 0) {
            echo 'Update success';
        } else {

            // echo "filled";
            $success = $wpdb->update(
                $table_name,
                array(
                    'apiKey' => $key,
                    'username' => $username,
                    'email' => $email
                ),
                array('ID' => 1),
                array(
                    '%s',
                    '%s',
                    '%s'
                ),
                array('%s')
            );

            if ($success) {
                // var_dump($success);
                echo 'Update success';
            } else {
                echo 'Update not success';
            }
        }
    }
    wp_die();
    // } catch (\Throwable $exception) {
    //     Sentry\captureException($exception);
    // }

    //  }
}

add_action('wp_ajax_make_donation', 'make_donation');
add_action('wp_ajax_nopriv_make_donation', 'make_donation');

function make_donation()
{
    // try {
    if (isset($_POST['info'])) {
        $data = $_POST['info'];

        // $url = 'https://whydonate-development.appspot.com/api/v1/donation/order/?client=whydonate-staging';
        // $url = 'https://whydonate-production-api.appspot.com/api/v1/donation/order/?client=whydonate-production';
        // $url = 'https://donation-staging.whydonate.dev/donation/order';
        global $wdplugin_donation_worker_url;
        $url = $wdplugin_donation_worker_url.'/donation/order';

        $myJSON = json_encode((object) $data, JSON_NUMERIC_CHECK);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $myJSON);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json'
            )
        );

        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;
    }
    // } catch (\Throwable $exception) {
    //     Sentry\captureException($exception);
    // }
}

add_action('wp_ajax_transfer_styling', 'transfer_styling');

function transfer_styling()
{
    $nonce_transfer_styling = $_POST['nonce_transfer_styling'];	
    $data = $_POST['row'];		

    if(!wp_verify_nonce($nonce_transfer_styling,'transfer_styling')){	
        echo 'Transfer styling action is not verified';	
        exit;	
    }else{
        global $wdplugin_fundraiser_worker_url;
        $wdplugin_fundraiser_worker_url;
        $url = $wdplugin_fundraiser_worker_url.'/fundraiser/wordpress/transfer/stylings';
    
        global $wpdb;
        $table_name = $wpdb->prefix . 'wdplugin_api_key';
        $query = $wpdb->prepare("SELECT id FROM $table_name WHERE id IS NOT NULL");
        $result = $wpdb->get_results($query);
    
        if (count($result) > 0) {
            global $wpdb;
            $query = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", 1);
            $retrieve_data = $wpdb->get_results($query);
    
            $data_json = json_encode($data);
    
            if (!empty($retrieve_data)) {
                $apiKey = '';
                foreach ($retrieve_data as $retrieved_data) {
                    $apiKey = $retrieved_data->apiKey;
                }
    
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'API-KEY: ' . $apiKey,
                    'Content-Type: application/json',
                ));
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
                $response = curl_exec($curl);
                curl_close($curl);
                echo $response;
        } else {
            echo 'Please set an api key first';
        }
    }
}
 

    wp_die();
}


add_action('wp_ajax_check_database', 'check_database');

function check_database()
{
    $nonce_check_database = $_POST['nonce_check_database'];	
    // $nonce = $_POST['nonce'];	
    if(!wp_verify_nonce($nonce_check_database,'check_database')){	
        echo 'database check action is not verified';	
        exit;	
    }	
    else	
    {
    // try {
    global $wpdb;
    $api_table = $wpdb->prefix . 'wdplugin_api_key';
    // $api_table = 'wp_wdplugin_api_key';
    $pluginStyle =  $wpdb->prefix . 'wdplugin_style';
    // $pluginStyle = 'wp_wdplugin_style';
    $configWidget = $wpdb->prefix . 'wdplugin_config_widget';
    // $configWidget = 'wp_wdplugin_config_widget';

    if ($wpdb->get_var("SHOW TABLES LIKE '$api_table'")) {
        if ($wpdb->get_var("SHOW TABLES LIKE '$pluginStyle'")) {
            if ($wpdb->get_var("SHOW TABLES LIKE '$configWidget'")) {
                //echo 'all tables exist';
                return true;
            } else {
                //echo 'config widget table not exist';
                return false;
            }
        } else {
            //echo 'plugin table not exist';
            return false;
        }
    } else {
        //echo 'api key table not exist!';
        return false;
    }
}
    wp_die();
    // } catch (\Throwable $exception) {
    //     Sentry\captureException($exception);
    // }
}

add_action('wp_ajax_my_action', 'my_action');

function my_action()
{
    // global $wpdb; // this is how you get access to the database

    // $whatever = intval($_POST['whatever']);

    // $whatever += 10;

    // echo $whatever . "adsfjashfkjas fahsdjfhas fhasfhashf asdkfhkasdhfkashflkashf a";

    // wp_die(); // this is required to terminate immediately and return a proper response

    $id = intval($_POST['id']);
    $nonce_plugin_remove_button = $_POST['nonce_plugin_remove_button'];	
    // $nonce = $_POST['nonce'];	
    if(!wp_verify_nonce($nonce_plugin_remove_button,'my_action')){	
        echo 'wrong';	
        exit;	
    }else{


    // try {
    global $wpdb;
    $style_table = $wpdb->prefix . 'wdplugin_style';
    $config_widget_table = $wpdb->prefix . 'wdplugin_config_widget';

    $reponse = $wpdb->delete($style_table, array('id' => $id), array('%d'));

    $success = $wpdb->update(
        $config_widget_table,
        array(
            'pluginStyle' => 'Default'
        ),
        array(
            'styleId' => $id
        ),
        array(
            '%s'
        ),
        array(
            '%d'
        )
    );

    if ($success) {
        echo json_encode(array('status' => $reponse));
    } else {
        echo 'Update not success';
    }

    #echo 'Delete Confirmed!';
    }
    wp_die();
    // } catch (\Throwable $exception) {
    //     Sentry\captureException($exception);
    // }
}

add_action('wp_ajax_remove_widget_action', 'remove_widget_action');

function remove_widget_action()
{

    $id = $_POST['id'];
    // echo $id;
    $nonce_remove_fundraiser_button = $_POST['nonce_remove_fundraiser_button'];	
    // $nonce = $_POST['nonce'];	
    if(!wp_verify_nonce($nonce_remove_fundraiser_button,'remove_widget_action')){	
        echo 'wrong';	
        exit;	
    }else{
    // try {
    global $wpdb;
    $config_widget_table = $wpdb->prefix . 'wdplugin_config_widget';
    $reponse = $wpdb->delete($config_widget_table, array('shortcode' => $id), array('%d'));

    
    global $wpdb;
    $table_name = $wpdb->prefix . 'wdplugin_api_key';
    $query = $wpdb->prepare("SELECT id FROM $table_name WHERE id IS NOT NULL");
    $result = $wpdb->get_results($query);
    $data = array("shortcode" => $id);
    
    // Added to remove from both wp plugin and backend
    if (count($result) > 0) {
        global $wpdb;
        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", 1);
        $retrieve_data = $wpdb->get_results($query);
        
        $data_json = json_encode($data);

        global $wdplugin_fundraiser_worker_url;
        $wdplugin_fundraiser_worker_url;
        $url = $wdplugin_fundraiser_worker_url.'/fundraiser/user/style';

        if (!empty($retrieve_data)) {
            $apiKey = '';
            foreach ($retrieve_data as $retrieved_data) {
                $apiKey = $retrieved_data->apiKey;
            }

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'API-KEY: ' . $apiKey,
                'Content-Type: application/json',
            ));
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
            $response = curl_exec($curl);
            curl_close($curl);
            echo $response;
        } else {
            echo 'Please set an api key first';
        }
    }
    
    echo json_encode(array('status' => $reponse));
    // echo json_encode(array('SHORTCODE' => $_POST['id'], 'intval SHORTCODE' => intval($_POST['id'])));

    #echo 'Delete Confirmed!';
    }
    wp_die();
    // } catch (\Throwable $exception) {
    //     Sentry\captureException($exception);
    // }
}

add_action('wp_ajax_fundraiser_shortcodes_array', 'fundraiser_shortcodes_array');

function fundraiser_shortcodes_array()
{
    // Verify the nonce.	
    $nonce_fundraiser_shortcodes_array = $_POST['nonce_fundraiser_shortcodes_array'];	
    if (!wp_verify_nonce($nonce_fundraiser_shortcodes_array, 'fundraiser_shortcodes_array')) {	
        // wp_send_json_error('Invalid or expired nonce.'); // Handle the invalid nonce.	
        echo 'api key not verified';	
        exit;	
    }
    // try {

    else {
        global $wpdb;
        $table_name = $wpdb->prefix . "wdplugin_api_key";
        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d" ,1);
        $retrieve_data = $wpdb->get_results($query);

        $apiKey = '';
        foreach ($retrieve_data as $retrieved_data) {
            $apiKey = $retrieved_data->apiKey;
        }
        global $wdplugin_fundraiser_worker_url;
        $wdplugin_fundraiser_worker_url;
        $url = $wdplugin_fundraiser_worker_url.'/fundraiser/styles/shortcodes';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'API-KEY: ' . $apiKey
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;
    }
    wp_die();
    
}

