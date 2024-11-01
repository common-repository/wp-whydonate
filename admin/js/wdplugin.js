let wdplugin_home_url = "https://whydonate.com/";

// let wdplugin_fundraiser_worker_url = 'https://fundraiser.whydonate.dev';
// let wdplugin_donation_worker_url = 'https://donation.whydonate.dev';
// let wdplugin_account_worker_url = 'https://account.whydonate.dev';

function ShowHideDiv() {
  var chkYes = document.getElementById("whydonate-member");
  var fundraiserIDInputDiv = document.getElementById("fundraiser-apikey-div");
  var registrationDiv = document.getElementById("registration-div");
  if (chkYes.checked) {
    // console.log("i'm here");
    fundraiserIDInputDiv.style.display = "block";
    registrationDiv.style.display = "none";
  } else {
    registrationDiv.style.display = "block";
    fundraiserIDInputDiv.style.display = "none";
  }
}

function redirectToCreateFundraiser() {
  window.open(`${wdplugin_home_url}fundraising/create`, "_blank");
}

function editRedirect(row) {
  if (
    row.pluginStyle == "Default" ||
    row.pluginStyle == "Standaard" ||
    row.pluginStyle == "Selecteer een stijl"
  ) {
    row.font = "";
    row.colorCode = "#2828d6";
    row.showDonateButton = 1;
    row.showProgressBar = 2;
    row.showRaisedAmount = 3;
    row.showEndDate = 4;
    row.showDonationFormOnly = 0;
    row.doNotShowBox = 0;
    row.oneTimeCheck = 1;
    row.monthlyCheck = 2;
    row.yearlyCheck = 3;
    row.firstAmountCheck = 1;
    row.secondAmountCheck = 2;
    row.thirdAmountCheck = 3;
    row.forthAmountCheck = 4;
    row.otherChecked = 1;
    row.firstAmount = 25;
    row.secondAmount = 50;
    row.thirdAmount = 75;
    row.forthAmount = 100;
    row.donationTitle = "My Safe Donation";
    row.buttonRadius = 30;
  }

  var nonce_transfer_styling = my_script_vars.nonce_transfer_styling;
  jQuery.ajax({
    url: ajaxurl,
    type: "post",
    data: {
      action: "transfer_styling",
      nonce_transfer_styling: nonce_transfer_styling,
      row: row,
    },
    beforeSend: function () {
      // show loader here
      jQuery("#modal").css("display", "block");
    },
    success: function (response) {
      let response_json = JSON.parse(response);
      // var newTab = window.open();
      // newTab.opener = null;
      // newTab.location.href = response_json.data.redirect_url;
      window.open(`${wdplugin_home_url}fundraising/widget/${response_json.data.redirect_url}`, "_blank");
      
      jQuery("#modal").css("display", "none");
      // console.log("response ", response);
      //window.location.reload();
    },
    error: function (xhr) {
      //error handling
      //console.log("suppression echoué");
    },
    complete: function () {
      // hide loader here
      jQuery("#modal").css("display", "none");
    },
  });
}


function redirectToCreateWidget() {
  //console.log("called redirectToCreateWidget()");
  let full_locale = navigator.language;

  // Extract the language code, e.g., "en"
  let lang = full_locale.substring(0, 2);

  switch (lang) {
    case "en":
    case "fr":
    case "es":
    case "de":
    case "nl":
      // $language_code is one of the specified values, no need to change anything
      break;
    default:
      // $language_code is not one of the specified values, set it to "en" (English)
      lang = "en";
      break;
  }
  // console.log(`${wdplugin_home_url}/${lang}/widget/create`)
  window.location.href = `${wdplugin_home_url}fundraising\\widget\\create`;
  //   var editWidgetDiv = document.getElementById("edit-widget");
  //   var widgetList = document.getElementById("widget-list");
  //   widgetList.style.display = "none";
  //   editWidgetDiv.style.display = "block";
}

jQuery(".plugin-style-list-item-remove-btn").click(function () {
  // console.log("remove row clicked");

  var button = jQuery(this),
    tr = button.closest("tr");
  var idNum = tr.find("td.plugin-style-list-id").text();
  var nonce_plugin_remove_button = my_script_vars.nonce_plugin_remove_button;
  //console.log("clicked button with id", idNum);
  /// ****** More standard way of doing AJAX Call /////////

  if (confirm("Are you sure you want to delete this entry?")) {
    // jQuery("#modal").css("display", "block");

    jQuery.ajax({
      url: ajaxurl,
      type: "post",
      data: {
        action: "my_action",
        id: idNum,
        nonce_plugin_remove_button: nonce_plugin_remove_button,
      },
      beforeSend: function () {
        // show loader here
        jQuery("#modal").css("display", "block");
      },
      success: function (response) {
        tr.remove();
        //jQuery("#modal").css("display", "none");
      },
      error: function (xhr) {
        //error handling
        //console.log("suppression echoué");
      },
      complete: function () {
        // hide loader here
        jQuery("#modal").css("display", "none");
      },
    });
  }
});

jQuery("#submit-api-key-btn").click(function (e) {
  e.preventDefault();
  var apiKey = document.getElementById("fundraiser-id-input").value;
  //console.log("api key btn clicked ", apiKey);

  jQuery("#blank-apikey-msg").hide();
  // jQuery("#apikey-error-msg").text("");
  jQuery("#apikey-error-msg").hide();
  // jQuery("#apikey-error-msg").css("display","none");
  // jQuery("#updating-apikey-msg").text("");
  jQuery("#updating-apikey-msg").hide();
  // jQuery("#updating-apikey-msg").css("display","none");
  // jQuery("#update-apikey-success-msg").text("");
  jQuery("#update-apikey-success-msg").hide();
  // jQuery("#update-apikey-success-msg").css("display","none");
  // jQuery('#apikey-account').text("");
  jQuery("#apikey-account").hide();
  // jQuery("#apikey-account").css("display","none");
  var nonce = my_script_vars.nonce;

  if (apiKey === "") {
    //console.log("api key blank");
    jQuery("#blank-apikey-msg").show();
  } else {
    payload = {
      api_key: apiKey,
    };
    jQuery.ajax({
      url: ajaxurl,
      type: "post",
      data: {
        action: "check_api_key",
        payload: payload,
        api_key: apiKey,
        nonce: nonce,
      },
      beforeSend: function () {
        // show loader here
        jQuery("#modal").css("display", "block");
      },
      success: function (response) {
        // console.log(response)
        response =
          response.substr(response.length - 1, 1) === "0"
            ? response.substr(0, response.length - 1)
            : response;
        jsonArray = JSON.parse(response);
        if (jsonArray["status"] === 200) {
          var api_key = my_script_vars.nonce_api_key;

          username = jsonArray["data"]["username"];
          email = jsonArray["data"]["email"];
          //console.log('email ', email);
          jQuery("#apikey-owner-email-label").text(email);
          jQuery("#connect-account-msg").css("display", "flex");
          jQuery("#connect-account-msg").delay(4000).fadeOut(3000);

          jQuery.ajax({
            url: ajaxurl,
            type: "post",
            data: {
              action: "api_key",
              api_key: apiKey,
              username: username,
              email: email,
              nonce_api_key: api_key,
            },
            beforeSend: function () {
              jQuery("#updating-apikey-msg").show();
              jQuery("#modal").css("display", "block");
            },
            success: function (response) {
              console.log("code changed");
              console.log("response ", response);
              if (
                response == "Insert success" ||
                response == "Update success"
              ) {
                jQuery("#updating-apikey-msg").hide();
                jQuery("#update-apikey-success-msg").show();
                jQuery("apikey-account").show();
                jQuery("#apikey-account").css("display", "flex");
                jQuery("#apikey-account-owner-email").text(email);
                localStorage.setItem("shouldRedirect", "true");
                window.location.reload();
              } else if (
                response == "Insert not success" ||
                response == "Update not success"
              ) {
                jQuery("apikey-error-msg").show();
              } else {
                jQuery("blank-apikey-msg").show();
              }
            },
            error: function (xhr) {},
            complete: function () {
              // hide loader here
              jQuery("#modal").css("display", "none");
            },
          });
        } else {
          // var text = jQuery("#apikey-account").text()
          // console.log('api key text content ', document.getElementById('apikey-account').textContent);
          jQuery("#apikey-error-msg").show();
          // jQuery("#resTest").css("color", "red");

          // jQuery("#apikey-account").css("font-weight", "Bold");
          // jQuery("#apikey-account").css("color", "red");
          // jQuery("#apikey-account").text('Failed to get information of the api key owner.')

          jQuery.ajax({
            url: ajaxurl,
            type: "post",
            data: {
              action: "api_key",
              api_key: apiKey,
              usename: "",
              email: "",
              nonce_api_key: api_key,
            },
            beforeSend: function () {
              // show loader here
              // jQuery("#updating-apikey-msg").css("display", "block");
              jQuery("#modal").css("display", "block");
            },
            success: function (response) {
              //jQuery("#modal").css("display", "none");
              console.log("code changed");
              console.log("success", response);

              // jQuery("#resTest").css("font-weight", "Bold");
              if (
                response == "Insert success" ||
                response == "Update success"
              ) {
                // jQuery("#updating-apikey-msg").hide();
                // jQuery("#update-apikey-success-msg").show();
                // jQuery("#apikey-account").hide();
                // jQuery("#resTest").text(response);
                // jQuery("#resTest").css("color", "green");
                // jQuery("#apikey-account").css("font-weight", "Bold");
                // jQuery("#apikey-account").css("color", "red");
                // jQuery("#apikey-account").text('Failed to retrive information of the api key owner.')
                // var location = window.location.href;
                // var locationArr = location.split("?");
                // location = locationArr[0] + "?page=whydonate-widget-list";
                // window.location.replace(location);
              } else if (
                response == "Insert not success" ||
                response == "Update not success"
              ) {
                jQuery("#apikey-error-msg").show();
                // jQuery("#apikey-error-msg").text(
                //     "It seems you have the same apikey stored already. Otherwise you can submit again."
                // );
                // jQuery("#resTest").css("color", "red");

                // } else if (response == 'The key is invalid, please check') {
                //     jQuery('#resTest').text(response);
                //     jQuery('#resTest').css("color", "red");
              } else {
                jQuery("#blank-apikey-msg").show();
                // jQuery("#resTest").text(
                //     "Please fill the form before submitting"
                // );
                // jQuery("#resTest").css("color", "blue");
              }
            },
            error: function (xhr) {
              //error handling
              //console.log("suppression echoué");
            },
            complete: function () {
              // hide loader here
              // jQuery("#update-apikey-success-msg").hide();
              jQuery("#modal").css("display", "none");
            },
          });
        }
      },
      error: function (xhr) {
        //error handling
        //console.log("suppression echoué");
      },
      complete: function () {
        // hide loader here
        jQuery("#modal").css("display", "none");
      },
    });
  }
});


function htmlCodeBoxGenerator() {
  var html = `
    <div style="margin-top: 100px">
        <div
            id="widget-here"
            class="widget-here"
            data-shortcode="${shortcode}"
            data-lang="en"
            data-success_url=""
            data-fail_url=""
            value="donation-widget"
        ></div>
    </div>
    <link
        rel="stylesheet"
        href="https://plugin.whydonate.com/wdplugin-style.css"
    />
    <script
        src="https://plugin.whydonate.com/wp_styling.js"
        type="text/javascript"
    ></script>
  `;

  return html;
}

// document.addEventListener("DOMContentLoaded", function () {


// });

function isURL(str) {
  var regex =
    /(https|http):\/\/(\w+:{0,1}\w*)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%!\-\/]))?/;
  var pattern = new RegExp(regex);
  return pattern.test(str);
}

function trimUrl(str) {
  if (!str.includes("https") && !str.includes("http")) {
    str = "https://" + str;
  }

  if (!str.includes("www")) {
    if (!str.includes("https://") && !str.includes("http://")) {
      str = "https://" + "www." + str;
    } else if (str.includes("https://") || str.includes("http://")) {
      if (str.includes("https://")) {
        strArr = str.split("https://");
        str = "https://" + "www." + strArr[1];
      } else {
        strArr = str.split("http://");
        str = "http://" + "www." + strArr[1];
      }
    } else {
    }
  }

  return str;
}

function changeDonationLabel(obj) {
  //console.log("called changeDonationLabel() ", obj);
  var label = document.getElementById("preview-header-label");
  label.textContent = obj;
}

jQuery(document).ready(function () {
  //console.log("called in here config widget");
  let locationString = window.location.href;
  let locationArray = locationString.split("?");
  //console.log('location ', locationString);
  //console.log("location last part ", locationArray[1]);

  if (locationArray[1] === "page=myplugin") {
    let shouldRedirect = localStorage.getItem("shouldRedirect");
    console.log("should redirect ", shouldRedirect);
    if (shouldRedirect === "true") {
      localStorage.setItem("shouldRedirect", "false");
      setTimeout(function () {
        //your code to be executed after 1 second
        var location = window.location.href;
        var locationArr = location.split("?");
        location = locationArr[0] + "?page=whydonate-widget-list";
        window.location.replace(location);
        window.scrollTo(0, 0);
      }, 2000);
    }
  }

  if (locationArray[1] === "page=whydonate-config-widget") {
    // console.log("Generate List");
    document.getElementById("error-msg-p").innerHTML = "";
    const newFundraiserLink =
      locationArray[0] + "?page=whydonate-fundraiser-list";
    const customStylelink =
      locationArray[0] + "?page=whydonate-plugin-style-list";
    document.getElementById("create-new-fundraiser-link").href =
      newFundraiserLink;

    document.getElementById("custom-plugin-style-link").href = customStylelink;
    jQuery("#error-msg-p").css("color", "red");
    
  }

  if (locationArray[1] === "page=whydonate-widget-list") {
    // const customStylelink =
    //   locationArray[0] + "?page=whydonate-plugin-style-list";

    // document.getElementById("custom-widget-plugin-style-link").href =
    //   customStylelink;
    // document.getElementById("error-msg-p").innerHTML = "";

    var nonce_fundraiser_shortcodes_array =
      my_script_vars.nonce_fundraiser_shortcodes_array;
    jQuery.ajax({
      url: ajaxurl,
      type: "post",
      data: {
        action: "fundraiser_shortcodes_array",
        nonce_fundraiser_shortcodes_array: nonce_fundraiser_shortcodes_array,
      },
      beforeSend: function () {
        // show loader here
        jQuery("#modal").css("display", "block");
      },
      success: function (response) {
        // console.log(response)
        jQuery("#modal").css("display", "none");
        
      },
      error: function (xhr) {
        //error handling
        //console.log("suppression echoué");
      },
      complete: function () {
        // hide loader here
        jQuery("#modal").css("display", "none");
      },
    });
  }
});

function copyCode(value) {
  var shortcode = '[whydonate id="' + value + '"]';
  // console.log("shortcode ", shortcode);

  // Create new element
  var el = document.createElement("textarea");
  // Set value (string to be copied)
  el.value = shortcode;
  // Set non-editable to avoid focus and move outside of view
  el.setAttribute("readonly", "");
  el.style = {
    position: "absolute",
    left: "-9999px",
  };
  document.body.appendChild(el);
  // Select text inside element
  el.select();
  // Copy text to clipboard
  document.execCommand("copy");
  // Remove temporary element
  document.body.removeChild(el);
  // alert("Copied the shortcode: " + shortcode);

  var x = document.getElementById("snackbar");
  x.className = "show";
  setTimeout(function(){ x.className = x.className.replace("show", ""); }, 2500);
}

function checkDatabase() {
  console.log("in Chekc database");
  var nonce_check_database = my_script_vars.nonce_check_database;
  jQuery.ajax({
    url: ajaxurl,
    type: "post",
    data: {
      action: "check_database",
      nonce_check_database: nonce_check_database,
    },
    beforeSend: function () {
      // show loader here
      jQuery("#modal").css("display", "block");
    },
    success: function (response) {
      console.log("check response ", response);
      jQuery("#modal").css("display", "none");
      // console.log("response ", response);
      //window.location.reload();
    },
    error: function (xhr) {
      //error handling
      //console.log("suppression echoué");
    },
    complete: function () {
      // hide loader here
      jQuery("#modal").css("display", "none");
    },
  });
}

jQuery(".widgets-list-item-remove-btn").click(function () {
  // console.log("remove widget clicked");

  var button = jQuery(this),
    tr = button.closest("tr");
  var idNum = tr.find("td.widgets-list-style-id").text().trim();
  // console.log("clicked button with id", idNum);

  /// ****** More standard way of doing AJAX Call /////////
  var nonce_remove_fundraiser_button =
    my_script_vars.nonce_remove_fundraiser_button;
  if (confirm("Are you sure you want to delete this entry?")) {
    // jQuery("#modal").css("display", "block");

    jQuery.ajax({
      url: ajaxurl,
      type: "post",
      data: {
        action: "remove_widget_action",
        id: idNum,
        nonce_remove_fundraiser_button: nonce_remove_fundraiser_button,
      },
      beforeSend: function () {
        // show loader here
        jQuery("#modal").css("display", "block");
      },
      success: function (response) {
        tr.remove();
        //jQuery("#modal").css("display", "none");
      },
      error: function (xhr) {
        //error handling
        //console.log("suppression echoué");
      },
      complete: function () {
        // hide loader here
        jQuery("#modal").css("display", "none");
      },
    });
  }
});

function copyText(elementId) {
  // Get the text content from the corresponding <span> using the provided element ID
  var textToCopy = document.getElementById(elementId).innerText;

  // Create a temporary textarea element
  var textarea = document.createElement("textarea");

  // Set the value of the textarea to the text to copy
  textarea.value = textToCopy;

  // Append the textarea to the document
  document.body.appendChild(textarea);

  // Select the text in the textarea
  textarea.select();

  // Execute the "copy" command to copy the selected text to the clipboard
  document.execCommand("copy");

  // Remove the textarea from the document
  document.body.removeChild(textarea);
  // console.log("1");
  // Optionally, provide some visual feedback to the user (e.g., an alert)
  // alert("Text copied to clipboard: " + textToCopy);
  // Change the color of the image to #51d171 using filter
  // document.getElementById('copy-icon').style.filter = 'invert(65%) sepia(50%) saturate(485%) hue-rotate(83deg) brightness(97%) contrast(93%)';
}

function copySnackBar() {
  var x = document.getElementById("snackbar");
  x.className = "show";
  setTimeout(function(){ x.className = x.className.replace("show", ""); }, 2500);
}

function refreshContent() {
  jQuery("#modal").css("display", "block");
  location.reload();
}

function applyFilter(id) {
  // Change the color of the image using filter
  document.getElementById(id).style.filter = 'invert(65%) sepia(50%) saturate(485%) hue-rotate(83deg) brightness(97%) contrast(93%)';
}

// Function to remove filter when mouse button is released
function removeFilter(id) {
  // Remove the filter
  document.getElementById(id).style.filter = 'none';
}

const tooltipTrigger = document.querySelector(".tooltip-trigger");
if(tooltipTrigger != null) {
  
  const tooltipText = tooltipTrigger.getAttribute("data-tooltip");
  const tooltipElement = tooltipTrigger.querySelector(".tooltip-api-key");
  tooltipElement.textContent = tooltipText;
  tooltipTrigger.addEventListener("mouseover", function () {
      tooltipElement.classList.add("active");
  });
  
  tooltipTrigger.addEventListener("mouseout", function () {
      tooltipElement.classList.remove("active");
  });
  
  tooltipTrigger.addEventListener("click", function () {
      const inputField = document.getElementById("fundraiser-id-input");
      const imageElement = document.getElementById("fundraiser-id-image");
      if (inputField.type === "password") {
          inputField.type = "text";
          imageElement.src = "https://whydonate.com/cdn-cgi/imagedelivery/_0vgnXOEIHPwLg2E52a7gg/shared/visibilityOnPrimary/public";
          tooltipElement.textContent = document.getElementById('hide-api-key').innerText;;
      } else {
          inputField.type = "password";
          imageElement.src = "https://whydonate.com/cdn-cgi/imagedelivery/_0vgnXOEIHPwLg2E52a7gg/shared/visibilityOff/public";
          tooltipElement.textContent = document.getElementById('show-api-key').innerText;
      }
  });
  
}  

function getValidLanguageCode(fullLocale) {
    var languageCode = fullLocale.substr(0, 2);

    switch (languageCode) {
        case 'en':
        case 'fr':
        case 'es':
        case 'de':
        case 'nl':
            // languageCode is one of the specified values, no need to change anything
            break;
        default:
            // languageCode is not one of the specified values, set it to "en" (English)
            languageCode = 'en';
            break;
    }

    return languageCode;
}

document.addEventListener('DOMContentLoaded', function() {
  // Debugging: Check if localeData is available
  // console.log("localeData:", localeData);

  if (typeof localeData !== 'undefined' && localeData.locale) {
    var validLanguageCode = getValidLanguageCode(localeData.locale);
    localStorage.setItem('locale', validLanguageCode);
  }
});
