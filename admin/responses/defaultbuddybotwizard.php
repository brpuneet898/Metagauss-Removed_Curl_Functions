<?php

namespace BuddyBot\Admin\Responses;

class DefaultBuddyBotWizard extends \BuddyBot\Admin\Responses\MoRoot
{
    protected $file_data = '';

    public function isLocalFileWritable()
    {
        $this->checkNonce('is_local_file_writable');
        $data_types = $_POST['data_types'];

        if (!is_array($data_types)) {
            $this->response['success'] = false;
            $this->response['message'] = __('Data types should be passed as an array.', 'buddybot');
        }

        $errors = 0;

        foreach ($data_types as $data_type) {
            
            $file = $this->core_files->getLocalPath($data_type);
            
            if ($file === false) {
                $errors += 1;
                $this->response['message'] .= __(sprintf('%s file path is not defined.', $data_type), 'buddybot');
            }

            if (!is_writable($file)) {
                $errors += 1;
                $this->response['message'] .= __(sprintf('%s file is not writable.', $data_type), 'buddybot');
            }

        }

        if ($errors === 0) {
            $this->response['success'] = true;
            $this->response['message'] = __('Yay! The files are writable.', 'buddybot');
        } else {
            $this->response['success'] = false;
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function addDataToFile()
    {
        $this->checkNonce('add_data_to_file');
        $this->checkCapabilities();
        
        $data_type = $_POST['data_type'];

        $method = 'compile' . $data_type;

        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            $this->response['success'] = false;
            $this->response['message'] = '<div>' . __('Data compile method undefined. Operation aborted.', 'buddybot') . '</div>';
            echo wp_json_encode($this->response);
            wp_die();
        }

        $this->writeData($data_type);
        
        $this->response['success'] = true;
        $this->response['message'] = '<div>' . __('Added data to file.', 'buddybot') . '</div>';

        echo wp_json_encode($this->response);
        wp_die();
    }

    private function compilePosts()
    {
        $args = array(
            'post_type' => 'post'
        );
    
        $post_query = new \WP_Query($args);
    
        if($post_query->have_posts()) {
            while($post_query->have_posts()) {
                $post_query->the_post();
                $this->file_data .= wp_strip_all_tags(get_the_title());
                $this->file_data .= wp_strip_all_tags(get_the_content());
            }
        }

        wp_reset_postdata();
    }

    function compileComments()
    {
        $args = array(
            'status' => 'approve' // Fetch only approved comments
        );

        $comments = get_comments($args);

        foreach ($comments as $comment) {
            $this->file_data .= wp_strip_all_tags($comment->comment_content);
        }
    
    }

    private function writeData($data_type)
    {
        $file = fopen($this->core_files->getLocalPath($data_type), "w");
        fwrite($file, str_replace('&nbsp;',' ', $this->file_data));
        fclose($file);
        $this->file_data = '';
    }

    // public function transferDataFile()
    // {
    //     $this->checkNonce('transfer_data_file');
    //     $this->checkCapabilities();

    //     $data_type = $_POST['data_type'];

    //     $cfile = curl_file_create(
    //         realpath($this->core_files->getLocalPath($data_type)),
    //         'application/octet-stream',
    //         basename($this->core_files->getRemoteName($data_type))
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

    //     $output = $this->curlOutput($ch);
    //     $this->checkError($output);
    //     $this->updateRemoteFileOption($data_type, $output);

    //     wp_die();
    // }

    public function transferDataFile()
    {
        $this->checkNonce('transfer_data_file');
        $this->checkCapabilities();

        $data_type = $_POST['data_type'];
        $file_path = realpath($this->core_files->getLocalPath($data_type));
        $file_name = basename($this->core_files->getRemoteName($data_type));
        $cfile = new CURLFile(
            $file_path,
            'application/octet-stream',
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
            $this->checkError($response);
            wp_die();
        }
        $body = wp_remote_retrieve_body($response);
        $output = json_decode($body);
        if ($output && isset($output->id)) {
            $this->updateRemoteFileOption($data_type, $output);
        } else {
            wp_die('Failed to transfer data file.');
        }
        wp_die();
    }


    private function updateRemoteFileOption($data_type, $output)
    {
        $update = update_option($this->core_files->getWpOptionName($data_type), $output->id, false);

        if ($update) {
            $this->response['success'] = true;
            $this->response['message'] = '<div>' . __(sprintf('Remote file name updated to %s.', $output->id), 'buddybot') . '</div>';
        } else {
            $this->response['success'] = false;
            $this->response['message'] = '<div>' . __('Unable to update remote file name.', 'buddybot') . '</div>';
        }

        echo wp_json_encode($this->response);
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_isLocalFileWritable', array($this, 'isLocalFileWritable'));
    }
}