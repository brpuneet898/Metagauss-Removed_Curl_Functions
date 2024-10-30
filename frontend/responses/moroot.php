<?php
namespace BuddyBot\Frontend\Responses;

class MoRoot extends \BuddyBot\Frontend\Moroot
{
    protected $sql;

    protected $response;
    protected $api_key;

    protected function setSql()
    {
        $class_name = (new \ReflectionClass($this))->getShortName();
        $file_path = $this->config->getRootPath() . 'frontend/sql/' . strtolower($class_name) . '.php';

        if (file_exists($file_path)) {
            $class_name = '\BuddyBot\frontend\Sql\\' . $class_name;
            $this->sql = $class_name::getInstance(); 
        }
    }

    protected function setApiKey()
    {
        $this->api_key = $this->options->getOption('openai_api_key');
    }
    
    protected function checkNonce($nonce)
    {
        $nonce_status = wp_verify_nonce($_POST['nonce'], $nonce);

        if ($nonce_status === false) {
            $this->response['success'] = false;
            $this->response['message'] =  __('Nonce error. Unable to complete request.', 'buddybot');
            $this->response['errors'] = array(__('Nonce check failed.', 'buddybot'));
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
            $this->response['message'] =  __('There was an error. ', 'buddybot') . $output->error->message;
            echo wp_json_encode($this->response);
            wp_die();
        } else {
            $this->response['success'] = true;
        }
    }
}