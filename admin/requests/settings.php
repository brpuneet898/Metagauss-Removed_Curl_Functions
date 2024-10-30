<?php

namespace BuddyBot\Admin\Requests;

final class Settings extends \BuddyBot\Admin\Requests\MoRoot
{
    public function requestJs()
    {
        $this->pageVarsJs();
        $this->sectionToggleJs();
        $this->saveOptionsJs();
        $this->getGeneralOptionsJs();
        $this->toggleErrorsJs();
        $this->getOpenAiApiKeyJs();
    }

    protected function pageVarsJs()
    {
        echo '
        const optionsData = {};
        let dataErrors = [];
        let errorMessage = "";
        ';
    }

    private function sectionToggleJs()
    {
        echo '
        sectionToggle();
        $("#mgao-settings-section-select").change(sectionToggle);

        function sectionToggle() {
            $("#buddybot-settings-section-options > tbody").html("");
            $("#buddybot-settings-section-options-loader").removeClass("visually-hidden");
            let section = $("#mgao-settings-section-select").val();

            const data = {
                "action": "getOptions",
                "section": section,
                "nonce": "' . esc_js(wp_create_nonce('get_options')) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                $("#buddybot-settings-section-options-loader").addClass("visually-hidden");
                $("#buddybot-settings-section-options > tbody").html(response);
            });
        }
        ';
    }

    private function saveOptionsJs()
    {
        echo '
        $("#buddybot-settings-update-btn").click(saveOptions);

        function saveOptions() {

            const section = $("#mgao-settings-section-select").val();
            getGeneralOptions();

            if (dataErrors.length > 0) {
                displayErrors();
                disableFields(false);
                hideBtnLoader("#mgao-chatbot-save-btn");
                return;
            }

            const data = {
                "action": "saveSettings",
                "options_data": optionsData,
                "section": section,
                "nonce": "' . esc_js(wp_create_nonce('save_settings')) . '"
            };

            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    location.replace("' . esc_url(admin_url()) . 'admin.php?page=buddybot-settings&section=' . '" + section + "&success=1");
                } else {
                    $("#buddybot-settings-error-message").html(response.message);
                    dataErrors = response.errors;
                    displayErrors();
                }

                disableFields(false);
                hideBtnLoader("#mgao-chatbot-save-btn");
            });
        }
        ';
    }

    private function getGeneralOptionsJs()
    {
        echo '
        function getGeneralOptions() {
            if ($("#mgao-settings-section-select").val() === "general") {
                optionsData["openai_api_key"] = getOpenAiApiKey();
            }
        }
        ';
    }

    protected function toggleErrorsJs()
    {
        echo '
        displayErrors();
        function displayErrors() {
            let errorsHtml = "";

            if (dataErrors.length === 0) {
                $("#buddybot-settings-errors").hide();
                return;
            }

            $("#buddybot-settings-success").hide();
            $.each(dataErrors, function(index, value){
                errorsHtml = errorsHtml + "<li>" + value + "</li>";
            });

            $("#buddybot-settings-errors-list").html(errorsHtml);
            $("#buddybot-settings-errors").show();
            dataErrors.length = 0;
        }
        ';
    }

    private function getOpenAiApiKeyJs()
    {
        echo '
        function getOpenAiApiKey() {
            let key = $("#buddybot-settings-openai-api-key").val();
            key = $.trim(key);

            if (key === "") {
                dataErrors.push("' . esc_html(__('OpenAI API Key cannot be empty.', 'buddybot')) . '"); 
            }

            return key;
        }
        ';
    }
}