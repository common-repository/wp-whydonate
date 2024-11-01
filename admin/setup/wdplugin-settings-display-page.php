<?php

?>

<div class='setup-container'>

    <div id="connect-account-msg"
        style="position: absolute; top: 10px; background:khaki; height: 40px; display: none; width: 90%;">
        <img src="<?php echo esc_url(plugin_dir_url(__FILE__)) . 'images/tickmark.png'; ?>" alt="Tick mark">
        <label style="font-size: 18px; margin-left: 10px; margin-top:10px">
            <?php _e('Your plugin is now connected to this account: ', 'whydonate-v2') ?>
        </label>
        <label id="apikey-owner-email-label" style="font-size: 18px; margin-left: 10px; margin-top:10px"></label>
    </div>

    <h1>
        <?php _e("Account Setup", 'whydonate-v2') ?>
    </h1>
    <div class="title-bar">
        <h4>
            <?php _e("How to use this plugin", 'whydonate-v2') ?>
        </h4>
        <div style="width: 80%; height: 1px; background: black; margin-left: 20px; margin-top: -25px;">
        </div>

        <div class="whydonate-account-instruction">
            <!-- <p><span><strong>
                        <?php _e("1. Connect the plugin to your WhyDonate account", "whydonate-v2") ?>
                    </strong></span></p>
            <p style="margin-top: -10px;"><span>
                    <strong>
                        <?php _e("For WhyDonate members: ", "whydonate-v2") ?>
                    </strong>
                    <?php _e("Open your <a href='https://www.whydonate.eu/profile' style='text-decoration:none' target='blank'> WhyDonate Profile</a> and click the API Key menu. Generate a new API Key and copy-paste it to the bottom of this page.", "whydonate-v2") ?>
                </span>
            </p>
            <p style="margin-top: -10px;"><span>
                    <strong>
                        <?php _e("No Whydonate account yet? ", "whydonate-v2") ?>
                    </strong>
                    <?php _e('<a href="https://www.whydonate.eu/registration/en" style="text-decoration:none" target="blank">Register for free at WhyDonate</a>. Open your <a href="https://www.whydonate.eu/registration/eu" style="text-decoration:none" target="blank">WhyDonate Profile</a> and click the API Key menu. Generate a new API Key and copy-paste it to the bottom of this page.', 'whydonate-v2') ?>
                </span>
            </p> -->
            <p>
                <span>
                    <strong>
                        <?php _e("1. Connect the plugin to your WhyDonate account", "whydonate-v2") ?>
                    </strong>
                </span>
            </p>
            <ul>
                <li>
                    <p style="margin-top: -10px;">
                        <span>
                            <strong>
                                <?php _e("For Existing WhyDonate Members: ", "whydonate-v2") ?>
                            </strong>
                            <ol>
                                <li><?php _e("Go to <a href='https://whydonate.com/' style='text-decoration:none' target='blank'>whydonate.com</a> and log in to your account.","whydonate-v2") ?></li>
                                <li><?php _e("Access the API Key from the side menu of your profile.", "whydonate-v2") ?></li>
                                <li><?php _e("Generate an API Key and copy-paste it into the designated section at the bottom of this page.","whydonate-v2") ?></li>
                            </ol>
                        </span>
                    </p>
                </li>
                <li>
                    <p><span>
                            <strong>
                                <?php _e("New to WhyDonate? Register for Free: ", "whydonate-v2") ?>
                            </strong>
                            <ol>
                                <li><?php _e("Sign up for a free account on <a href='https://whydonate.com/' style='text-decoration:none' target='blank'>whydonate.com</a>.","whydonate-v2") ?></li>
                                <li><?php _e("Navigate to <a href='https://whydonate.com/' style='text-decoration:none' target='blank'>whydonate.com</a> and access the API Key from the side menu of your profile.", "whydonate-v2") ?></li>
                                <li><?php _e("Generate an API Key and copy-paste it into the designated section at the bottom of this page.","whydonate-v2") ?></li>
                            </ol>
                        </span>
                    </p>
                </li>
            </ul>
        </div>

        <div class="whydonate-account-instruction" style="margin-top: 25px">
            <p><span><strong>
                        <?php _e("2. Create a Widget", "whydonate-v2") ?>
                    </strong></span></p>
            <p style="margin-top: -10px;">
                <span>
                    <ol>
                        <li><?php _e("You will be redirected to <a href='https://whydonate.com/' style='text-decoration:none' target='blank'>whydonate.com</a>.","whydonate-v2") ?></li>
                        <li><?php _e("Under the 'Style Widget' section, either create a new fundraiser or select an existing one from your fundraiser list.", "whydonate-v2") ?></li>
                        <li><?php _e("Customise the style according to your preferences and save it as your widget","whydonate-v2") ?></li>
                    </ol>
                </span>
            </p>
        </div>

        <div class="whydonate-account-instruction" style="margin-top: 25px">
            <p><span><strong>
                        <?php _e("3. Add the widget to your website", "whydonate-v2") ?>
                    </strong></span></p>
            <p style="margin-top: -10px;">
                <span>
                    <ol>
                        <li><?php _e("Copy the HTML code of the widget you wish to place.","whydonate-v2") ?></li>
                        <li><?php _e("Paste the code directly onto your webpage.", "whydonate-v2") ?></li>
                    </ol>
                </span>
            </p>
        </div>


    </div>

    <form method="post">
        <div class="setup-account">
            <div class="title-bar">
                <h4>
                    <?php _e("Account Setup", 'whydonate-v2') ?>
                </h4>
                <div style="width: 80%; height: 1px; background: black; margin-left: 20px; margin-top: -25px;">
                </div>
                <fieldset>
                    <div class="account-setup-option" >
                        <div style="flex-grow: 1">
                            <input type="radio" id="whydonate-member" name="whydonate-member" value="yes" checked
                                onclick="ShowHideDiv()">
                            <?php _e("I have a WhyDonate account", 'whydonate-v2') ?><br>
                        </div>
                        <div class="no-account-div" style="flex-grow: 4">
                            <input type="radio" id="whydonate-member" name="whydonate-member" value="no"
                                onclick="ShowHideDiv()">
                            <?php _e("I don't have a WhyDonate account", 'whydonate-v2') ?><br>
                        </div>
                    </div>
                </fieldset>

            </div>

            <div  style="display: flex; flex-direction: column">
                <div class="fundraiser-apikey-div" id="fundraiser-apikey-div">
                    <label for="fundraiser-id-label" style="margin-left: 5px">
                        <?php _e('Api Key', 'whydonate-v2') ?>
                    </label><br>
                    <!-- <input type="password" name="fundraiser-id-input" id="fundraiser-id-input"
                        style="height: 40px; border-radius: 10px;">
                    <input type="checkbox" onclick="apiKeyVisibility()">
                     -->

                     <!-- <div class="custom-input">
                        <input type="text" class="custom-text-input" placeholder="Enter text"> 
                        <img src="https://whydonate.com/cdn-cgi/imagedelivery/_0vgnXOEIHPwLg2E52a7gg/shared/visibilityOnPrimary/public">
                    </div> -->
                    <div class="api-key-container">
                        <input type="password" class="api-key-container-pass" name="fundraiser-id-input" id="fundraiser-id-input" style="border:none;outline: none;">
                        <span class="tooltip-trigger" data-tooltip="<?php _e('Click to show API Key', 'whydonate-v2') ?>" style="position: relative;">
                            <img id="fundraiser-id-image" src="https://whydonate.com/cdn-cgi/imagedelivery/_0vgnXOEIHPwLg2E52a7gg/shared/visibilityOff/public" alt="Show password">
                            <div class="tooltip-api-key"></div> <!-- Empty div for tooltip -->
                        </span>
                    </div>     
                    <div id="show-api-key" style="display:none;"><?php _e('Click to show API Key', 'whydonate-v2') ?></div>
                    <div id="hide-api-key" style="display:none;"><?php _e('Click to hide API Key', 'whydonate-v2') ?></div>

                    <div>
                    <span class="tooltip"><?php esc_html_e('Show your API Key', 'whydonate-v2'); ?></span>
                    <p id="blank-apikey-msg" style="display: none; font-weight: bold; color: red">
                        <?php _e('Enter your api-key.', 'whydonate-v2') ?>
                    </p>
                    <p id="apikey-error-msg" style="display: none; font-weight: bold; color: red">
                        <?php _e('Your API Key is not valid. We failed to connect the plugin.', 'whydonate-v2') ?>
                    </p>
                    <p id="updating-apikey-msg" style="display: none; font-weight: bold; color: green">
                        <?php _e('Updating the API Key...', 'whydonate-v2') ?>
                    </p>
                    <p id="update-apikey-success-msg" style="display: none; font-weight: bold; color: green">
                        <?php _e('Update success.', 'whydonate-v2') ?>
                    </p>
                    <?php
                    global $wpdb;
                    $table_name = $wpdb->prefix . "wdplugin_api_key";
                    $query = $wpdb->prepare("SELECT * FROM $table_name");
                    $result = $wpdb->get_results($query, ARRAY_A);
                    $email = '';

                    if (!empty($result)) {
                        $email = $result[0]['email'];
                        $apiKey = $result[0]['apiKey'];

                        if ($email != "") { ?>
                            <div class="apikey-account-owner-text" id="apikey-account">
                            <script>
                                const inputField = document.getElementById("fundraiser-id-input");
                                inputField.value = "<?php echo esc_html($apiKey); ?>"
                            </script>
                                <p class="apikey-account-owner-text" style="font-weight: bold; color: gray">
                                    <?php _e('Your plugin is now connected to this account: ', 'whydonate-v2') ?>
                                </p>
                                <p class="apikey-account-owner-email" id="apikey-account-owner-email" >
                                    <?php echo esc_html($email); ?>
                                </p>
                            </div>

                        <?php }
                    }

                    ?>


                    <p>
                        <?php _e('Open your <a href="https://whydonate.eu/profile" style="text-decoration:none" target="blank"> WhyDonate Profile</a> and click the API Key menu. Generate a new API Key and copy-paste it to the bottom of this page. ', 'whydonate-v2') ?>
                    </p>
                </div>

                <div style="margin-top: 20px">
                    <button class="btn-style" type="submit" name="submitFundraiserId" id="submit-api-key-btn"
                        style="float: none; margin: 0px; margin-left: 10px; width: 100px">
                        <?php _e('Save', 'whydonate-v2') ?>
                        <!-- <i class="material-icons right">save</i> -->
                    </button>
                    <!-- <button style="margin-left: 10px" onclick="checkDatabase()">Check</button> -->
                </div>

            </div>

            <div id="registration-div" class="registration-div" style="display: none">
                <h3>
                    <?php _e("Sign up for a free account on <a href='https://whydonate.com/' style='text-decoration:none' target='blank'>whydonate.com</a>. Navigate to <a href='https://whydonate.com/' style='text-decoration:none' target='blank'>whydonate.com</a> and access the API Key from the side menu of your profile. Generate an API Key and copy-paste it into the designated section at the bottom of this page.", 'whydonate-v2') ?>
                </h3>
            </div>
        </div>

        <br><br>
        <!-- <input type="submit" name="submit" value="Submit"> -->

    </form>

    <div class="modal" id="modal">
        <!-- Place at bottom of page -->
    </div>


</div>