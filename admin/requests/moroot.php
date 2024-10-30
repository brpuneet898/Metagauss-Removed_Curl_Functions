<?php

namespace BuddyBot\Admin\Requests;

class MoRoot extends \BuddyBot\Admin\MoRoot
{
    public function requestsJs()
    {
        echo '
        <script>
        $(document).ready(function(){' . PHP_EOL;

        $this->showAlertJs();
        $this->hideAlertJs();
        $this->loaderBtnJs();
        $this->disableFieldsJs();
        $this->requestJs();
        
        echo 
        PHP_EOL . '});
        </script>';
    }

    protected function showAlertJs()
    {
        echo '
        function showAlert(message = "") {
            $("#buddybot-alert-container").html(message);
            $("#buddybot-alert-container").show();
        }
        ';
    }

    protected function hideAlertJs()
    {
        echo '
        function hideAlert(message = "") {
            $("#buddybot-alert-container").html("");
            $("#buddybot-alert-container").hide();
        }
        ';
    }

    protected function loaderBtnJs()
    {
        echo '
        function showBtnLoader(btnId) {
            $(btnId).prop("disabled", true);
            $(btnId).children(".buddybot-loaderbtn-label").addClass("visually-hidden");
            $(btnId).children(".buddybot-loaderbtn-spinner").removeClass("visually-hidden");
        }

        function hideBtnLoader(btnId) {
            $(btnId).prop("disabled", false);
            $(btnId).children(".buddybot-loaderbtn-label").removeClass("visually-hidden");
            $(btnId).children(".buddybot-loaderbtn-spinner").addClass("visually-hidden");
        }
        ';
    }

    protected function disableFieldsJs()
    {
        echo '
        function disableFields(isDisabled) {
            $(".buddybot-item-field").each(function(){
                $(this).prop("disabled", isDisabled);
            });
        }
        ';
    }

    protected function requestJs()
    {

    }
}