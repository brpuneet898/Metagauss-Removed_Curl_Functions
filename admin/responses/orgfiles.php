<?php

namespace BuddyBot\Admin\Responses;

class OrgFiles extends \BuddyBot\Admin\Responses\MoRoot
{
    
    // public function deleteOrgFile()
    // {
    //     $this->checkNonce('delete_org_file');
    //     $file_id = $_POST['file_id'];

    //     $url = 'https://api.openai.com/v1/files/' . $file_id;

    //     $ch = curl_init($url);

    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //         'Authorization: Bearer ' . $this->api_key
    //         )
    //     );

    //     $this->response['result'] = json_decode(curl_exec($ch));
        
    //     if ($this->response['result']->deleted) {
    //         $this->response['success'] = true;
    //     } else {
    //         $this->response['success'] = false;
    //     }

    //     echo wp_json_encode($this->response);
    //     wp_die();
    // }

    public function deleteOrgFile()
    {
        $this->checkNonce('delete_org_file');
        $file_id = $_POST['file_id'];

        $url = 'https://api.openai.com/v1/files/' . $file_id;

        $args = array(
            'method' => 'DELETE',
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_key
            ),
        );

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            $this->response['success'] = false;
            $this->response['message'] = __('Failed to delete file.', 'buddybot');
        } else {
            $body = wp_remote_retrieve_body($response);
            $result = json_decode($body);

            if ($result && isset($result->deleted) && $result->deleted) {
                $this->response['success'] = true;
            } else {
                $this->response['success'] = false;
                $this->response['message'] = __('File deletion unsuccessful.', 'buddybot');
            }
        }
        echo wp_json_encode($this->response);
        wp_die();
    }


    // public function getOrgFiles()
    // {
    //     $nonce_status = wp_verify_nonce($_POST['nonce'], 'get_org_files');

    //     if ($nonce_status === false) {
    //         wp_die();
    //     }

    //     $url = 'https://api.openai.com/v1/files';

    //     $ch = curl_init($url);
        
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //         'Authorization: Bearer ' . $this->api_key
    //         )
    //     );

    //     $output = json_decode(curl_exec($ch));
    //     $files = $output->data;
    //     $this->filesTableHtml($files);
    //     curl_close($ch);

    //     echo wp_kses_post($this->response['html']);
    //     wp_die();
    // }

    public function getOrgFiles()
    {
        $nonce_status = wp_verify_nonce($_POST['nonce'], 'get_org_files');

        if ($nonce_status === false) {
            wp_die();
        }
        $url = 'https://api.openai.com/v1/files';
        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_key
            ),
        );
        $response = wp_remote_get($url, $args);

        if (is_wp_error($response)) {
            $this->response['html'] = __('Failed to fetch organization files.', 'buddybot');
        } else {
            $body = wp_remote_retrieve_body($response);
            $output = json_decode($body);

            if ($output && isset($output->data)) {
                $files = $output->data;
                $this->filesTableHtml($files);
            } else {
                $this->response['html'] = __('No files found.', 'buddybot');
            }
        }

        echo wp_kses_post($this->response['html']);
        wp_die();
    }


    private function filesTableHtml($files)
    {
        if (!is_array($files)) {
            return;
        }

        $html = '';

        foreach ($files as $index => $file) {
            $html .= '<tr class="small" data-buddybot-itemid="' . esc_attr($file->id) . '">';
            $html .= '<th scope="row">' . absint($index) + 1 . '</th>';
            $html .= '<td>' . $this->fileIcon($file->filename) . '</td>';
            $html .= '<td>' . esc_html($file->filename) . '</td>';
            $html .= '<td>' . esc_html($file->purpose) . '</td>';
            $html .= '<td>' . esc_html($this->fileSize($file->bytes)) . '</td>';
            $html .= '<td><code>' . esc_html($file->id) . '</code></td>';
            $html .= '<td>' . $this->listBtns('file') . '</td>';
            $html .= '</tr>';
        }

        $this->response['html'] = $html;
    }

    private function fileIcon($filename)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $icon_dir = $this->config->getRootPath() . 'admin/html/images/fileicons/';
        $icon_file = $icon_dir . $ext . '.png';
        $icon_url = $this->config->getRootUrl() . 'admin/html/images/fileicons/';

        if (file_exists($icon_file)) {
            $icon_url = $icon_url . $ext . '.png';
        } else {
            $icon_url = $icon_url . 'file.png';
        }

        return '<img width="16" src="' . $icon_url . '">';

    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_deleteOrgFile', array($this, 'deleteOrgFile'));
        add_action('wp_ajax_getOrgFiles', array($this, 'getOrgFiles'));
    }
}