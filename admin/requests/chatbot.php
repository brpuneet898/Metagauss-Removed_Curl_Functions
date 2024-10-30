<?php

namespace BuddyBot\Admin\Requests;

class ChatBot extends \BuddyBot\Admin\Requests\MoRoot
{
    public function requestJs()
    {
        $this->pageVarsJs();
        $this->selectAssistantModalJs();
        $this->saveBtnJs();
        $this->getChatbotDataJs();
        $this->toggleErrorsJs();
    }

    protected function chatbotId() {
        if (empty($_GET['chatbot_id'])) {
            return 0;
        } else {
            return $_GET['chatbot_id'];
        }
    }

    protected function pageVarsJs()
    {
        echo '
        let dataErrors = [];
        let errorMessage = "";
        ';
    }

    protected function selectAssistantModalJs()
    {
        $this->getAssistantsListJs();
        $this->selectAssistantJs();
        $this->showLoadMoreBtnJs();
        $this->updateLastIdJs();
        $this->loadMoreBtnJs();
        $this->parseAssistantListDataJs();
        $this->highlightCurrentAssistantJs();
        $this->autoScrollJs();
    }

    // protected function getAssistantsListJs()
    // {
    //     echo '
    //     const selectAssistantModal = document.getElementById("buddybot-select-assistant-modal");
    //     if (selectAssistantModal) {
    //         selectAssistantModal.addEventListener("show.bs.modal", event => {

    //             $("#mgao-select-assistant-modal-list").html("");
    //             $("#buddybot-selectassistant-spinner").removeClass("visually-hidden");
                
                
    //             const data = {
    //                 "action": "selectAssistantModal",
    //                 "nonce": "' . esc_js(wp_create_nonce('select_assistant_modal')) . '"
    //             };
  
    //             $.post(ajaxurl, data, function(response) {
    //                 parseAssistantListData(response);
    //             });
    //         });
    //     }
    //     ';
    // }

    // Code Added by Puneet

    protected function getAssistantsListJs()
    {
        echo '
        const selectAssistantModal = document.getElementById("buddybot-select-assistant-modal");
        if (selectAssistantModal) {
            selectAssistantModal.addEventListener("show.bs.modal", event => {

                $("#mgao-select-assistant-modal-list").html("");
                $("#buddybot-selectassistant-spinner").removeClass("visually-hidden");

                const data = {
                    "action": "selectAssistantModal",
                    "nonce": "' . esc_js(wp_create_nonce('select_assistant_modal')) . '"
                };

                $.post(ajaxurl, data, function(response) {
                    $("#buddybot-selectassistant-spinner").addClass("visually-hidden");

                    if (response.error) {
                        console.error(response.error);
                        // Display error to the user if needed
                    } else {
                        parseAssistantListData(response);
                    }
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    $("#buddybot-selectassistant-spinner").addClass("visually-hidden");
                    console.error("AJAX error: " + textStatus + " : " + errorThrown);
                });
            });
        }
        ';
    }

    // Code Finished by Puneet

    protected function selectAssistantJs()
    {
        echo '
        $("#mgao-select-assistant-modal-list").on("click", ".list-group-item", function(){
            
            let assistantId = $(this).attr("data-mgao-id");
            let assistantName = $(this).attr("data-mgao-name");
            
            $("#mgao-chatbot-selected-assistant-name").html(assistantName);
            $("#mgao-chatbot-selected-assistant-id").html(assistantId);
            $("#mgao-chatbot-assistant-id").val(assistantId);
        });
        ';
    }

    protected function showLoadMoreBtnJs()
    {
        echo '
        showLoadMoreBtn();
        function showLoadMoreBtn(hasMore = false) {
            if (hasMore) {
                $("#buddybot-selectassistant-load-more-btn").show();
            } else {
                $("#buddybot-selectassistant-load-more-btn").hide();
            }
        }
        ';
    }

    protected function updateLastIdJs()
    {
        echo '
        function updateLastId(lastId) {
            $("#mgao-selectassistant-last-id").val(lastId);
        }
        ';
    }

    protected function loadMoreBtnJs()
    {
        echo '
        $("#buddybot-selectassistant-load-more-btn").click(loadMoreBtn);
        function loadMoreBtn() {
            autoScroll();
            showBtnLoader("#buddybot-selectassistant-load-more-btn");
            $("#buddybot-selectassistant-spinner").removeClass("visually-hidden");
            const data = {
                "action": "selectAssistantModal",
                "after": $("#mgao-selectassistant-last-id").val(),
                "nonce": "' . esc_js(wp_create_nonce('select_assistant_modal')) . '"
            };

            $.post(ajaxurl, data, function(response) {
                parseAssistantListData(response);
                hideBtnLoader("#buddybot-selectassistant-load-more-btn"); 
            });
        }
        ';
    }

    // protected function parseAssistantListDataJs()
    // {
    //     echo '
    //     function parseAssistantListData(listData) {
    //         listData = JSON.parse(listData);
    //         if (listData.success) {
    //             $("#buddybot-selectassistant-spinner").addClass("visually-hidden");
    //             $("#mgao-select-assistant-modal-list").append(listData.html);
    //             showLoadMoreBtn(listData.result.has_more);
    //             updateLastId(listData.result.last_id);
    //             highlightCurrentAssistant();
    //         }
    //     }
    //     ';
    // }

    // Code Added by Puneet

    protected function parseAssistantListDataJs()
    {
        echo '
        function parseAssistantListData(listData) {
            listData = JSON.parse(listData);
            if (listData.success) {
                $("#buddybot-selectassistant-spinner").addClass("visually-hidden");
                $("#mgao-select-assistant-modal-list").append(listData.html);
                if (listData.result) {
                    showLoadMoreBtn(listData.result.has_more);
                    updateLastId(listData.result.last_id);
                }
                highlightCurrentAssistant();
            }
        }
        ';
    }

    // Code Finished by Puneet

    protected function highlightCurrentAssistantJs()
    {
        echo '
        function highlightCurrentAssistant() {
            let currentAssistantId = $("#mgao-chatbot-assistant-id").val();
            if (currentAssistantId !== "") {
                $("#mgao-select-assistant-modal-list").find("[data-mgao-id=" + currentAssistantId).addClass("bg-success bg-opacity-25");
            }
        }
        ';
    }

    protected function autoScrollJs()
    {
        echo '
        function autoScroll() {
            count = $("#mgao-select-assistant-modal-list").length;
            $("#mgao-select-assistant-modal-list").parent().animate({
                scrollTop: $("#buddybot-selectassistant-spinner").offset().top
            }, 1000);
        }
        ';
    }

    protected function saveBtnJs()
    {
        echo '
        $("#mgao-chatbot-save-btn").click(saveChatbot);
        
        document.addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
              event.preventDefault();
              saveChatbot();
            }
          });

        function saveChatbot() {
            disableFields(true);
            showBtnLoader("#mgao-chatbot-save-btn");
            chatbotData = getChatbotData();

            if (dataErrors.length > 0) {
                displayErrors();
                disableFields(false);
                hideBtnLoader("#mgao-chatbot-save-btn");
                return;
            }
            
            const data = {
                "action": "saveChatbot",
                "chatbot_data": chatbotData,
                "nonce": "' . esc_js(wp_create_nonce('save_chatbot')) . '"
            };

            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    location.replace("' . esc_url(admin_url()) . 'admin.php?page=buddybot-chatbot&chatbot_id=' . '" + response.chatbot_id + "&success=1");
                } else {
                    dataErrors = response.errors;
                    displayErrors();
                }

                disableFields(false);
                hideBtnLoader("#mgao-chatbot-save-btn");
            });
        }
        ';
    }

    protected function getChatbotDataJs()
    {
        echo '
        function getChatbotData()
        {
            const chatabotData = {};
            chatabotData["id"] = ' . absint($this->chatbotId()) . ';
            chatabotData["name"] = getChatbotName();
            chatabotData["description"] = getChatbotDescription();
            chatabotData["assistant_id"] = getChatbotAssistantId();
            return chatabotData;
        }

        function getChatbotName() {
            
            let name = $("#mgao-chatbot-name").val();
            
            if (name === "") {
                dataErrors.push("' . esc_html(__('BuddyBot name cannot be empty.', 'buddybot')) . '"); 
            }

            if (name.length > 1024) {
                dataErrors.push("' . esc_html(__('BuddBot name cannot be more than 1024 characters.', 'buddybot')) . '"); 
            }

            return name;
        }

        function getChatbotDescription() {

            let description = $("#mgao-chatbot-description").val();

            if (description.length > 2048) {
                dataErrors.push("' . esc_html(__('Chatbot description cannot be more than 2048 characters.', 'buddybot')) . '"); 
            }

            return description;
        }

        function getChatbotAssistantId() {
            
            let assistantId = $("#mgao-chatbot-assistant-id").val();
            
            if (assistantId === "") {
                dataErrors.push("' . esc_html(__('Please select an Assistant for this BuddyBot.', 'buddybot')) . '"); 
            }

            return assistantId;
        }

        ';
    }

    protected function toggleErrorsJs()
    {
        echo '
        displayErrors();
        function displayErrors() {
            let errorsHtml = "";

            if (dataErrors.length === 0) {
                $("#buddybot-chatbot-errors").hide();
                return;
            }

            $("#buddybot-chatbot-success").hide();
            $.each(dataErrors, function(index, value){
                errorsHtml = errorsHtml + "<li>" + value + "</li>";
            });

            $("#buddybot-chatbot-errors-list").html(errorsHtml);
            $("#buddybot-chatbot-errors").show();
            dataErrors.length = 0;
        }
        ';
    }
}