<?php

namespace BuddyBot\Admin\Responses;

class EditAssistant extends \BuddyBot\Admin\Responses\MoRoot
{
    // public function getModels()
    // {
    //     $this->checkNonce('get_models');

    //     $url = 'https://api.openai.com/v1/models';
    //     $ch = curl_init($url);
        
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //         'Authorization: Bearer ' . $this->api_key
    //         )
    //     );

    //     $output = curl_exec($ch);
    //     curl_close($ch);

    //     $output = json_decode($output);

    //     if ($output->object === 'list') {
    //         $this->response['success'] = true;
    //         $this->response['list'] = $output->data;
    //         $this->response['html'] = $this->modelsListHtml($output->data);
    //     } else {
    //         $this->response['success'] = false;
    //         $this->response['message'] = __('Unable to fetch models list.', 'buddybot');
    //     }

    //     echo wp_json_encode($this->response);
    //     wp_die();
    // }

    public function getModels()
    {
        $this->checkNonce('get_models');

        $url = 'https://api.openai.com/v1/models';
        $response = wp_remote_get($url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_key
            ),
            'timeout' => 15,
        ));

        if (is_wp_error($response)) {
            $this->response['success'] = false;
            $this->response['message'] = __('Request to OpenAI API failed.', 'buddybot');
        } else {
            $body = wp_remote_retrieve_body($response);
            $output = json_decode($body);

            if ($output && isset($output->object) && $output->object === 'list') {
                $this->response['success'] = true;
                $this->response['list'] = $output->data;
                $this->response['html'] = $this->modelsListHtml($output->data);
            } else {
                $this->response['success'] = false;
                $this->response['message'] = __('Unable to fetch models list.', 'buddybot');
            }
        }

        echo wp_json_encode($this->response);
        wp_die();
    }


    // public function getFiles()
    // {
    //     $this->checkNonce('get_files');

    //     $url = 'https://api.openai.com/v1/files';
    //     $ch = curl_init($url);
        
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //         'Authorization: Bearer ' . $this->api_key
    //         )
    //     );

    //     $output = $this->curlOutput($ch);
    //     $this->checkError($output);

    //     $this->response['html'] = $this->filesListHtml($output->data);

    //     echo wp_json_encode($this->response);
    //     wp_die();
    // }

    // Code Added by Puneet

    public function getFiles()
    {
        $this->checkNonce('get_files');

        $url = 'https://api.openai.com/v1/files';

        $response = wp_remote_get($url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_key,
            ),
        ));

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            $this->response['success'] = false;
            $this->response['message'] = $error_message;
        } else {
            $output = wp_remote_retrieve_body($response);
            $output_data = json_decode($output);

            if ($output_data === null) {
                $this->response['success'] = false;
                $this->response['message'] = 'Error decoding JSON response';
            } else {
                $this->response['success'] = true;
                $this->response['html'] = $this->filesListHtml($output_data->data);
            }
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    // Code Finished by Puneet

    // public function createAssistant()
    // {
    //     $this->checkNonce('create_assistant');
    //     $this->checkCapabilities();

    //     $assistant_id = '';

    //     if (!empty($_POST['assistant_id'])) {
    //         $assistant_id = '/' . $_POST['assistant_id'];
    //     }

    //     $url = 'https://api.openai.com/v1/assistants' . $assistant_id;

    //     $ch = curl_init($url);
        
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //         'Content-Type: application/json',
    //         'Authorization: Bearer ' . $this->api_key,
    //         'OpenAI-Beta: assistants=v1'
    //         )
    //     );

    //     $assistant_data = json_decode(wp_unslash($_POST['assistant_data']));

    //     $data = array(
    //         'model' => $assistant_data->model,
    //         'name' => $assistant_data->name,
    //         'description' => $assistant_data->description,
    //     );

    //     $data['tools'] = $this->assistantTools($assistant_data->tools);
    //     $data['file_ids'] = $this->assistantFiles($assistant_data->file_ids);

    //     curl_setopt($ch, CURLOPT_POSTFIELDS, wp_json_encode($data));

    //     $output = $this->curlOutput($ch);
    //     $this->checkError($output);

    //     $this->response['result'] = $output;
    //     echo wp_json_encode($this->response);
    //     wp_die();
    // }

    // Code Added by Puneet

    public function createAssistant()
    {
        $this->checkNonce('create_assistant');
        $this->checkCapabilities();

        $assistant_id = '';

        if (!empty($_POST['assistant_id'])) {
            $assistant_id = '/' . $_POST['assistant_id'];
        }

        $url = 'https://api.openai.com/v1/assistants' . $assistant_id;

        $assistant_data = json_decode(wp_unslash($_POST['assistant_data']));

        $data = array(
            'model' => $assistant_data->model,
            'name' => $assistant_data->name,
            'description' => $assistant_data->description,
        );

        $data['tools'] = $this->assistantTools($assistant_data->tools);
        $data['file_ids'] = $this->assistantFiles($assistant_data->file_ids);

        $response = wp_remote_post($url, array(
            'method'    => 'POST',
            'headers'   => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key,
                'OpenAI-Beta' => 'assistants=v1',
            ),
            'body'      => wp_json_encode($data),
        ));

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            $this->response['error'] = $error_message;
        } else {
            $output = wp_remote_retrieve_body($response);
            $this->response['result'] = json_decode($output, true); 
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    // Code Finished by Puneet

    private function assistantTools($tools)
    {
        $value = array();

        foreach ($tools as $tool) {
            $value[] = array('type' => $tool);
        }

        return $value;
    }

    private function assistantFiles($file_ids)
    {
        $value = array();

        foreach ($file_ids as $file_id) {
            $value[] = $file_id;
        }

        return $value;
    }

    private function modelsListHtml($list)
    {
        $unsupported_models = $this->config->getProp('unsupported_models');

        $html = '';

        if (!is_array($list) or empty($list)) {
            return $html;
        }

        foreach ($list as $model) {

            if (!in_array($model->id, $unsupported_models)) {
                $html .= '<option value="' . esc_attr($model->id) . '">';
                $html .= esc_html(strtoupper(str_replace('-', ' ', $model->id)));
                $html .= '</option>';
            }
        }

        return $html;
    }

    private function filesListHtml($list)
    {
        $html = '';

        if (!is_array($list) or empty($list)) {
            return $html;
        }

        foreach ($list as $file) {

            if ($file->purpose === 'assistants' and absint($file->bytes) <= 536870912) {
                $html .= '<div class="mb-2 text-muted">';
                $html .= '<label for="' . $file->id . '">';
                $html .= '<input type="checkbox" class="me-2 buddybot-item-field" id="' . $file->id . '" value="' . $file->id . '">';
                $html .= $file->filename;
                $html .= '<span class="badge text-bg-secondary rounded-pill ms-1">' . $this->fileSize($file->bytes) . '</span>';
                $html .= '</label>';
                $html .= '</div>';
            }
        }

        return $html;
    }

    // public function getAssistantData()
    // {
    //     $this->checkNonce('get_assistant_data');

    //     $assistant_id = sanitize_text_field($_POST['assistant_id']);

    //     $url = 'https://api.openai.com/v1/assistants/' . $assistant_id;
    //     $ch = curl_init($url);
        
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //         'Content-Type: application/json',
    //         'Authorization: Bearer ' . $this->api_key,
    //         'OpenAI-Beta: assistants=v1'
    //         )
    //     );

    //     $output = $this->curlOutput($ch);
    //     $this->checkError($output);

    //     echo wp_json_encode($this->response);
    //     wp_die();
    // }

    public function getAssistantData()
    {
        $this->checkNonce('get_assistant_data');

        $assistant_id = sanitize_text_field($_POST['assistant_id']);

        $url = 'https://api.openai.com/v1/assistants/' . $assistant_id;

        $response = wp_remote_get($url, array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key,
                'OpenAI-Beta' => 'assistants=v1'
            ),
            'timeout' => 15,
        ));

        if (is_wp_error($response)) {
            $this->response['result'] = '';
            $this->response['message'] = __('Request to OpenAI API failed.', 'buddybot');
        } else {
            $body = wp_remote_retrieve_body($response);
            $output = json_decode($body);

            $this->response['result'] = $output;
        }

        echo wp_json_encode($this->response);
        wp_die();
    }


    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_getModels', array($this, 'getModels'));
        add_action('wp_ajax_getFiles', array($this, 'getFiles'));
        add_action('wp_ajax_createAssistant', array($this, 'createAssistant'));
        add_action('wp_ajax_getAssistantData', array($this, 'getAssistantData'));
    }
}