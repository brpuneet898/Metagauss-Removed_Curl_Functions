<?php

namespace BuddyBot\Admin\Responses;

class MoRoot extends \BuddyBot\Admin\MoRoot
{
    protected $response = array();
    protected $api_key = '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';
    protected $core_files;
    protected $sql;
    
    protected function setSql()
    {
        $class_name = (new \ReflectionClass($this))->getShortName();
        $file_path = $this->config->getRootPath() . 'admin/sql/' . strtolower($class_name) . '.php';

        if (file_exists($file_path)) {
            $class_name = '\BuddyBot\Admin\Sql\\' . $class_name;
            $this->sql = $class_name::getInstance(); 
        }
    }

    protected function setCoreFiles()
    {
        $this->core_files = \BuddyBot\Admin\CoreFiles::getInstance();
    }

    protected function checkNonce($nonce)
    {
        $nonce_status = wp_verify_nonce($_POST['nonce'], $nonce);

        if ($nonce_status === false) {
            $this->response['success'] = false;
            $this->response['message'] = '<div>' . __('Nonce error.', 'buddybot') . '</div>';
            $this->response['errors'] = array(__('Nonce check failed.', 'buddybot'));
            echo wp_json_encode($this->response);
            wp_die();
        }
    }

    protected function checkCapabilities()
    {
        if (!(current_user_can('manage_options'))) {
            $this->response['success'] = false;
            $this->response['message'] = __('You do not have permission to do this.', 'buddybot');
            echo wp_json_encode($this->response);
            wp_die();
        }
    }

    // protected function curlOutput($ch)
    // {
    //     $output = curl_exec($ch);
    //     $output = json_decode($output);
    //     $this->response['result'] = $output;
    //     curl_close($ch);
    //     return $output;
    // }

    protected function wpRemoteGetOutput($response)
    {
        $body = wp_remote_retrieve_body($response);
        $output = json_decode($body);
        $this->response['result'] = $output;
        return $output;
    }


    protected function checkError($output)
    {
        if (!is_object($output)) {
            $this->response['success'] = false;
            $this->response['message'] = __('Output is not an object. ', 'buddybot') . ' ' . maybe_serialize($output);
            echo wp_json_encode($this->response);
            wp_die();
        } elseif (!empty($output->error)) {
            $this->response['success'] = false;
            $this->response['message'] = '<span class="text-danger">' . __('There was an error. ', 'buddybot');
            $this->response['message'] .= $output->error->message . '</span>';
            echo wp_json_encode($this->response);
            wp_die();
        } else {
            $this->response['success'] = true;
        }
    }

    protected function moIcon($icon)
    {
        $html = '<span class="material-symbols-outlined" style="font-size:20px;vertical-align:sub;">';
        $html .= esc_html($icon);
        $html .= '</span>';
        return $html;
    }

    protected function listBtns($item_type)
    {
        $info_btn_class = 'buddybot-listbtn-' . $item_type . '-info';
        $delete_btn_class = 'buddybot-listbtn-' . $item_type . '-delete';
        
        $html = '<div class="btn-group btn-group-sm me-2" role="group" aria-label="Basic example">';
        $html .= '<button type="button" class="' . esc_attr($info_btn_class) . ' btn btn-outline-dark">' . $this->moIcon('info') . '</button>';
        $html .= '<button type="button" class="' . esc_attr($delete_btn_class) . ' btn btn-outline-dark">' . $this->moIcon('delete') . '</button>';
        $html .= '</div>';

        $html .= $this->listSpinner();
        
        return $html;
    }

    protected function listSpinner()
    {
        $html .= '<div class="buddybot-list-spinner spinner-border spinner-border-sm visually-hidden" role="status">';
        $html .= '<span class="visually-hidden">Loading...</span></div>';
        return $html;
    }
}