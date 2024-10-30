<?php
namespace BuddyBot\Frontend\Requests;

class BuddybotChat extends \BuddyBot\Frontend\Requests\Moroot
{
    protected function shortcodeJs()
    {
        $this->toggleAlertJs();
        $this->onLoadJs();
        $this->lockUiJs();
        $this->getUserThreadsJs();
        $this->startNewThreadBtnJs();
        $this->singleThreadBackBtnJs();
        $this->threadListItemJs();
        $this->loadThreadListViewJs();
        $this->loadSingleThreadViewJs();
        $this->getMessagesJs();
        $this->hasMoreMessagesJs();
        $this->getPreviousMessagesJs();
        $this->sendUserMessageJs();
        $this->createRunJs();
        $this->retrieveRunJs();
        $this->getAssistantResponseJs();
        $this->scrollToMessageJs();
        $this->animateTypeJs();
        $this->deleteThreadModalBtnJs();
    }

    private function toggleAlertJs()
    {
        echo '
        function showAlert(type = "danger", text = "") {
            let alert = $(".buddybot-chat-conversation-alert[data-bb-alert=" + type + "]");
            alert.text(text);
            alert.removeClass("visually-hidden");
        }

        function hideAlerts() {
            let alert = $(".buddybot-chat-conversation-alert");
            alert.addClass("visually-hidden");
        }
        ';
    }

    private function onLoadJs()
    {
        echo '
            const bbTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            loadThreadListView();
        ';
    }

    private function lockUiJs()
    {
        echo '
        function lockUi(state = true) {
            $("#buddybot-single-conversation-user-message").prop("disabled", state);
            $("#buddybot-single-conversation-send-message-btn").prop("disabled", state);
            $("#buddybot-single-conversation-load-messages-btn").prop("disabled", state);
            $("#buddybot-single-conversation-delete-thread-btn").prop("disabled", state);
            $("#buddybot-single-conversation-back-btn").prop("disabled", state);
            $("#buddybot-chat-conversation-start-new").prop("disabled", state);
            toggleElement("#buddybot-single-conversation-top-spinners", state);
        }
        
        function toggleElement(element, state = true) {
            if (state === true) {
                $(element).removeClass("visually-hidden");
            } else {
                $(element).addClass("visually-hidden");
            }
        }
        ';
    }

    private function getUserThreadsJs()
    {
        echo '
        function getUserThreads() {

            lockUi();

            const data = {
                "action": "getConversationList",
                "timezone": bbTimeZone
            };
  
            $.post(ajaxurl, data, function(response) {
                $("#buddybot-chat-conversation-list-loader").addClass("visually-hidden");
                $("#buddybot-chat-conversation-list-wrapper").html(response);
                lockUi(false);
            });
        }
        ';
    }

    private function startNewThreadBtnJs()
    {
        echo '
        $("#buddybot-chat-conversation-start-new").click(function(){
            loadSingleThreadView();
        });
        ';
    }

    private function singleThreadBackBtnJs()
    {
        echo '
        $("#buddybot-single-conversation-back-btn").click(function(){
            loadThreadListView();
        });
        ';
    }

    private function threadListItemJs()
    {
        echo '
        $("#buddybot-chat-conversation-list-wrapper").on("click", "li", function(){
            let threadId = $(this).attr("data-bb-threadid");
            loadSingleThreadView(threadId);
        });';
    }

    private function loadThreadListViewJs()
    {
        echo '
        function loadThreadListView() {
            hideAlerts();
            getUserThreads();
            $("#buddybot-chat-conversation-list-header").removeClass("visually-hidden");
            $("#buddybot-chat-conversation-list-loader").removeClass("visually-hidden");
            $("#buddybot-chat-conversation-list-wrapper").removeClass("visually-hidden");
            $("#buddybot-single-conversation-wrapper").addClass("visually-hidden");
            sessionStorage.removeItem("bbCurrentThreadId");
            sessionStorage.removeItem("bbFirstId");
            sessionStorage.removeItem("bbLastId");
            $("#buddybot-single-conversation-messages-wrapper").html("");
        }';
    }

    private function loadSingleThreadViewJs()
    {
        echo '
        function loadSingleThreadView(threadId = false) {
            hideAlerts();
            $("#buddybot-chat-conversation-list-header").addClass("visually-hidden");
            $("#buddybot-chat-conversation-list-wrapper").addClass("visually-hidden");
            $("#buddybot-chat-conversation-list-wrapper").html("");
            $("#buddybot-single-conversation-wrapper").removeClass("visually-hidden");

            if (threadId === false) {
                loadNewThreadView();
            } else {
                loadExistingThreadView(threadId);
            }
        }
            
        function loadNewThreadView() {
            sessionStorage.removeItem("bbCurrentThreadId");
            $("#buddybot-single-conversation-load-messages-btn").addClass("visually-hidden");
            $("#buddybot-single-conversation-delete-thread-btn").addClass("visually-hidden");
        }

        function loadExistingThreadView(threadId) {
            sessionStorage.setItem("bbCurrentThreadId", threadId);
            getMessages(20, "", "bottom");
        }

        ';
    }

    private function getMessagesJs()
    {
        echo '
        function getMessages(limit = 10, after = "", scroll = "bottom") {
            lockUi();
            const data = {
                "action": "getMessages",
                "thread_id": sessionStorage.getItem("bbCurrentThreadId"),
                "limit": limit,
                "order": "desc",
                "after": after,
                "timezone": bbTimeZone,
                "nonce": "' . esc_js(wp_create_nonce('get_messages')) . '"
            };

            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                
                if (response.success) {
                    hasMoreMessages(response.result);
                    $("#buddybot-single-conversation-messages-wrapper").prepend(response.html);

                    if (scroll === "bottom") {
                        scrollToBottom();
                    } else {
                        scrollToTop();
                    }

                } else {
                    showAlert("danger", response.message);
                }
                
                lockUi(false);
            });
        }';
    }

    private function hasMoreMessagesJs()
    {
        echo '
        function hasMoreMessages(thread) {

            if(thread.has_more) {
                $("#buddybot-single-conversation-load-messages-btn").removeClass("visually-hidden");
            } else {
                $("#buddybot-single-conversation-load-messages-btn").addClass("visually-hidden");
            }

            sessionStorage.setItem("bbFirstId", thread.first_id);
            sessionStorage.setItem("bbLastId", thread.last_id);
        }
        ';
    }

    private function getPreviousMessagesJs()
    {
        echo '
        $("#buddybot-single-conversation-load-messages-btn").click(getPreviousMessages);

        function getPreviousMessages() {
            let lastId = sessionStorage.getItem("bbLastId");

            if (lastId === "") {
                return;
            }

            getMessages(limit = 10, lastId, "top");
        }

        ';
    }

    private function sendUserMessageJs()
    {
        echo '
        $("#buddybot-single-conversation-send-message-btn").click(sendUserMessage);

        function sendUserMessage() {
            let userMessage = $.trim($("#buddybot-single-conversation-user-message").val());
            
            if (userMessage === "" || userMessage == null) {
                return;
            }
            
            lockUi();

            const messageData = {
                "action": "sendUserMessage",
                "thread_id": sessionStorage.getItem("bbCurrentThreadId"),
                "user_message": userMessage,
                "nonce": "' . esc_js(wp_create_nonce('send_user_message')) . '"
            };

            $.post(ajaxurl, messageData, function(response) {
                response = JSON.parse(response);
                
                if (response.success) {
                    $("#buddybot-single-conversation-user-message").val("");
                    $("#buddybot-single-conversation-messages-wrapper").append(response.html);
                    sessionStorage.setItem("bbCurrentThreadId", response.result.thread_id);
                    $("#buddybot-single-conversation-delete-thread-btn").removeClass("visually-hidden");
                    sessionStorage.setItem("bbFirstId", response.result.id);
                    scrollToBottom(response.result.id);
                    createRun();
                } else {
                    showAlert("danger", response.message);
                }
            });
        }
        ';
    }

    private function createRunJs()
    {
        echo '
        function createRun() {

            const assistantId = $("#buddybot-chat-conversation-assistant-id").val();

            const data = {
                "action": "createFrontendRun",
                "thread_id": sessionStorage.getItem("bbCurrentThreadId"),
                "assistant_id": assistantId,
                "nonce": "' . esc_js(wp_create_nonce('create_run')) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    sessionStorage.setItem("bbCurrentRunId", response.result.id);
                    checkRun = setInterval(retrieveRun, 2000);
                } else {
                    showAlert("danger", response.message);
                }
            });
        }
        ';
    }

    private function retrieveRunJs()
    {
        echo '
        function retrieveRun() {

            const data = {
                "action": "retrieveFrontendRun",
                "thread_id": sessionStorage.getItem("bbCurrentThreadId"),
                "run_id": sessionStorage.getItem("bbCurrentRunId"),
                "nonce": "' . esc_js(wp_create_nonce('retrieve_run')) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                
                response = JSON.parse(response);
                
                if (response.success) {
                    
                    switch (response.result.status) {
                        
                        case "completed":
                            clearInterval(checkRun);
                            getAssistantResponse();
                            break;
                        
                        case "failed":
                            clearInterval(checkRun);
                            showAlert(
                                "danger", response.result.last_error.code + ": " +
                                response.result.last_error.message
                            );
                            break;

                        case "cancelled":
                        case "cancelling":
                            clearInterval(checkRun);
                            break;
                        
                        case "requires_action":
                            clearInterval(checkRun);
                            getAssistantResponse();
                            break;
                    }

                } else {
                    showAlert("danger", response.message);
                    clearInterval(checkRun);
                }
            });
        }
        ';
    }

    private function getAssistantResponseJs()
    {
        echo '
        function getAssistantResponse() {

            const data = {
                "action": "getMessages",
                "thread_id": sessionStorage.getItem("bbCurrentThreadId"),
                "before": sessionStorage.getItem("bbFirstId"),
                "limit": 10,
                "order": "desc",
                "nonce": "' . esc_js(wp_create_nonce('get_messages')) . '"
            };
  
            $.post(ajaxurl, data, function(response) {

                response = JSON.parse(response);

                if (response.success) {
                    $("#buddybot-single-conversation-messages-wrapper").append(response.html);
                    sessionStorage.setItem("bbFirstId", response.result.first_id);
                    animateMessageText(response.result.first_id);
                } else {
                    showAlert("danger", response.message);
                }

                lockUi(false);

            });
        }
        ';
    }


    private function scrollToMessageJs()
    {
        echo '
        function scrollToBottom(id = null) {

        const wrapper = "#buddybot-single-conversation-messages-wrapper";

            if (id === null) {
                $(wrapper).stop().animate({
                    scrollTop: $(wrapper)[0].scrollHeight
                }, 1000);
            } else {
                let height = $("#" + id).outerHeight();
                $(wrapper).animate({
                    scrollTop: $(wrapper)[0].scrollHeight - height - 200
                }, 1000);
            }
        }

        function scrollToTop() {
            $("#buddybot-single-conversation-messages-wrapper").animate({
                scrollTop: 0
            }, 1000);
        }
        ';
    }

    private function animateTypeJs()
    {
        echo '
        let charIndex = 0;
        let messageText = "";
        let messageContainer = null;

        function animateMessageText(element) {
            charIndex = 0;
            messageContainer = $("#" + element).find(".buddybot-chat-conversation-assistant-response");
            messageText = messageContainer.html();
            
            if (messageText.includes("<")) {
                scrollToBottom(element);
            } else {   
                messageContainer.text("");
                animateText();
            }
        }

        function animateText() {
            if (charIndex < messageText.length) {
                messageContainer.append(document.createTextNode(messageText.charAt(charIndex)));
                scrollToBottom();
                charIndex++;
                setTimeout(animateText, 50);
            }
        }
        ';
    }

    private function deleteThreadModalBtnJs()
    {
        echo '
        $("#buddybot-single-conversation-delete-thread-modal-btn").click(deleteThread);
        
        function deleteThread() {
        lockUi();
        
        const threadData = {
                "action": "deleteFrontendThread",
                "thread_id": sessionStorage.getItem("bbCurrentThreadId"),
                "nonce": "' . esc_js(wp_create_nonce('delete_frontend_thread')) . '"
            };

            $.post(ajaxurl, threadData, function(response) {

                response = JSON.parse(response);

                if (response.success) {
                    loadThreadListView();
                } else {
                    showAlert("danger", response.message);
                }

                lockUi(false);

            });
        }
        ';
    }
}