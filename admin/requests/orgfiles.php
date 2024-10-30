<?php

namespace BuddyBot\Admin\Requests;

final class OrgFiles extends \BuddyBot\Admin\Requests\MoRoot
{
    public function requestJs()
    {
        $this->getOrgFilesJs();
        $this->deleteFileJs();
    }

    private function getOrgFilesJs()
    {
        $nonce = wp_create_nonce('get_org_files');
        echo '
        getOrgFiles();
        function getOrgFiles() {

            const data = {
                "action": "getOrgFiles",
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                $("tbody").html(response);
            });
        }
        ';
    }

    private function deleteFileJs()
    {
        $nonce = wp_create_nonce('delete_org_file');
        echo '
        $(".buddybot-org-files-table").on("click", ".buddybot-listbtn-file-delete", function(){
            
            let row = $(this).parents("tr");
            let fileId = row.attr("data-buddybot-itemid");

            row.find(".buddybot-list-spinner").removeClass("visually-hidden");

            const data = {
                "action": "deleteOrgFile",
                "file_id": fileId,
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);

                if (response.success) {
                    getOrgFiles();
                } else {
                    alert("Failed to delete file " + fileId);
                    row.find(".buddybot-list-spinner").addClass("visually-hidden");
                }
            });
        });
        ';
    }
}