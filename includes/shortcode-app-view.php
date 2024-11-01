<?php

function enqueue_styles()
{

    wp_enqueue_script('jquery');
    
    wp_enqueue_style('shortcode_style', 'https://plugin.whydonate.com/wdplugin-style.css', false, version_id());
    
    wp_enqueue_script('wp-styling-script', 'https://plugin.whydonate.com/wp_styling.js');
    // wp_enqueue_script('wp-styling-script', 'http://localhost:4200/assets/wp_styling_development.js');
    
    wp_localize_script(
        'shortcode_script',
        'my_ajax_object',
        array('ajax_url' => admin_url('admin-ajax.php'))
    );


}

function shortcode_func($atts)
{
    $locale = get_locale();

    // Debugging: Log the locale to verify it's being fetched
    error_log('Locale: ' . $locale);

    ob_start();
    enqueue_styles();
    $a = shortcode_atts(array(
        'id' => $atts['id'],
    ), $atts);
    // echo $a['id'] . '<br>';
    $id = $a['id'];
    global $wpdb;
    //$widget_table = "wp_wdplugin_config_widget";
    $widget_table = $wpdb->prefix . "wdplugin_config_widget";
    //$styles_table = "wp_wdplugin_style";
    $styles_table = $wpdb->prefix . "wdplugin_style";
    $query = $wpdb->prepare("SELECT * FROM $widget_table WHERE shortcode = %s",$id);
    $retrieve_data = $wpdb->get_results($query);
    
    $fundraiserName = '';
    $styleId = 0;
    $pluginStyle = '';
    $colorCode = '';
    $showDonateButton = 0;
    $showProgressBar = 0;
    $showRaisedAmount = 0;
    $showEndDate = 0;
    $showDonationFormOnly = 0;
    $doNotShowBox = 0;
    $oneTimeCheck = 0;
    $monthlyCheck = 0;
    $yearlyCheck = 0;
    $firstAmountCheck = 0;
    $secondAmountCheck = 0;
    $thirdAmountCheck = 0;
    $forthAmountCheck = 0;
    $otherChecked = 0;
    $firstAmount = 0;
    $secondAmount = 0;
    $thirdAmount = 0;
    $forthAmount = 0;
    $slug = '';
    $font = '';
    $flocalId = 0;
    $progress_bar = 0.0;
    $progress_bar_width = 0.0;
    $achived_per = 0.0;
    $data = array();
    $appearanceWindowHeight = 230;
    $selectInterval = 0;
    $selectAmount = 0;
    $redirectUrl = '';
    $is_opened = true;
    $donationTitle = '';
    $elapsed = '';
    $fundraiserTitle = '';
    $successUrl = '';
    $failureUrl = '';
    $buttonRadius = 30;
    $tip_enabled = true;
    $language_code = ''; //
    $stripe_status = true;	
    $widget_data_avaliable = false;

    if (!empty($retrieve_data)) {
        $widget_data_avaliable = true;
        foreach ($retrieve_data as $retrieved_data) {
            $fundraiserName = $retrieved_data->fundraiserName;
            $pluginStyle = $retrieved_data->pluginStyle;
            $slug = $retrieved_data->slugName;
            $flocalId = $retrieved_data->slugId;
            $styleId = $retrieved_data->styleId;
            $successUrl = $retrieved_data->successUrl;
            $failureUrl = $retrieved_data->failureUrl;
        }
    }

    if ($pluginStyle == 'Default' || $pluginStyle == 'Standaard' || $pluginStyle == 'Selecteer een stijl') {
        $font = '';
        $colorCode = '#2828d6';
        $showDonateButton = 1;
        $showProgressBar = 2;
        $showRaisedAmount = 3;
        $showEndDate = 4;
        $showDonationFormOnly = 0;
        $doNotShowBox = 0;
        $oneTimeCheck = 1;
        $monthlyCheck = 2;
        $yearlyCheck = 3;
        $firstAmountCheck = 1;
        $secondAmountCheck = 2;
        $thirdAmountCheck = 3;
        $forthAmountCheck = 4;
        $otherChecked = 1;
        $firstAmount = 25;
        $secondAmount = 50;
        $thirdAmount = 75;
        $forthAmount = 100;
        $donationTitle = 'My Safe Donation';
    } else {
        // var_dump("Test");
        $query = $wpdb->prepare("SELECT * FROM $styles_table WHERE id = %s", $styleId);
        $retrieve_styles = $wpdb->get_results($query);
        // var_dump($styleId);
        // print_r($retrieve_styles);

        if (!empty($retrieve_styles)) {
            // var_dump($retrieve_styles);
            foreach ($retrieve_styles as $style_data) {
                $font = $style_data->font;
                $colorCode = $style_data->colorCode;
                $showDonateButton = $style_data->showDonateButton;
                $showProgressBar = $style_data->showProgressBar;
                $showRaisedAmount = $style_data->showRaisedAmount;
                $showEndDate = $style_data->showEndDate;
                $showDonationFormOnly = $style_data->showDonationFormOnly;
                $doNotShowBox = $style_data->doNotShowBox;
                $oneTimeCheck = $style_data->oneTimeCheck;
                $monthlyCheck = $style_data->monthlyCheck;
                $yearlyCheck = $style_data->yearlyCheck;
                $firstAmountCheck = $style_data->firstAmountCheck;
                $secondAmountCheck = $style_data->secondAmountCheck;
                $thirdAmountCheck = $style_data->thirdAmountCheck;
                $forthAmountCheck = $style_data->forthAmountCheck;
                $otherChecked = $style_data->otherChecked;
                $firstAmount = $style_data->firstAmount;
                $secondAmount = $style_data->secondAmount;
                $thirdAmount = $style_data->thirdAmount;
                $forthAmount = $style_data->forthAmount;
                $donationTitle = $style_data->donationTitle;
                $buttonRadius = $style_data->buttonRadius;
            }
            // var_dump($buttonRadius);
            // var_dump($retrieve_styles);
            // var_dump($showDonateButton);
            // var_dump($showProgressBar);
            // var_dump($showRaisedAmount);
        }
    }

    if ($oneTimeCheck != 0) {
        $selectInterval = 1;
    } else if ($monthlyCheck != 0) {
        $selectInterval = 2;
    } else {
        $selectInterval = 3;
    }

    if ($firstAmountCheck != 0) {
        $selectAmount = 1;
    } else if ($secondAmountCheck != 0) {
        $selectAmount = 2;
    } else if ($thirdAmountCheck != 0) {
        $selectAmount = 3;
    } else if ($forthAmountCheck != 0) {
        $selectAmount = 4;
    } else {
        $selectAmount = 5;
    }


    if ($showRaisedAmount == 0 || $showProgressBar == 0) {
        $appearanceWindowHeight = 200;
    }

    if ($showRaisedAmount == 0 && $showProgressBar == 0) {
        $appearanceWindowHeight = 100;
    }

    $btnId = 'apreview-donate-btn-' . $pluginStyle;
    $modalId = 'donate-window-modal-' . $pluginStyle;

    $full_locale = get_locale();

    $language_code = substr($full_locale, 0, 2);

    switch ($language_code) {
        case 'en':
        case 'fr':
        case 'es':
        case 'de':
        case 'nl':
            // $language_code is one of the specified values, no need to change anything
            break;
        default:
            // $language_code is not one of the specified values, set it to "en" (English)
            $language_code = 'en';
            break;
    }
    
    // var_dump($data['data'],$showDonationFormOnly);
    // var_dump($language_code);

    $options = array(
        "widget_data_avaliable"=>$widget_data_avaliable,
        "id" => $id,
        "shortcode" => $id,
        "fundraiserName" => $fundraiserName,
        "styleId" => $styleId,
        "pluginStyle" => $pluginStyle,
        "colorCode" => $colorCode,
        "showDonateButton" => $showDonateButton,
        "showProgressBar" => $showProgressBar,
        "showRaisedAmount" => $showRaisedAmount,
        "showEndDate" => $showEndDate,
        "showDonationFormOnly" => $showDonationFormOnly,
        "doNotShowBox" => $doNotShowBox,
        "oneTimeCheck" => $oneTimeCheck,
        "monthlyCheck" => $monthlyCheck,
        "yearlyCheck" => $yearlyCheck,
        "firstAmountCheck" => $firstAmountCheck,
        "secondAmountCheck" => $secondAmountCheck,
        "thirdAmountCheck" => $thirdAmountCheck,
        "forthAmountCheck" => $forthAmountCheck,
        "otherChecked" => $otherChecked,
        "firstAmount" => $firstAmount,
        "secondAmount" => $secondAmount,
        "thirdAmount" => $thirdAmount,
        "forthAmount" => $forthAmount,
        "slug" => $slug,
        "font" => $font,
        "flocalId" => $flocalId,
        "progress_bar" => $progress_bar,
        "progress_bar_width" => $progress_bar_width,
        "achived_per" => $achived_per,
        "data" => $data,
        "appearanceWindowHeight" => $appearanceWindowHeight,
        "selectInterval" => $selectInterval,
        "selectAmount" => $selectAmount,
        "redirectUrl" => $redirectUrl,
        "is_opened" => $is_opened,
        "donationTitle" => $donationTitle,
        "elapsed" => $elapsed,
        "fundraiserTitle" => $fundraiserTitle,
        "successUrl" => $successUrl,
        "failureUrl" => $failureUrl,
        "buttonRadius" => $buttonRadius,
        "tip_enabled" => $tip_enabled,
        "language_code" => $language_code,
        "stripe_status" => $stripe_status,
        "data_source" => "word_press_plugin"
    );
    $jsonOptions = json_encode($options);
    // var_dump($jsonOptions)
?>
<!-- <script src="https://plugin.whydonate.com/wp_styling.js"><scrpt>
<link rel="stylesheet" type="text/css" href="https://plugin.whydonate.com/wdplugin-style.css"> -->
<div id="widget-here-<?php echo esc_html($id); ?>" ></div>
<script>
    if(typeof widgetElement === undefined){
        let widgetElement
    }
    var widgetOptions = <?php echo $jsonOptions; ?>;
    console.log("widgetOptions", widgetOptions)
    wp_html_generator(widgetOptions).then( (htmlContent) => {
        widgetElement = document.getElementById("widget-here-<?php echo esc_html($id); ?>")
        widgetElement.innerHtml = htmlContent
    })
</script>


<?php
$output = ob_get_clean();
return $output;
}
