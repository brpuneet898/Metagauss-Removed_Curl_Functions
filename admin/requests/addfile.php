<?php

namespace BuddyBot\Admin\Requests;

class AddFile extends \BuddyBot\Admin\Requests\MoRoot
{
    public function requestJs()
    {
        $this->addFileJs();
    }

    private function addFileJs()
    {
        $nonce = wp_create_nonce('add_file');
        
        echo '
        $("#buddybot-file-upload-btn").click(addFile);

        function addFile() {

            let fileId = $("#buddybot-file-selected").val();

            const data = {
                "action": "addFile",
                "file_id": fileId,
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                alert(response);
                response = JSON.parse(response);

                if (response.success) {
                    $("#buddybot-file-output").html(response.html);
                }
            });
        }
        ';
    }
}