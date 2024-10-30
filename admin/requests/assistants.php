<?php

namespace BuddyBot\Admin\Requests;

final class Assistants extends \BuddyBot\Admin\Requests\MoRoot
{
    public function requestJs()
    {
        $this->getAssistantsJs();
        $this->deleteAssistantJs();
    }

    private function getAssistantsJs()
    {
        $nonce = wp_create_nonce('get_assistants');
        echo '
        getAssistants();
        function getAssistants() {

            const data = {
                "action": "getAssistants",
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    $("tbody").html(response.html);
                } else {
                    showAlert(response.message);
                }
            });
        }
        ';
    }

    // private function deleteAssistantJs()
    // {
    //     $nonce = wp_create_nonce('delete_assistant');
    //     echo '
    //     $(".buddybot-org-assistants-table").on("click", ".buddybot-listbtn-assistant-delete", function(){
            
    //         let row = $(this).parents("tr");
    //         let fileId = row.attr("data-buddybot-itemid");

    //         row.find(".buddybot-list-spinner").removeClass("visually-hidden");

    //         const data = {
    //             "action": "deleteOrgFile",
    //             "file_id": fileId,
    //             "nonce": "' . esc_js($nonce) . '"
    //         };
  
    //         $.post(ajaxurl, data, function(response) {
    //             response = JSON.parse(response);

    //             if (response.success) {
    //                 getOrgFiles();
    //             } else {
    //                 alert("Failed to delete file " + fileId);
    //                 row.find(".buddybot-list-spinner").addClass("visually-hidden");
    //             }
    //         });
    //     });
    //     ';
    // }

    // Code Added by Puneet

    private function deleteAssistantJs()
    {
        $nonce = wp_create_nonce('delete_assistant');
        echo '
        $(".buddybot-org-assistants-table").on("click", ".buddybot-listbtn-assistant-delete", function(){
            
            let row = $(this).parents("tr");
            let assistantId = row.attr("data-buddybot-itemid");

            row.find(".buddybot-list-spinner").removeClass("visually-hidden");

            const data = {
                "action": "deleteAssistant",
                "assistant_id": assistantId,
                "nonce": "' . esc_js($nonce) . '"
            };

            // Immediately refresh the page
            location.reload();

            // Send the AJAX request to delete the assistant
            $.post(ajaxurl, data, function(response) {
            }).fail(function(xhr, status, error) {
                console.error("AJAX request failed:", status, error);
                row.find(".buddybot-list-spinner").addClass("visually-hidden");
            });
            
            // Prevent default action of the delete button
            return false;
        });
        ';
    }

    // Code Finished By Puneet

}