<?php

namespace BuddyBot\Admin\Requests;

final class EditAssistant extends \BuddyBot\Admin\Requests\MoRoot
{
    protected $assistant_id = '';

    protected function setAssistantId()
    {
        if (!empty($_GET['assistant_id'])) {
            $this->assistant_id = sanitize_text_field($_GET['assistant_id']);
        }
    }

    public function requestJs()
    {
        $this->setVarsJs();
        $this->getModelsJs();
        $this->getFilesJs();
        $this->filesCountJs();
        $this->updateFilesCountJs();
        $this->toggleCheckboxesJs();
        $this->assistantDataJs();
        $this->createAssistantJs();
        $this->loadAssistantValuesJs();
    }

    private function setVarsJs()
    {
        $context = 'create';

        if ($this->assistant_id !== '') {
            $context = 'update';
        }

        echo '
        const context = "' . esc_html($context) . '";
        ';
    }

    private function getModelsJs()
    {
        $nonce = wp_create_nonce('get_models');
        echo '
        getModels();
        function getModels(){
            const select = $("#buddybot-editassistant-assistantmodel");
            const data = {
                "action": "getModels",
                "nonce": "' . esc_js($nonce) . '"
            };
      
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    select.html(response.html);
                    select.siblings(".buddybot-dataload-spinner").hide();
                    if (context === "update") {
                        getAssistantData();
                    }

                } else {
                    showAlert(response.message);
                }
            });
        };
        ';
    }

    // private function getFilesJs()
    // {
    //     $nonce = wp_create_nonce('get_files');
    //     echo '
    //     getFiles();
    //     function getFiles(){
    //         const data = {
    //             "action": "getFiles",
    //             "nonce": "' . esc_js($nonce) . '"
    //         };
      
    //         $.post(ajaxurl, data, function(response) {
    //             response = JSON.parse(response);
    //             if (response.success) {
    //                 $("#buddybot-editassistant-assistantfiles").html(response.html);
    //                 recountFiles();
    //             } else {
    //                 showAlert(response.message);
    //             }
    //         });
    //     };
    //     ';
    // }

    // Code Added by Puneet

    private function getFilesJs()
    {
        $nonce = wp_create_nonce('get_files');
        echo '
        getFiles();
        function getFiles(){
            const data = {
                "action": "getFiles",
                "nonce": "' . esc_js($nonce) . '"
            };
    
            $.post(ajaxurl, data, function(response) {
                try {
                    response = JSON.parse(response);
                    if (response.success) {
                        $("#buddybot-editassistant-assistantfiles").html(response.html);
                        recountFiles();
                    } else {
                        showAlert(response.message);
                    }
                } catch (error) {
                    showAlert("Error parsing JSON response: " + error.message);
                }
            });
        };
        ';
    }

    // Code Finished by Puneet

    private function filesCountJs()
    {
        echo '
        function filesCount() {
            let count = 0;

            $("#buddybot-editassistant-assistantfiles").find("input[type=checkbox]").each(function(){
                if ($(this).is(":checked")) {
                    count++;
                }
            });

            $("#buddybot-editassistant-assistantfiles-filescount").text(count);
            return count;
        }
        ';
    }

    private function updateFilesCountJs()
    {
        echo '
        $("#buddybot-editassistant-assistantfiles").click(recountFiles);

        function recountFiles() {
            let count = filesCount();
            if (count >= 20) {
                disableCheckBoxes();
            } else {
                enableCheckBoxes();
            }
        };
        ';
    }

    private function toggleCheckboxesJs()
    {
        echo '
        function disableCheckBoxes() {
            $("#buddybot-editassistant-assistantfiles").find("input[type=checkbox]").each(function(){
                if (!$(this).is(":checked")) {
                    $(this).prop("disabled", true);
                }
            });
        }

        function enableCheckBoxes() {
            $("#buddybot-editassistant-assistantfiles").find("input[type=checkbox]").each(function(){
                $(this).prop("disabled", false);
            });
        }
        ';
    }

    private function assistantDataJs()
    {
        echo '
        function assistantData() {
            let assistantData = {};
            assistantData["name"] = $("#buddybot-editassistant-assistantname").val();
            assistantData["description"] = $("#buddybot-editassistant-assistantdescription").val();
            assistantData["model"] = $("#buddybot-editassistant-assistantmodel").val();
            assistantData["instructions"] = $("#buddybot-editassistant-assistantinstructions").val();
            assistantData["tools"] = assistantTools();
            assistantData["file_ids"] = assistantFiles();

            return assistantData;
        }

        function assistantTools() {
            let assistantTools = [];

            $("#buddybot-editassistant-assistanttools").find("input[type=checkbox]").each(function(){
                if ($(this).is(":checked")) {
                    let value = $(this).val();
                    assistantTools.push(value);
                }
            });

            return assistantTools;
        }

        function assistantFiles() {
            let assistantFiles = [];

            $("#buddybot-editassistant-assistantfiles").find("input[type=checkbox]").each(function(){
                if ($(this).is(":checked")) {
                    let value = $(this).val();
                    assistantFiles.push(value);
                }
            });

            return assistantFiles;
        }
        ';
    }

    // private function createAssistantJs()
    // {
    //     $nonce = wp_create_nonce('create_assistant');
    //     echo '
    //     $("#buddybot-editassistant-editassistant-submit").click(createAssistant);

    //     function createAssistant(){
    //         hideAlert();
    //         disableFields(true);
    //         showBtnLoader("#buddybot-editassistant-editassistant-submit");
    //         let aData = assistantData();

    //         const data = {
    //             "action": "createAssistant",
    //             "assistant_id": "' . absint($this->assistant_id) . '",
    //             "assistant_data": JSON.stringify(aData),
    //             "nonce": "' . esc_js($nonce) . '"
    //         };
      
    //         $.post(ajaxurl, data, function(response) {
    //             hideBtnLoader("#buddybot-editassistant-editassistant-submit");
    //             response = JSON.parse(response);
    //             if (response.success) {
    //                 location.replace("' . esc_url(get_admin_url()) . 'admin.php?page=buddybot-assistant&assistant_id=' . '" + response.result.id);
    //             } else {
    //                 showAlert(response.message);
    //             }
    //             disableFields(false);
    //         });
    //     };
    //     ';
    // }

    // Code Added by Puneet

    private function createAssistantJs()
    {
        $nonce = wp_create_nonce('create_assistant');
        echo '
        $("#buddybot-editassistant-editassistant-submit").click(createAssistant);

        function createAssistant(){
            hideAlert();
            disableFields(true);
            showBtnLoader("#buddybot-editassistant-editassistant-submit");
            let aData = assistantData();

            const data = {
                "action": "createAssistant",
                "assistant_id": "' . absint($this->assistant_id) . '",
                "assistant_data": JSON.stringify(aData),
                "nonce": "' . esc_js($nonce) . '"
            };

            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response); 
                hideBtnLoader("#buddybot-editassistant-editassistant-submit");
                if (response.result) {
                    location.replace("' . esc_url(get_admin_url()) . 'admin.php?page=buddybot-assistant&assistant_id=' . '" + response.result.id);
                } else {
                    showAlert(response.error || response.message); 
                }
                disableFields(false);
            })
            .fail(function(xhr, status, error) {
                showAlert("Error: " + error); 
                hideBtnLoader("#buddybot-editassistant-editassistant-submit");
                disableFields(false);
            });
        };
        ';
    }

    // Code Finished by Puneet

    private function loadAssistantValuesJs()
    {
        if ($this->assistant_id === null or $this->assistant_id === '') {
            return;
        }

        $nonce = wp_create_nonce('get_assistant_data');
        echo '

        getAssistantData();

        function getAssistantData(){
            disableFields(true);
            const data = {
                "action": "getAssistantData",
                "assistant_id": "' . absint($this->assistant_id) . '",
                "nonce": "' . esc_js($nonce) . '"
            };
      
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                
                if (response.success) {
                    fillAssistantValues(response.result);
                } else {
                    showAlert(response.message);
                }

                disableFields(false);
                recountFiles();
            });
        };

        function fillAssistantValues(assistant) {
            $("#buddybot-editassistant-assistantname").val(assistant.name);
            $("#buddybot-editassistant-assistantdescription").val(assistant.description);
            $("#buddybot-editassistant-assistantmodel").val(assistant.model);
            $("#buddybot-editassistant-assistantinstructions").val(assistant.instructions);
            checkEnabledTools(assistant.tools);
            selectAttachedFiles(assistant.file_ids);
        }

        function checkEnabledTools(tools) {
            
            let cbValues = [];
            
            $.each(tools, function(index, tool) {
                cbValues.push(tool.type);
            });

            if (cbValues.length === 0) {
                return;
            }

            $("#buddybot-editassistant-assistanttools").find("input[type=checkbox]").each(function(){
                
                let cbValue = $(this).val();

                if ($.inArray(cbValue, cbValues) > -1) {
                    $(this).prop("checked", true);
                }
            
            });
        }

        function selectAttachedFiles(fileIds) {

            if (fileIds.length === 0 || !$.isArray(fileIds)) {
                return;
            }

            $("#buddybot-editassistant-assistantfiles").find("input[type=checkbox]").each(function(){
                
                let fileId = $(this).val();
                
                if ($.inArray(fileId, fileIds) > -1) {
                    $(this).prop("checked", true);
                }

                filesCount();

            });
        }
        ';
    }
}