<?php

namespace BuddyBot\Admin\Html\Views;

class Playground extends \BuddyBot\Admin\Html\Views\MoRoot
{
    public function getHtml()
    {
        $heading = __('Playground', 'buddybot');
        $this->pageHeading($heading);
        $this->playgroundContainer();
    }

    private function playgroundContainer()
    {
        echo '<div class="row border small">';
        
        $this->playgroundOptions();
        $this->threadsContainer();
        $this->messagesContainer();
        
        echo '</div>';
    }

    private function playgroundOptions()
    {
        echo '<div id="buddybot-playground-options-container" class="col-md-12 d-flex border-bottom">';
        
        echo '<div id="buddybot-playground-options-select-assistant" class="p-3">';
        echo '<label class="">';
        esc_html_e('Assistant', 'buddybot');
        echo '<label>';
        echo '<select id="buddybot-playground-assistants-list" class="form-select ms-2">';
        echo '<option disabled>' . esc_html__('Loading...', 'buddybot') . '</option>';
        echo '</select>';
        echo '</div>';
        
        echo '<div id="buddybot-playground-options-select-user" class="p-3">';
        echo '<label class="">';
        esc_html_e('User', 'buddybot');
        echo '<label>';
        echo '<select id="" class="ms-2">';
        $this->getUsers();
        echo '</select>';
        echo '</div>';

        echo '</div>';
    }

    private function threadsContainer()
    {
        echo '<div id="buddybot-playground-threads-container" class="col-md-2 flex-column border-end bg-light">';
        
        echo '<div id="buddybot-playground-threads-header" class="fs-6 p-4">';
        esc_html_e('History', 'buddybot');
        echo '</div>';

        $this->threatIdInput();
        $this->runIdInput();
        
        echo '<div id="buddybot-playground-threads-list" class="p-3" style="height: 750px; overflow-y: auto;">';
        $this->threadList();
        echo '</div>';
        
        echo '</div>';
    }

    private function messagesContainer()
    {
        echo '<div class="col-md-10 flex-column">';
        $this->threadOperations();
        $this->messagesListContainer();
        $this->messagesStatusBar();
        $this->newMessageContainer();
        echo '</div>';
    }

    private function threadOperations()
    {
        echo '<div id="buddybot-playground-thread-operations" class="d-flex justify-content-between p-3">';
        $this->loadMessagesBtn();
        $this->tokensDisplay();
        echo '<input id="buddybot-playground-first-message-id" type="hidden">';
        echo '<input id="buddybot-playground-last-message-id" type="hidden">';
        echo '<input id="buddybot-playground-has-more-messages" type="hidden">';
        $this->deleteThreadBtn();
        echo '</div>';
    }

    private function loadMessagesBtn()
    {
        echo '<button id="buddybot-playground-past-messages-btn" type="button" class="btn btn-outline-dark btn-sm" style="opacity:0;">';
        $this->moIcon('cached');
        // esc_html_e('Delete Thread', 'buddybot');
        echo '</button>';
    }

    private function tokensDisplay()
    {
        echo '<span id="mgao-playground-tokens-display" class="small text-muted">';
        echo '</span>';
    }

    private function deleteThreadBtn()
    {
        echo '<button id="buddybot-playground-delete-thread-btn" type="button" class="btn btn-outline-danger btn-sm">';
        $this->moIcon('delete');
        // esc_html_e('Delete Thread', 'buddybot');
        echo '</button>';
    }

    private function messagesListContainer()
    {
        echo '<div id="buddybot-playground-messages-list" class="p-3" style="height:600px; overflow-y: auto;">';
        echo '</div>';
    }

    private function openAiBadge()
    {
        $badge_url = $this->config->getRootUrl() . 'admin/html/images/third-party/openai/openai-dark-badge.svg';
        echo '<div class="text-center my-2">';
        echo '<img width="150" src="' . esc_url($badge_url) . '">';
        echo '</div>';
    }

    private function messagesStatusBar()
    {
        echo '<div class="">';
        echo '<div id="buddybot-playground-message-status" class="text-center small">';
        $this->statusBarMessage('creating-thread', __('Starting new conversation', 'buddybot'));
        echo '</div>';
        $this->openAiBadge();
        echo '</div>';
    }

    private function statusBarMessage($attr, $text) {
        echo '<span data-buddybot-message="' . esc_attr($attr) . '">';
        echo esc_html($text);
        echo '</span>';
    }

    private function newMessageContainer()
    {
        echo '<div class="d-flex align-items-center mt-auto">';
        $this->attachFileBtn();
        $this->messageTextArea();
        $this->sendMessageBtn();
        echo '</div>';
    }

    private function attachFileBtn()
    {
        wp_enqueue_media();
        echo '<div class="p-2">';
        echo '<button id="mgao-playground-message-file-btn" type="button"';
        echo 'class="btn btn-light border btn-sm rounded-circle p-2">';
        $this->moIcon('attach_file');
        echo '</button>';
        echo '</div>';
    }

    private function messageTextArea()
    {
        echo '<div class="p-2 flex-fill">';
        
        echo '<div id="buddybot-playground-attachment-wrapper" class="rounded p-2 mb-2 border small d-flex justify-content-between align-items-center visually-hidden">';

        echo '<div><img id="buddybot-playground-attachment-icon" src="" width="12" class="me-2">';
        echo '<span id="buddybot-playground-attachment-name"></span></div>';

        echo '<div role="button" id="buddybot-playground-remove-attachment-btn">';
        $this->moIcon('close');
        echo '</div>';

        echo '<input id="buddybot-playground-attachment-url" type="text">';
        echo '<input id="buddybot-playground-attachment-mime" type="text">';

        echo '</div>';

        echo '<textarea id="mgao-playground-new-message-text" data-buddybot-threadid="" class="w-100 form-control" rows="5">';
        echo '</textarea>';
        
        echo '</div>';
    }

    private function sendMessageBtn()
    {
        echo '<div class="p-2">';
        echo '<button id="mgao-playground-send-message-btn" type="button"';
        echo 'class="btn btn-dark">';
        esc_html_e('Send', 'buddybot');
        echo '</button>';
        echo '</div>';
    }

    private function getUsers()
    {
        $users = get_users(array('fields' => array('display_name', 'id')));
        $current_user_id = get_current_user_id();

        foreach ($users as $user) {
            $selected = '';

            if ($user->id == $current_user_id) {
                $selected = ' selected';
            }

            echo '<option' . esc_attr($selected) . '>' . esc_html($user->display_name) . '</option>';
        }
    }

    private function threatIdInput()
    {
        $thread_id = '';

        if (!empty($_GET['thread_id'])) {
            $thread_id = $_GET['thread_id'];
        }

        echo '<input id="mgao-playground-thread-id-input" ';
        echo 'type="hidden" value="' . esc_attr($thread_id) . '">';
    }

    private function runIdInput()
    {
        echo '<input id="mgao-playground-run-id-input" ';
        echo 'type="hidden" value="">';
    }

    private function threadList()
    {
        $response = $this->sql->getThreadsByUserId();

        if ($response['success'] === false) {
            esc_html_e('There was an error while fetching threads.', 'buddybot');
            echo ' ';
            echo esc_html($response['message']);
            return;
        }

        if (empty($response['result'])) {
            echo '<span class="text-muted">';
            esc_html_e('No previous conversations.', 'buddybot');
            echo '</span>';
            return;
        }

        foreach ($response['result'] as $thread) {
            
            $label = $thread->thread_name;

            if (empty($label)) {
                $label = $thread->thread_id;
            }

            echo '<div class="buddybot-playground-threads-list-item mb-2 p-2 text-truncate small" data-buddybot-threadid="' . esc_attr($thread->thread_id) . '" role="button">';
            echo esc_html($label);
            echo '</div>';
        }
    }

    public function pageJs()
    {
        echo '
        <script>
        $(document).ready(function(){' . PHP_EOL;

        $this->openMediaWindowJs();
        $this->selectAttachmentJs();
        
        echo 
        PHP_EOL . '});
        </script>';
    }

    private function openMediaWindowJs()
    {
        $title = __('Select a file to attach to your message', 'buddybot');
        $btn_label = __('Attach To Message', 'buddybot');
        
        echo '
        $("#mgao-playground-message-file-btn").click(function(e) {

            e.preventDefault();

            let file_frame;

            if (file_frame) {
                file_frame.open();
                return;
            }
            
            file_frame = wp.media({
                title: "' . esc_html($title) . '",
                button: {
                    text: "' . esc_html($btn_label) . '",
                },
                multiple: false 
            });

            file_frame.open();

            file_frame.on("select",function() {
                let attachment =  file_frame.state().get("selection").first();
                selectAttachment(attachment)
             });

        });
        ';
    }

    private function selectAttachmentJs()
    {   
        echo '
        function selectAttachment(attachment) {
            if (
                typeof attachment === "object" &&
                !Array.isArray(attachment) &&
                attachment !== null
            ) {
                $("#buddybot-playground-attachment-wrapper").removeClass("visually-hidden");
                attachment = JSON.parse(JSON.stringify(attachment));
                $("#mgao-playground-message-file-btn").attr("data-buddybot-fileid", attachment.id);
                $("#buddybot-playground-attachment-icon").attr("src", attachment.icon);
                $("#buddybot-playground-attachment-name").text(attachment.filename);
                $("#buddybot-playground-attachment-url").val(attachment.url);
                $("#buddybot-playground-attachment-mime").val(attachment.mime);
            } else {
                deselectAttachment();
            }
        }

        $("#buddybot-playground-remove-attachment-btn").click(deselectAttachment);
        
        function deselectAttachment() {
            $("#buddybot-playground-attachment-wrapper").addClass("visually-hidden");
            $("#mgao-playground-message-file-btn").attr("data-buddybot-fileid", "");
            $("#buddybot-playground-attachment-icon").attr("src", "");
            $("#buddybot-playground-attachment-name").text("");
            $("#buddybot-playground-attachment-url").val("");
            $("#buddybot-playground-attachment-mime").val("");
        }
        ';
    }
}