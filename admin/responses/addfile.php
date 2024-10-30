<?php

namespace BuddyBot\Admin\Responses;

class AddFile extends \BuddyBot\Admin\Responses\MoRoot
{

    // public function addFile()
    // {
    //     $nonce_status = wp_verify_nonce($_POST['nonce'], 'add_file');

    //     if ($nonce_status === false) {
    //         wp_die();
    //     }

    //     $file_id = $_POST['file_id'];

    //     $cfile = curl_file_create(
    //         wp_get_attachment_url($file_id),
    //         get_post_mime_type($file_id),
    //         get_the_title($file_id)
    //     );

    //     $url = 'https://api.openai.com/v1/files';
    //     $ch = curl_init($url);
        
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //         'Authorization: Bearer ' . $this->api_key
    //         )
    //     );

    //     $data = array(
    //         'purpose' => 'assistants',
    //         'file' => $cfile
    //     );

    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    //     $output = curl_exec($ch);

    //     if ($output != false) {
    //         $response['success'] = true;
    //         $output = json_decode($output);
    //         $response['html'] = $this->printFileOutput($output);
    //     } else {
    //         $response['success'] = false;
    //     }

    //     echo json_encode($response);
    //     curl_close($ch);

    //     wp_die();
    // }

    public function addFile()
    {
        $nonce_status = wp_verify_nonce($_POST['nonce'], 'add_file');

        if ($nonce_status === false) {
            wp_die();
        }
        $file_id = $_POST['file_id'];
        $file_url = wp_get_attachment_url($file_id);
        $file_type = get_post_mime_type($file_id);
        $file_name = get_the_title($file_id);

        $cfile = new CURLFile(
            realpath($file_url),
            $file_type,
            $file_name
        );

        $url = 'https://api.openai.com/v1/files';

        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_key
            ),
            'body' => array(
                'purpose' => 'assistants',
                'file' => $cfile->getCurlFile()
            ),
        );
        $response = wp_remote_post($url, $args);

        if (is_wp_error($response)) {
            $this->response['success'] = false;
        } else {
            $body = wp_remote_retrieve_body($response);
            $output = json_decode($body);

            if ($output && isset($output->id)) {
                $this->response['success'] = true;
                $this->response['html'] = $this->printFileOutput($output);
            } else {
                $this->response['success'] = false;
            }
        }
        echo wp_json_encode($this->response);
        wp_die();
    }


    private function printFileOutput($output)
    {
        $html = '<span>';
        $html .= __(sprintf('Your file has been uploaded successfully with id <b>%s</b>', $output->id), 'buddybot');
        $html .= '</span>';
        return $html;
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_addFile', array($this, 'addFile'));
    }
}