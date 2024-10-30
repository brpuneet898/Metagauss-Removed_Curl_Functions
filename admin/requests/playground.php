<?php

namespace BuddyBot\Admin\Requests;

final class Playground extends \BuddyBot\Admin\Requests\MoRoot
{
    public function requestJs()
    {
        $this->setVarsJs();
        $this->disableMessageJs();
        $this->updateStatusJs();
        $this->getAssistantsJs();
        $this->sendMessageBtnJs();
        $this->createThreadJs();
        $this->createMessageJs();
        $this->createRunJs();
        $this->retrieveRunJs();
        $this->getAssistantResponseJs();
        $this->listMessagesJs();
        $this->storeThreadInfoJs();
        $this->scrollToMessageJs();
        $this->selectThreadJs();
        $this->pastMessagesJs();
        $this->toggleThreadBtnsJs();
        $this->toggleDeleteThreadBtnJs();
        $this->togglePastMessagesBtnJs();
        $this->deleteThreadBtnJs();
        $this->updateThreadNameJs();
    }

    private function setVarsJs()
    {
        echo '
        let checkRun = "";
        const gettingAssistants = "' . esc_html__('Getting list of assistants.', 'buddybot') . '";
        const assistantsUpdated = "' . esc_html__('Assistants updated.', 'buddybot') . '";
        const messageEmpty = "' . esc_html__('Cannot send empty message.', 'buddybot') . '";
        const creatingThread = "' . esc_html__('Starting new conversation.', 'buddybot') . '";
        const threadCreated = "' . esc_html__('Conversation started.', 'buddybot') . '";
        const sendingMessage = "' . esc_html__('Sending message to the Assistant.', 'buddybot') . '";
        const messageSent = "' . esc_html__('Message sent.', 'buddybot') . '";
        const creatingRun = "' . esc_html__('Asking assistant to read your message.', 'buddybot') . '";
        const runCreated = "' . esc_html__('Assistant is processing your message.', 'buddybot') . '";
        const retrievingRun = "' . esc_html__('Checking response to your message.', 'buddybot') . '";
        const runCancelled = "' . esc_html__('The process was aborted.', 'buddybot') . '";
        const gettingResponse = "' . esc_html__("Fetching Assistant response.", 'buddybot') . '";
        const responseUpdated = "' . esc_html__("Assistant response received.", 'buddybot') . '";
        const gettingThreadMessages = "' . esc_html__("Fetching conversation data.", 'buddybot') . '";
        const threadMessagesUpdated = "' . esc_html__("Conversation data updated.", 'buddybot') . '";
        const gettingPastMessages = "' . esc_html__("Loading previous messages.", 'buddybot') . '";
        const pastMessagesUpdated = "' . esc_html__("Loaded previous messages.", 'buddybot') . '";
        const deletingThread = "' . esc_html__("Deleting conversation.", 'buddybot') . '";
        const threadDeleted = "' . esc_html__("Conversation deleted successfully!", 'buddybot') . '";
        ';
    }

    private function disableMessageJs()
    {
        echo '
        function disableMessage(propState = true) {
            $("#mgao-playground-send-message-btn").prop("disabled", propState);
            $("#mgao-playground-new-message-text").prop("disabled", propState);
        }
        ';
    }

    private function updateStatusJs()
    {
        echo '
        function updateStatus(text) {
            $("#buddybot-playground-message-status").children("span").html(text);
        }
        ';
    }

    private function getAssistantsJs()
    {
        $nonce = wp_create_nonce('get_assistants');
        echo '
        getAssistants();
        function getAssistants() {

            disableMessage();
            updateStatus(gettingAssistants);

            const data = {
                "action": "getAssistantOptions",
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);

                if (response.success) {
                    $("#buddybot-playground-assistants-list").html(response.html);
                    updateStatus(assistantsUpdated);
                    disableMessage(false);
                } else {
                    updateStatus(response.message);
                }
            });
        }
        ';
    }

    private function sendMessageBtnJs()
    {
        echo '
        $("#mgao-playground-send-message-btn").click(sendMessage);
        
        $("#mgao-playground-new-message-text").keypress(function(e) {
            let key = e.key;
            if (key === "Enter") {
                e.preventDefault();
                sendMessage();
            }
        });

        function sendMessage() {
            const message = $("#mgao-playground-new-message-text").val();

            if (message === "") {
                updateStatus(messageEmpty);
                return;
            }

            disableMessage();
            const threadId = $("#mgao-playground-thread-id-input").val();
            if (threadId === "") {
                createThread();
            } else {
                addMessage();
            }
        }
        ';
    }

    // private function createThreadJs()
    // {
    //     $nonce = wp_create_nonce('create_thread');
    //     echo '
    //     function createThread() {
            
    //         disableMessage();
    //         updateStatus(creatingThread);
            
    //         const data = {
    //             "action": "createThread",
    //             "nonce": "' . esc_js($nonce) . '"
    //         };
  
    //         $.post(ajaxurl, data, function(response) {
    //             response = JSON.parse(response);
    //             if (response.success) {
    //                 updateStatus(threadCreated);
    //                 $("#mgao-playground-thread-id-input").val(response.result.id);
    //                 addMessage();
    //             } else {
    //                 disableMessage(false);
    //                 updateStatus(response.message);
    //             }
    //         });
    //     }
    //     ';
    // }

    private function createThreadJs()
    {
        $nonce = wp_create_nonce('create_thread');
        echo '
        function createThread() {
            
            disableMessage();
            updateStatus(creatingThread);
            
            const data = {
                "action": "createThread",
                "nonce": "' . esc_js($nonce) . '"
            };

            $.post(ajaxurl, data, function(response) {
                try {
                    response = JSON.parse(response);
                    if (response.success) {
                        updateStatus(threadCreated);
                        if (response.result && response.result.id) {
                            $("#mgao-playground-thread-id-input").val(response.result.id);
                            addMessage();
                        } else {
                            disableMessage(false);
                            updateStatus("Thread created but no ID returned.");
                        }
                    } else {
                        disableMessage(false);
                        updateStatus(response.message);
                    }
                } catch (e) {
                    disableMessage(false);
                    updateStatus("An error occurred while processing the response.");
                }
            });
        }
        ';
    }


    private function createMessageJs()
    {
        $nonce = wp_create_nonce('create_message');
        echo '
        function addMessage() {

            disableMessage();
            updateStatus(sendingMessage);

            const threadId = $("#mgao-playground-thread-id-input").val();
            const message = $("#mgao-playground-new-message-text").val();

            const data = {
                "action": "createMessage",
                "thread_id": threadId,
                "message": message,
                "file_url": $("#buddybot-playground-attachment-url").val(),
                "file_mime": $("#buddybot-playground-attachment-mime").val(),
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    updateStatus(messageSent);
                    $("#mgao-playground-new-message-text").val("");
                    $("#buddybot-playground-messages-list").append(response.html);
                    $("#buddybot-playground-first-message-id").val(response.result.id);
                    updateThreadName(message);
                    scrollToBottom(response.result.id);
                    createRun();
                } else {
                    disableMessage(false);
                    updateStatus(response.message);
                }
            });
        }
        ';
    }

    private function createRunJs()
    {
        $nonce = wp_create_nonce('create_run');
        echo '
        function createRun() {

            disableMessage();
            updateStatus(creatingRun);

            const threadId = $("#mgao-playground-thread-id-input").val();
            const assistantId = $("#buddybot-playground-assistants-list").val();

            const data = {
                "action": "createRun",
                "thread_id": threadId,
                "assistant_id": assistantId,
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    updateStatus(runCreated);
                    $("#mgao-playground-run-id-input").val(response.result.id);
                    checkRun = setInterval(retrieveRun, 2000);
                } else {
                    disableMessage(false);
                    updateStatus(response.message);
                }
            });
        }
        ';
    }

    private function retrieveRunJs()
    {
        $nonce = wp_create_nonce('retrieve_run');
        echo '
        function retrieveRun() {

            disableMessage();
            updateStatus(retrievingRun);

            const threadId = $("#mgao-playground-thread-id-input").val();
            const runId = $("#mgao-playground-run-id-input").val();

            const data = {
                "action": "retrieveRun",
                "thread_id": threadId,
                "run_id": runId,
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {

                response = JSON.parse(response);
                
                if (response.success) {
                    
                    switch (response.result.status) {
                        
                        case "completed":
                            clearInterval(checkRun);
                            getAssistantResponse();
                            $("#mgao-playground-tokens-display").html(response.tokens);
                            break;
                        
                        case "failed":
                            clearInterval(checkRun);
                            disableMessage(false);
                            updateStatus(
                                "<span class=text-danger>" +
                                response.result.last_error.code + ": " +
                                response.result.last_error.message +
                                "</span>"
                            );
                            break;

                        case "cancelled":
                        case "cancelling":
                            clearInterval(checkRun);
                            disableMessage(false);
                            updateStatus(runCancelled);
                            break;
                        
                        case "requires_action":
                            clearInterval(checkRun);
                            getAssistantResponse();
                            break;
                    }

                } else {
                    disableMessage(false);
                    updateStatus(response.message);
                    clearInterval(checkRun);
                }
            });
        }
        ';
    }

    private function getAssistantResponseJs()
    {
        $nonce = wp_create_nonce('list_messages');
        echo '
        function getAssistantResponse() {

            disableMessage();
            updateStatus(gettingResponse);

            const threadId = $("#mgao-playground-thread-id-input").val();

            const data = {
                "action": "listMessages",
                "thread_id": threadId,
                "before": $("#buddybot-playground-first-message-id").val(),
                "limit": 20,
                "order": "desc",
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {

                response = JSON.parse(response);

                if (response.success) {
                    updateStatus(responseUpdated);
                    $("#buddybot-playground-messages-list").append(response.html);
                    $("#buddybot-playground-first-message-id").val(response.result.first_id);
                    scrollToBottom(response.result.first_id);
                } else {
                    updateStatus(response.message);
                }

                disableMessage(false);
                toggleThreadBtns();
            });
        }
        ';
    }

    private function listMessagesJs()
    {
        $nonce = wp_create_nonce('list_messages');
        echo '
        function listMessages() {

            disableMessage();
            updateStatus(gettingThreadMessages);

            const threadId = $("#mgao-playground-thread-id-input").val();

            const data = {
                "action": "listMessages",
                "thread_id": threadId,
                "limit": 5,
                "order": "desc",
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                
                response = JSON.parse(response);

                if (response.success) {
                    updateStatus(threadMessagesUpdated);
                    $("#buddybot-playground-messages-list").append(response.html);
                    storeThreadInfo(response.result);
                    scrollToBottom(response.result.first_id);
                } else {
                    updateStatus(response.message);
                }

                disableMessage(false);
                toggleThreadBtns();
            });
        }
        ';
    }

    private function storeThreadInfoJs()
    {
        echo '
        function storeThreadInfo(thread)
        {
            $("#buddybot-playground-first-message-id").val(thread.first_id);
            $("#buddybot-playground-last-message-id").val(thread.last_id);
            $("#buddybot-playground-has-more-messages").val(thread.has_more);
        }
        ';
    }

    private function scrollToMessageJs()
    {
        echo '
        function scrollToBottom(id) {
            let height = $("#" + id).outerHeight();
            $("#buddybot-playground-messages-list").animate({
                scrollTop: $("#buddybot-playground-messages-list")[0].scrollHeight - height - 200
            }, 1000);
        }

        function scrollToTop() {
            $("#buddybot-playground-messages-list").animate({
                scrollTop: 0
            }, 1000);
        }
        ';
    }

    private function selectThreadJs()
    {
        echo '
        $("#buddybot-playground-threads-list").on("click", ".buddybot-playground-threads-list-item", function() {
            
            const threadId = $(this).attr("data-buddybot-threadid");
            const highlightClass = "fw-bold text-primary";
            
            
            $("#mgao-playground-tokens-display").html("");
            $("#buddybot-playground-messages-list").html("");
            $("#mgao-playground-thread-id-input").val(threadId);
            $(".buddybot-playground-threads-list-item.fw-bold.text-primary").removeClass(highlightClass);
            $(this).addClass(highlightClass);
            
            listMessages();
        });
        ';
    }

    private function pastMessagesJs()
    {
        $nonce = wp_create_nonce('list_messages');

        echo '
        $("#buddybot-playground-past-messages-btn").click(function(){

            updateStatus(gettingPastMessages);
            disableMessage(true);
            $("#buddybot-playground-past-messages-btn").children("span").addClass("buddybot-rotate-icon");

            const hasMore = $("#buddybot-playground-has-more-messages").val();

            if (hasMore == false) {
                return;
            }

            const firstId = $("#buddybot-playground-first-message-id").val();
            const lastId = $("#buddybot-playground-last-message-id").val();
            const threadId = $("#mgao-playground-thread-id-input").val();

            const data = {
                "action": "listMessages",
                "thread_id": threadId,
                "limit": 5,
                "after": lastId,
                "order": "desc",
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                
                response = JSON.parse(response);
                
                if (response.success) {
                    updateStatus(pastMessagesUpdated);
                    $("#buddybot-playground-messages-list").prepend(response.html);
                    storeThreadInfo(response.result);
                    scrollToTop();
                } else {
                    updateStatus(response.message);
                }

                $("#buddybot-playground-past-messages-btn").children("span").removeClass("buddybot-rotate-icon");
                disableMessage(false);
                toggleThreadBtns();
            });
          });
        ';
    }

    private function toggleThreadBtnsJs()
    {
        echo '
        toggleThreadBtns();
        function toggleThreadBtns() {
            toggleDeleteThreadBtn();
            togglePastMessagesBtn();
        }
        ';
    }

    private function toggleDeleteThreadBtnJs()
    {
        echo '
        function toggleDeleteThreadBtn() {
            let threadId = $("#mgao-playground-thread-id-input").val();
            if (threadId === "") {
                $("#buddybot-playground-delete-thread-btn").css("opacity", 0);
            } else {
                $("#buddybot-playground-delete-thread-btn").css("opacity", 100);
            }
        }
        ';
    }

    private function togglePastMessagesBtnJs()
    {
        echo '
        function togglePastMessagesBtn() {
            let hasMore = $("#buddybot-playground-has-more-messages").val();
            if (hasMore === "true") {
                $("#buddybot-playground-past-messages-btn").css("opacity", 100);
            } else {
                $("#buddybot-playground-past-messages-btn").css("opacity", 0);
            }
        }
        ';
    }

    private function deleteThreadBtnJs()
    {
        $nonce = wp_create_nonce('delete_thread');
        echo '
        $("#buddybot-playground-delete-thread-btn").click(deleteThreadBtn);

        function deleteThreadBtn() {
            
            disableMessage(true);
            updateStatus(deletingThread);
            $("#buddybot-playground-messages-list").css("opacity", 0.5);
            let threadId = $("#mgao-playground-thread-id-input").val();
            
            if (threadId === "") {
                return;
            }

            const data = {
                "action": "deleteThread",
                "thread_id": threadId,
                "nonce": "' . esc_js($nonce) . '"
            };

            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    $("#mgao-playground-thread-id-input").val("");
                    $("#buddybot-playground-messages-list").html("");
                    $("#buddybot-playground-messages-list").css("opacity", 1);
                    $("div[data-buddybot-threadid=" + threadId + "]").remove();
                    updateStatus(threadDeleted);
                } else {
                    updateStatus(response.message);
                }

                disableMessage(false);
                toggleThreadBtns();
            });

        }
        ';
    }

    private function updateThreadNameJs()
    {
        echo '
        function updateThreadName(message) {
            
            const threadId = $("#mgao-playground-thread-id-input").val();

            if (message.length > 100) {
                message = $.trim(message).substring(0, 100);
            }

            $("div[data-buddybot-threadid=" + threadId + "]").text(message);
        }
        ';
    }
}