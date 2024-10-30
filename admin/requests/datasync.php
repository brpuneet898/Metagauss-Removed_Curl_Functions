<?php

namespace BuddyBot\Admin\Requests;

class DataSync extends \BuddyBot\Admin\Requests\MoRoot
{
    public function requestJs()
    {
        $this->checkFileStatusJs();
        $this->syncBtnJs();
        $this->isFileWritableJs();
        $this->addDataToFileJs();
        $this->transferDataFileJs();
    }

    private function checkFileStatusJs()
    {
        $nonce = wp_create_nonce('check_file_status');
        echo '
        $(".list-group-item").each(function(){
            let listItem = $(this);
            let dataType = listItem.attr("data-buddybot-type");
            let fileId = listItem.attr("data-buddybot-remote_file_id");

            if (fileId == 0) {
                listItem.find(".buddybot-remote-file-status").text("Not syncronized.");
            } else {
                const data = {
                    "action": "checkFileStatus",
                    "file_id": fileId,
                    "nonce": "' . esc_js($nonce) . '"
                };
      
                $.post(ajaxurl, data, function(response) {
                    response = JSON.parse(response);
                    listItem.find(".buddybot-remote-file-status").text(response.message);   
                });
            }

        });
        ';
    }

    private function syncBtnJs()
    {
        echo '
        $(".buddybot-sync-btn").click(syncBtn);
        
        function syncBtn() {
            let dataType = $(this).attr("data-buddybot-type");
            isFileWritable(dataType);
        }
        ';
    }

    private function isFileWritableJs()
    {
        $nonce = wp_create_nonce('is_file_writable');
        echo '
        function isFileWritable(dataType) {
            const data = {
                "action": "isFileWritable",
                "data_type": dataType,
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);

                if (response.success) {
                    addDataToFile(dataType);
                };

                $(".buddybot-msgs").append(response.message);
            });
        }
        ';
    }

    private function addDataToFileJs()
    {
        $nonce = wp_create_nonce('add_data_to_file');
        echo '
        function addDataToFile(dataType) {
            const data = {
                "action": "addDataToFile",
                "data_type": dataType,
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);

                if (response.success) {
                    transferDataFile(dataType);
                }

                $(".buddybot-msgs").append(response.message);
            });
        }
        ';
    }

    private function transferDataFileJs()
    {
        $nonce = wp_create_nonce('transfer_data_file');
        echo '
        function transferDataFile(dataType) {
            const data = {
                "action": "transferDataFile",
                "data_type": dataType,
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                $(".buddybot-msgs").append(response.message);
            });
        }
        ';
    }
}