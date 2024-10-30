<?php

namespace BuddyBot\Admin\Responses;

class ChatBot extends \BuddyBot\Admin\Responses\MoRoot
{
    // public function selectAssistantModal()
    // {
    //     $this->checkNonce('select_assistant_modal');

    //     $after = '';

    //     if (!empty($_POST['after'])) {
    //         $after = '&after=' . sanitize_text_field($_POST['after']);
    //     }

    //     $url = 'https://api.openai.com/v1/assistants?limit=10' . $after;

    //     $ch = curl_init($url);
        
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //         'OpenAI-Beta: assistants=v1',
    //         'Content-Type: application/json',
    //         'Authorization: Bearer ' . $this->api_key
    //         )
    //     );

    //     $output = $this->curlOutput($ch);
    //     $this->checkError($output);

    //     $this->response['html'] = $this->getAssistantListHtml($output->data);

    //     echo wp_json_encode($this->response);
    //     wp_die();
    // }

    // Code Added by Puneet

    public function selectAssistantModal()
    {
        $this->checkNonce('select_assistant_modal');

        $after = '';

        if (!empty($_POST['after'])) {
            $after = '&after=' . sanitize_text_field($_POST['after']);
        }

        $url = 'https://api.openai.com/v1/assistants?limit=10' . $after;

        $args = array(
            'headers' => array(
                'OpenAI-Beta' => 'assistants=v1',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key,
            ),
            'timeout' => 60,
        );

        $response = wp_remote_get($url, $args);

        if (is_wp_error($response)) {
            $this->response['error'] = $response->get_error_message();
            echo wp_json_encode($this->response);
            wp_die();
        }

        $output = json_decode(wp_remote_retrieve_body($response));

        $this->checkError($output);

        $this->response['html'] = $this->getAssistantListHtml($output->data);

        echo wp_json_encode($this->response);
        wp_die();
    }

    // Code Finished by Puneet

    private function getAssistantListHtml($list)
    {
        $assisstant_list = new \BuddyBot\Admin\Html\Elements\Chatbot\AssistantList();

        $html = '';

        foreach ($list as $assistant) {
            $assisstant_list->listItem($assistant);
            $html .= $assisstant_list->getHtml();
        }

        return $html;
    }

    public function saveChatbot()
    {
        $this->checkNonce('save_chatbot');
        $this->checkCapabilities();

        $chatbot_data = $this->cleanChatbotData();
        
        if ($chatbot_data['id'] === false) {
            $this->createChatbot($chatbot_data);
        } else {
            $this->updateChatbot($chatbot_data);
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    private function cleanChatbotData()
    {
        $secure = new \BuddyBot\Admin\Secure\Chatbot();
        
        $id= $secure->chatbotId();
        $name = $secure->chatbotName();
        $description = $secure->chatbotDescription();
        $assistant_id = $secure->chatbotAssistantId();
        
        $errors = $secure->dataErrors();

        if (!empty($errors)) {
            $this->response['success'] = false;
            $this->response['message'] = __('There were errors in your data.', 'buddybot');
            $this->response['errors'] = $errors;
            echo wp_json_encode($this->response);
            wp_die();
        }
        
        return array(
            'id' => $id,
            'chatbot_name' => $name,
            'chatbot_description' => $description,
            'assistant_id' => $assistant_id
        );
    }

    private function createChatbot($chatbot_data)
    {
        $insert = $this->sql->createChatbot($chatbot_data);

        if ($insert === false) {
            global $wpdb;
            $this->response['success'] = false;
            $this->response['message'] = $wpdb->last_error;
        } else {
            $this->response['success'] = true;
            $this->response['chatbot_id'] = $insert;
        }
    }

    private function updateChatbot($chatbot_data)
    {
        $update = $this->sql->updateChatbot($chatbot_data);

        if ($update === false) {
            global $wpdb;
            $this->response['success'] = false;
            $this->response['message'] = $wpdb->last_error;
        } else {
            $this->response['success'] = true;
            $this->response['chatbot_id'] = $chatbot_data['id'];
        }
    }
   
    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_selectAssistantModal', array($this, 'selectAssistantModal'));
        add_action('wp_ajax_saveChatbot', array($this, 'saveChatbot'));
    }
}