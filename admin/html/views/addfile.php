<?php

namespace BuddyBot\Admin\Html\Views;

final class AddFile extends \BuddyBot\Admin\Html\Views\MoRoot
{
    public function getHtml()
    {
        $heading = __('Add File', 'megaform-openai');
        $this->pageHeading($heading);
        $this->uploadArea();
    }

    private function uploadArea()
    {
        wp_enqueue_media();

        echo '<div class="p-4 border border bg-light rounded-3 w-50">';

        echo '<div id="buddybot-file-output" class="small mb-3">';
        echo '</div>';

        echo '<input type="hidden" id="buddybot-file-selected" class="form-control mb-2">';

        echo '<button class="btn btn-outline-dark btn-sm me-1" type="button" id="buddybot-file-select-btn">';
        echo esc_html(__('Select File', 'buddybot'));
        echo '</button>';

        echo '<button class="btn btn-dark btn-sm ms-1" type="button" id="buddybot-file-upload-btn">';
        echo esc_html(__('Upload File', 'buddybot'));
        echo '</button>';

        echo '</div>';
    }

    public function pageJs()
    {
        echo '
        <script>
        $(document).ready(function(){' . PHP_EOL;

        $this->openMediaWindow();
        
        echo 
        PHP_EOL . '});
        </script>';
    }

    private function openMediaWindow()
    {
        echo '
        $("#buddybot-file-select-btn").click(function(e) {

            e.preventDefault();

            let file_frame;

            if(file_frame){
                file_frame.open();
                return;
            }
            
            file_frame = wp.media({
                title: "Select a File to Upload",
                button: {
                    text: "Use This File",
                },
                multiple: false 
            });

            file_frame.open();

            file_frame.on("select",function() {
                let selection =  file_frame.state().get("selection").first();
                $("#buddybot-file-selected").val(selection.id);
                $("#buddybot-file-output").html("You selected file ID " + selection.id);
             });

        });
        ';
    }
}