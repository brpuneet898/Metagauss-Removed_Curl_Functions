<?php

namespace BuddyBot\Admin\Requests;

final class DefaultBuddyBotWizard extends \BuddyBot\Admin\Requests\MoRoot
{
    public function requestJs()
    {
        $this->toggleAlertsJs();
        $this->setProgressBarJs();
        $this->startWizardBtnJs();
        $this->isLocalFileWritableJs();
    }

    protected function toggleAlertsJs()
    {
        echo '

        function hideAlerts() {
            $("#buddybot-default-wizard-alerts").children("[role=alert]").addClass("visually-hidden");
        }
        
        function showAlert(status = true, txt = "") {
            
            hideAlerts();
            
            if (status) {
                $("#buddybot-default-wizard-success-alert").removeClass("visually-hidden");
                $("#buddybot-default-wizard-success-alert").text(txt);
            } else {
                $("#buddybot-default-wizard-failure-alert").removeClass("visually-hidden");
                $("#buddybot-default-wizard-failure-alert").text(txt);
                $(".progress-bar").addClass("bg-danger");
            }
        }
        ';
    }

    private function setProgressBarJs()
    {
        echo '
        function setProgressBar(width = 0) {
            $(".progress-bar").css("width", width + "%");
        }
        ';
    }

    private function startWizardBtnJs()
    {
        echo '
        $("#buddybot-default-wizard-start-btn").click(startWizardBtn);

        function startWizardBtn() {
            hideAlerts();
            setProgressBar(5);
            isLocalFileWritable();
        }
        ';
    }

    private function isLocalFileWritableJs()
    {
        echo '
        function isLocalFileWritable() {

            let dataTypes = selectedDataTypes();

            if (dataTypes.length === 0) {
                return;
            }

            const data = {
                "action": "isLocalFileWritable",
                "data_types": dataTypes,
                "nonce": "' .  esc_js(wp_create_nonce('is_local_file_writable')) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                showAlert(response.success, response.message);
                if (response.success) {
                    setProgressBar(10);
                    addDataToFile(dataTypes);
                };
            });
        }
        
        function selectedDataTypes() {
            const dataTypes = [];
            
            $("#buddybot-default-wizard-data-types-selection").find("input[type=checkbox]").each(function() {
                if ($(this).prop("checked")) {
                    dataTypes.push($(this).val());
                }
            });

            return dataTypes;
        }
        ';
    }
}