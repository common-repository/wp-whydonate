<?php
function getFundraiserShortcodesArray()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "wdplugin_api_key";
    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", 1);
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
    curl_setopt(
        $curl,
        CURLOPT_HTTPHEADER,
        array(
            'API-KEY: ' . $apiKey
        )
    );

    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
}

$response = getFundraiserShortcodesArray();
$responseJson = json_decode($response, true);
$responseArray = $responseJson["data"];

$escapedResponseArray = array_map('esc_html', $responseArray);
function getUserFundraiserStylings()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "wdplugin_api_key";
    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", 1);
    $retrieve_data = $wpdb->get_results($query);

    $apiKey = '';
    foreach ($retrieve_data as $retrieved_data) {
        $apiKey = $retrieved_data->apiKey;
    }

    global $wdplugin_fundraiser_worker_url;
    $wdplugin_fundraiser_worker_url;
    $url = $wdplugin_fundraiser_worker_url.'/fundraiser/styles';

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(
        $curl,
        CURLOPT_HTTPHEADER,
        array(
            'API-KEY: ' . $apiKey
        )
    );

    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
}

$responseStylings = getUserFundraiserStylings();
$responseStylingsJson = json_decode($responseStylings, true);
$responseStylingsArray = $responseStylingsJson["data"];

function generateHtmlCode($shortcode, $successUrl, $failureUrl)
{
    // HTML code
    $full_locale = get_locale();

    // Extract the language code, e.g., "en"
    $lang = substr($full_locale, 0, 2);

    switch ($lang) {
        case 'en':
        case 'fr':
        case 'es':
        case 'de':
        case 'nl':
            // $language_code is one of the specified values, no need to change anything
            break;
        default:
            // $language_code is not one of the specified values, set it to "en" (English)
            $lang = 'en';
            break;
    }

    $html = "";
    $html .= '<div>';
    $html .= '    <div';
    $html .= "        id=\"widget-here-$shortcode\"";
    $html .= '        class="widget-here"';
    $html .= "        data-shortcode=\"$shortcode\"";
    $html .= '        data-lang="auto"';
    $html .= "        data-success_url=\"$successUrl\"";
    $html .= "        data-fail_url=\"$failureUrl\"";
    $html .= '        value="donation-widget"';
    $html .= '    ></div>';
    $html .= '</div>';
    $html .= '<link';
    $html .= '    rel="stylesheet"';
    $html .= '    href="https://plugin.whydonate.com/wdplugin-style.css"';
    $html .= '/>';
    $html .= '<script';
    $html .= '    src="https://plugin.whydonate.com/wp_styling.js"';
    $html .= '    type="text/javascript"';
    $html .= '></script>';

    return $html;
}


?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
    .widgets-list-style {
        transform: scale(0.6);
        transform-origin: top left;
        transform-origin: 0 0;
        width: 50%;
        height: 50%;
    }
</style>
<div>
    <script>
        let html_codes = {};
    </script>

    <div class="widget-list" id="widget-list">
        <div class="widget-list-header">
            <div class="widget-list-heading">
                <h1 style="flex-grow: 1;">
                    <?php _e('Embed donation Widget to your website', 'whydonate-v2') ?>
                    <!-- <?php _e('Widgets', 'whydonate-v2') ?> -->
                </h1>
            </div>
            <div class="widget-list-button">
                <button class="btn-style" onclick="redirectToCreateWidget()">
                <span class="btn-style-text">
                    <?php _e('Create New Widget', 'whydonate-v2') ?>
                    <img src="https://imagedelivery.net/_0vgnXOEIHPwLg2E52a7gg/shared/outboundLink/public" class="white-svg">
                </span>
                </button>
                <button class="btn-style-refresh" onclick="refreshContent()">
                    <span class="btn-style-text"><?php _e('Refresh', 'whydonate-v2') ?></span>
                </button>
            </div>
        </div>
        <div class="widget-list-table">
        <table class="data-list-table">
            <thead>
                <tr>
                    <th class="widget-id-column" scope="col">
                        <?php _e('Widget ID', 'whydonate-v2') ?>
                    </th>
                    <th scope="col">
                        <?php _e('Style', 'whydonate-v2') ?>
                    </th>
                    <th scope="col">
                        <?php _e('Fundraisers', 'whydonate-v2') ?>
                    </th>
                    <!-- <th scope="col">
                        <?php _e('Shortcode', 'whydonate-v2') ?>
                    </th> -->
                    <th scope="col">
                        <?php _e('HTML Code', 'whydonate-v2') ?>
                    </th>
                    <th scope="col">
                        <?php _e('Action', 'whydonate-v2') ?>
                    </th>
                </tr>
            </thead>

        <?php
        global $wpdb;
        $table_name = $wpdb->prefix . "wdplugin_config_widget";
        $style_table_name = $wpdb->prefix . "wdplugin_style";

        $query = $wpdb->prepare("
            SELECT *
            FROM $table_name
            LEFT JOIN $style_table_name ON $table_name.styleId = $style_table_name.id
        ");
        $result = $wpdb->get_results($query, ARRAY_A);
        $resultWithStyles = array_merge($result, $responseStylingsArray['styles']);

        if (empty($resultWithStyles)) {
            ?>
        </table>
        </div>
        <div style="display: flex; flex-direction: column">

            <div style="flex-grow: 1; text-align: center; margin-top: 20px">
                <img src="<?php echo plugin_dir_url(__FILE__) . 'images/widget.png'; ?>" alt="Italian Trulli">
                <h3 style="text-align: center; padding-top: 10px">
                    <?php _e("It seems you haven’t created any widgets yet.", "whydonate-v2") ?>
                </h3>
            </div>
            <div style="flex-grow: 1; text-align: center">
                <button class="btn-style" onclick="redirectToCreateWidget()" style="margin: 0px; float: none">
                    <?php _e('Add Widget', 'whydonate-v2') ?>
                    <img src="https://imagedelivery.net/_0vgnXOEIHPwLg2E52a7gg/shared/outboundLink/public" class="white-svg">
                </button>
            </div>

        </div>
        
    <?php
    } else {
// Initialize arrays
$filteredArray = [];
$seenShortcodes = [];
$seenShortcodesWhyDonate = [];

        // Iterate through the original array
        foreach ($resultWithStyles as $item) {
            echo "Processing item with shortcode: {$item['shortcode']}\n";

            $shortcode = isset($item['shortcode']) ? trim($item['shortcode']) : null;
            $source = isset($item['source']) ? strtolower(trim($item['source'])) : '';

            if (empty($shortcode)) {
                echo "Skipping item due to missing shortcode\n";
                continue;
            }

            if (!in_array($shortcode, $seenShortcodes)) {
                // Add the item to the filtered array
                $filteredArray[] = $item;
                $seenShortcodes[] = $shortcode;

                // Track shortcodes from whydonate.com
                if ($source === 'whydonate.com') {
                    $seenShortcodesWhyDonate[] = $shortcode;
                }

            } elseif ($source === 'whydonate.com') {
                foreach ($filteredArray as $key => $filteredItem) {
                    if ($filteredItem['shortcode'] === $shortcode) {
                        // Replace the item with the one from whydonate.com
                        $filteredArray[$key] = $item;

                        // Ensure it's marked as seen from whydonate.com
                        if (!in_array($shortcode, $seenShortcodesWhyDonate)) {
                            $seenShortcodesWhyDonate[] = $shortcode;
                        }
                        break;
                    }
                }
            }
        }


        foreach ($filteredArray as $row) {
            $shortcode = esc_attr($row['shortcode']);
            // Escape the shortcode for the onclick attribute
             // Escape the shortcode for the onclick attribute
            ?>
        <tr>
            <td class="widgets-list-style-id widget-id-column" style="text-align: center">
                <?php echo $shortcode; ?>
            </td>
            <!-- <td class="widgets-list-plugin-style"><?php echo esc_html($row['pluginStyle']); ?></td> -->
            <td>
                <div class="widgets-list-style-<?php echo $shortcode; ?>"
                    id="widgets-list-style-<?php echo $shortcode; ?>" style="display: flex;">
                    <?php
                    $id = $shortcode;
                    global $wpdb;
                    //$widget_table = "wp_wdplugin_config_widget";
                    $widget_table = $wpdb->prefix . "wdplugin_config_widget";
                    //$styles_table = "wp_wdplugin_style";
                    $styles_table = $wpdb->prefix . "wdplugin_style";
                    $query = $wpdb->prepare("SELECT * FROM $widget_table WHERE shortcode = %s", $id);
                    $retrieve_data = $wpdb->get_results($query);

                    if (empty($retrieve_data) || !is_array($retrieve_data)) {
                        // If empty or not an array, assign default values from $row
                        $temporary = (object) $row;
                        $retrieve_data = array($temporary);
                    }

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
                    $buttonRadius = 30;
                    $tip_enabled = true;
                    $language_code = ''; //
                    $stripe_status = true;
                    $background_allowed = 0;
                    $successUrl = '';
                    $failureUrl = '';
                    $successUrlOld = '';
                    $failureUrlOld = '';

                    if (!empty($retrieve_data)) {

                        foreach ($retrieve_data as $retrieved_data) {
                            $fundraiserName = $retrieved_data->fundraiserName;
                            $pluginStyle = $retrieved_data->pluginStyle;
                            $slug = $retrieved_data->slugName;
                            $flocalId = $retrieved_data->slugId;
                            $styleId = $retrieved_data->styleId;
                            $successUrl = $retrieved_data->successUrl;
                            $failureUrl = $retrieved_data->failureUrl;
                            $successUrlOld = $retrieved_data->successUrl;
                            $failureUrlOld = $retrieved_data->failureUrl;
                        }
                    }

                    $successUrl = esc_attr($row['successUrl']);
                    $failureUrl = esc_attr($row['failureUrl']);

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

                        // Check if $retrieve_data is empty
                        if (empty($retrieve_styles)) {
                            $temporary_retrieve_styles = (object) $row;
                            $retrieve_styles = array($temporary_retrieve_styles);
                        }


                        if (!empty($retrieve_styles)) {
                            // var_dump($retrieve_styles);
                            foreach ($retrieve_styles as $style_data) {
                                $font = $style_data->font;
                                $colorCode = $style_data->primaryColor;
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
                                $background_allowed = $style_data->background_allowed;

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

                    // echo esc_html($font)  . '<br>';
                    // echo $pluginStyle  . '<br>';
                    // echo esc_html($colorCode)  . '<br>';
                    $full_locale = get_locale();

                    // Extract the language code, e.g., "en"
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
                        "id" => $id,
                        "background_allowed" => $background_allowed,
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
                        "successUrl" => $successUrlOld,
                        "failureUrl" => $failureUrlOld,
                        "buttonRadius" => $buttonRadius,
                        "tip_enabled" => $tip_enabled,
                        "language_code" => $language_code,
                        "stripe_status" => $stripe_status,
                        "data_source" => "word_press_plugin"
                    );
                    $jsonOptions = json_encode($options);
                    ?>
                    <div id="html_preview_box_image_<?php echo $shortcode; ?>">
                    </div>
                    <img id="preview-image-<?php echo $shortcode; ?>" src="">
                    <script>
                        var jsonData = <?php echo $jsonOptions; ?>;

                        switch (true) {
                        case jsonData['doNotShowBox'] == 1:
                            // console.log('Donation Button Only');
                            var imageElement = document.createElement('img');

                            imageElement.src = 'https://whydonate.com/cdn-cgi/imagedelivery/_0vgnXOEIHPwLg2E52a7gg/shared/Card_type-2/w=180'; // Replace with the actual path to your image
                            imageElement.width = 180;
                            var divElement = document.getElementById("html_preview_box_image_<?php echo $shortcode; ?>");

                            // Insert the image into the div
                            divElement.appendChild(imageElement);
                        break;

                        case jsonData['showDonationFormOnly'] == 0 && jsonData['background_allowed'] == 0:
                                var imageElement = document.createElement('img');

                                imageElement.src = 'https://whydonate.com/cdn-cgi/imagedelivery/_0vgnXOEIHPwLg2E52a7gg/shared/Card_type-3/w=180';
                                imageElement.width = 180; 
                                var divElement = document.getElementById("html_preview_box_image_<?php echo $shortcode; ?>");

                                divElement.appendChild(imageElement);
                                break;

                        case jsonData['showDonationFormOnly'] == 1 && jsonData['background_allowed'] == 0:
                            // console.log('Form Only without image');

                            var imageElement = document.createElement('img');

                                // Set attributes for the image (src, alt, etc.)
                            imageElement.src = 'https://imagedelivery.net/_0vgnXOEIHPwLg2E52a7gg/shared/wpPreviewForm/w=180'; // Replace with the actual path to your image
                            imageElement.width = 180;
                            var divElement = document.getElementById("html_preview_box_image_<?php echo $shortcode; ?>");

                            // Insert the image into the div
                            divElement.appendChild(imageElement);
                            break;

                        case jsonData['showDonationFormOnly'] == 0 && jsonData['background_allowed'] == 1:
                            // console.log('Widget with image');
                            
                            var imageElement = document.createElement('img');

                                // Set attributes for the image (src, alt, etc.)
                            imageElement.src = 'https://whydonate.com/cdn-cgi/imagedelivery/_0vgnXOEIHPwLg2E52a7gg/shared/Card_type/w=180'; // Replace with the actual path to your image
                            imageElement.width = 180;
                            var divElement = document.getElementById("html_preview_box_image_<?php echo $shortcode; ?>");

                            // Insert the image into the div
                            divElement.appendChild(imageElement);
                            break;

                        case jsonData['showDonationFormOnly'] == 1 && jsonData['background_allowed'] == 1:
                            // console.log('Form Only with image');

                            var imageElement = document.createElement('img');

                                // Set attributes for the image (src, alt, etc.)
                            imageElement.src = 'https://whydonate.com/cdn-cgi/imagedelivery/_0vgnXOEIHPwLg2E52a7gg/shared/Card_type-1/w=180'; // Replace with the actual path to your image
                            imageElement.width = 180;
                            var divElement = document.getElementById("html_preview_box_image_<?php echo $shortcode; ?>");

                            // Insert the image into the div
                            divElement.appendChild(imageElement);
                            break;

                        default:
                            break;
                        }

                    </script>
                    
                </div>
            </td>
            <td class="widgets-list-fundraiser-name">
                <div class="widgets-list-fundraiser-name-div"><?php echo esc_html($row['fundraiserName']); ?></div>
            </td>
            <!-- <td class="widgets-list-shortcode">
                <?php echo '[whydonate id="' . $shortcode . '"]'; ?>
            </td> -->
            <td class="widgets-html-shortcode" id='widgets-html-shortcode-<?php echo esc_html($shortcode); ?>'>
                <?php if (in_array($shortcode, $escapedResponseArray)): ?> 
                    <!-- Text alongside the clickable container -->
                    <div class="clickable-container">
                        <span id='widgets-html-shortcode-span-<?php echo esc_html($shortcode); ?>' class='widgets-html-shortcode-span'>
                            <?php echo esc_html(generateHtmlCode($shortcode, $successUrl, $failureUrl)); ?>
                            <!-- <?php echo esc_html($shortcode); ?> -->
                        </span>
                        <!-- SVG image inside a clickable container -->
                        <div class="clickable-svg">
                            <!-- Use a data URI with the SVG content -->
                            <img
                                id='copy-icon-<?php echo esc_html($shortcode); ?>'
                                class="clickable-svg clickable-svg-img"
                                src="<?php echo plugin_dir_url(__FILE__) . 'images/fileCopy.svg'; ?>"
                                alt="Copy Icon"
                                onclick="copyText('widgets-html-shortcode-span-<?php echo esc_html($shortcode); ?>'); copySnackBar();"
                                onmousedown="applyFilter('copy-icon-<?php echo esc_html($shortcode); ?>')"
                                onmouseup="removeFilter('copy-icon-<?php echo esc_html($shortcode); ?>')"
                                onmouseleave="removeFilter('copy-icon-<?php echo esc_html($shortcode); ?>')"
                            />
                            <span class="tooltip"><?php esc_html_e('Copy the code', 'whydonate-v2'); ?></span>
                        </div>
                    </div>
                    <!-- Additional content generated by generateHtmlCode function -->
                <?php else: ?>
                    <div>
                        <!-- Display a message when the shortcode is not in the array -->
                        <?php echo esc_html_e('Please create a widget in the widget portal by clicking on the edit button', 'whydonate-v2'); ?>
                    </div>
                <?php endif; ?>
            </td>
            <td class="widgets-list-item-btn">
                <!-- <button class="widgets-list-item-edit-btn" style="width: 135px"
                    onclick='copyCode("<?php echo $shortcode; ?>")'>
                    <?php esc_html_e('Copy shortcode', 'whydonate-v2'); ?>
                </button> -->
                <div class="action-button-container">
                    <!-- <button class="widgets-list-item-edit-btn"
                        onclick='editRedirect(<?php echo json_encode($row, JSON_HEX_APOS); ?>)'>
                        <img height="28px" src="https://whydonate.com/cdn-cgi/imagedelivery/_0vgnXOEIHPwLg2E52a7gg/shared/editSmall/public">
                    </button> -->
                    <!-- <?php esc_html_e('Edit', 'whydonate-v2'); ?> -->
                    <!-- <button class="widgets-list-item-remove-btn" name="remove_widget" style="margin-left: 10px;">
                        <img height="28px" src="https://whydonate.com/cdn-cgi/imagedelivery/_0vgnXOEIHPwLg2E52a7gg/shared/deleteSmall/public">
                    </button> -->
                    <!-- <?php esc_html_e('Remove', 'whydonate-v2'); ?> -->
                    <button class="widgets-list-item-edit-btn"
                        onclick='editRedirect(<?php echo json_encode($row, JSON_HEX_APOS); ?>)'>
                        <?php esc_html_e('Edit', 'whydonate-v2'); ?>
                        <img src="https://imagedelivery.net/_0vgnXOEIHPwLg2E52a7gg/shared/outboundLink/public">
                    </button> 
                </div>
            </td>
        </tr>
        <?php
        }

        echo '</table>';
    }
    ?>
</div>


<div class="modal"  id="modal">
    <div class="image-container"></div>
    <!-- Add other modal content here if needed -->
    </div>
</div>
<div id="snackbar"><?php _e('Copied to Clipboard', 'whydonate-v2'); ?></div>