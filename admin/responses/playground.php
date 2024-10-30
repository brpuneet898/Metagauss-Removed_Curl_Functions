<?php

namespace BuddyBot\Admin\Responses;

class Playground extends \BuddyBot\Admin\Responses\MoRoot
{
    // public function getAssistantOptions()
    // {
    //     $this->checkNonce('get_assistants');

    //     $url = 'https://api.openai.com/v1/assistants?limit=50';

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

    //     if ($output->object === 'list') {
    //         $this->response['success'] = true;
    //         $this->assistantOptionsHtml($output);
    //     } else {
    //         $this->response['success'] = false;
    //         $this->response['message'] = __('Unable to fetch assistants list.', 'buddybot');
    //     }

    //     echo wp_json_encode($this->response);
    //     wp_die();
    // }

    public function getAssistantOptions()
    {
        $this->checkNonce('get_assistants');

        $url = 'https://api.openai.com/v1/assistants?limit=50';

        $response = wp_remote_get($url, array(
            'headers' => array(
                'OpenAI-Beta' => 'assistants=v1',
                'Content-Type' => 'application/json',
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
                $this->assistantOptionsHtml($output);
            } else {
                $this->response['success'] = false;
                $this->response['message'] = __('Unable to fetch assistants list.', 'buddybot');
            }
        }

        echo wp_json_encode($this->response);
        wp_die();
    }


    protected function assistantOptionsHtml($assistants)
    {
        $this->response['html'] = '';

        if (!is_array($assistants->data)) {
            return;
        }

        foreach ($assistants->data as $assistant) {
            $name = $assistant->name;
            $id = $assistant->id;
            $model = $assistant->model;

            if (empty($name)) {
                $name = $assistant->id;
            }

            $this->response['html'] .= '<option value="' . $id . '">' . $name . ' (' . $model . ')</option>';
        }
    }

    // public function createThread()
    // {
    //     $this->checkNonce('create_thread');
        
    //     $url = 'https://api.openai.com/v1/threads';

    //     $ch = curl_init($url);
        
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POST, true);
        
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //         'OpenAI-Beta: assistants=v1',
    //         'Content-Type: application/json',
    //         'Authorization: Bearer ' . $this->api_key
    //         )
    //     );

    //     $data = array(
    //         'metadata' => array(
    //             'wp_user_id' => get_current_user_id(),
    //             'wp_source' => 'wp_admin'
    //         )
    //     );

    //     curl_setopt($ch, CURLOPT_POSTFIELDS, wp_json_encode($data));

    //     $output = $this->curlOutput($ch);
    //     $this->checkError($output);

    //     if ($this->response['success']) {
    //         $insert = $this->sql->saveThreadId($output->id);
    //         if ($insert === false) {
    //             $this->response['success'] = false;
    //             $this->response['message'] = __('Unable to save thread in the database', 'buddybot');
    //         }
    //     }

    //     echo wp_json_encode($this->response);
    //     wp_die();
    // }

    public function createThread()
    {
        $this->checkNonce('create_thread');
        
        $url = 'https://api.openai.com/v1/threads';

        $data = array(
            'metadata' => array(
                'wp_user_id' => get_current_user_id(),
                'wp_source' => 'wp_admin'
            )
        );

        $args = array(
            'method'    => 'POST',
            'headers'   => array(
                'OpenAI-Beta' => 'assistants=v1',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key
            ),
            'body'      => wp_json_encode($data),
            'timeout'   => 45,
        );

        $response = wp_remote_post($url, $args);

        if (is_wp_error($response)) {
            $this->response['success'] = false;
            $this->response['message'] = $response->get_error_message();
        } else {
            $output = json_decode(wp_remote_retrieve_body($response));
            $this->checkError($output);

            // if ($this->response['success']) {
            //     $insert = $this->sql->saveThreadId($output->id);
            //     if ($insert === false) {
            //         $this->response['success'] = false;
            //         $this->response['message'] = __('Unable to save thread in the database', 'buddybot');
            //     }
            // }

            if ($this->response['success']) {
                if (isset($output->id)) {
                    $insert = $this->sql->saveThreadId($output->id);
                    if ($insert === false) {
                        $this->response['success'] = false;
                        $this->response['message'] = __('Unable to save thread in the database', 'buddybot');
                    } else {
                        $this->response['result'] = ['id' => $output->id]; // Ensure the result has the thread ID
                    }
                } else {
                    $this->response['success'] = false;
                    $this->response['message'] = __('Invalid response from API', 'buddybot');
                }
            }
        }

        echo wp_json_encode($this->response);
        wp_die();
    }



    // public function createMessage()
    // {
    //     $this->checkNonce('create_message');

    //     $thread_id = $_POST['thread_id'];
    //     $message = wp_unslash($_POST['message']);
    //     $file_url = $_POST['file_url'];
    //     $file_mime = $_POST['file_mime'];

    //     $file_id = '';

    //     if (filter_var($file_url, FILTER_VALIDATE_URL)) {
    //         $file_id = $this->uploadMessageFile($file_url, $file_mime);
    //     }
        
    //     $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/messages';

    //     $ch = curl_init($url);
        
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POST, true);
        
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //         'OpenAI-Beta: assistants=v1',
    //         'Content-Type: application/json',
    //         'Authorization: Bearer ' . $this->api_key
    //         )
    //     );

    //     $data = array(
    //         'role' => 'user',
    //         'content' => $message,
    //         'metadata' => array(
    //             'wp_user_id' => get_current_user_id(),
    //             'wp_source' => 'wp_admin'
    //         )
    //     );

    //     if (!empty($file_id)) {
    //         $data['file_ids'] = array($file_id);
    //     }

    //     curl_setopt($ch, CURLOPT_POSTFIELDS, wp_json_encode($data));

    //     $output = $this->curlOutput($ch);
    //     $this->checkError($output);

    //     $this->sql->updateThreadName($thread_id, $message);

    //     $this->response['html'] = $this->chatBubbleHtml($output);

    //     echo wp_json_encode($this->response);
    //     wp_die();
    // }

    public function createMessage()
    {
        $this->checkNonce('create_message');

        $thread_id = sanitize_text_field($_POST['thread_id']);
        $message = wp_unslash($_POST['message']);
        $file_url = $_POST['file_url'];
        $file_mime = $_POST['file_mime'];

        $file_id = '';

        if (filter_var($file_url, FILTER_VALIDATE_URL)) {
            $file_id = $this->uploadMessageFile($file_url, $file_mime);
        }
        
        $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/messages';

        $response = wp_remote_post($url, array(
            'method'    => 'POST',
            'headers'   => array(
                'OpenAI-Beta' => 'assistants=v1',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key
            ),
            'body'      => wp_json_encode(array(
                'role' => 'user',
                'content' => $message,
                'metadata' => array(
                    'wp_user_id' => get_current_user_id(),
                    'wp_source' => 'wp_admin'
                ),
                'file_ids' => !empty($file_id) ? array($file_id) : array()
            )),
            'timeout'   => 15,
        ));

        if (is_wp_error($response)) {
            $this->response['html'] = '';
            $this->response['message'] = __('Request to OpenAI API failed.', 'buddybot');
        } else {
            $body = wp_remote_retrieve_body($response);
            $output = json_decode($body);

            $this->response['html'] = $this->chatBubbleHtml($output);
        }

        echo wp_json_encode($this->response);
        wp_die();
    }


    // private function uploadMessageFile($file_url, $file_mime)
    // {
    //     $cfile = curl_file_create(
    //         $file_url,
    //         $file_mime,
    //         basename($file_url)
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
    //     return $output->id;
    // }

    private function uploadMessageFile($file_url, $file_mime)
    {
        $file_name = basename($file_url);
        $file_contents = file_get_contents($file_url);

        if ($file_contents === false) {
            return new WP_Error('file_read_error', __('Unable to read the file.', 'buddybot'));
        }

        $url = 'https://api.openai.com/v1/files';
        $response = wp_remote_post($url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_key,
            ),
            'body' => array(
                'purpose' => 'assistants',
                'file' => wp_remote_retrieve_body(
                    wp_remote_request(
                        $file_url,
                        array(
                            'headers' => array(
                                'Content-Type' => $file_mime,
                            ),
                        )
                    )
                ),
            ),
            'timeout' => 15,
        ));

        if (is_wp_error($response)) {
            $this->response['success'] = false;
            $this->response['message'] = __('Request to OpenAI API failed.', 'buddybot');
            return $this->response;
        }

        $body = wp_remote_retrieve_body($response);
        $output = json_decode($body);

        if ($output && isset($output->id)) {
            return $output->id;
        } else {
            $this->response['success'] = false;
            $this->response['message'] = __('Unable to upload file.', 'buddybot');
            return $this->response;
        }
    }


    // public function createRun()
    // {
    //     $this->checkNonce('create_run');

    //     $thread_id = $_POST['thread_id'];
    //     $assistant_id = $_POST['assistant_id'];
        
    //     $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/runs';

    //     $ch = curl_init($url);
        
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POST, true);
        
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //         'OpenAI-Beta: assistants=v1',
    //         'Content-Type: application/json',
    //         'Authorization: Bearer ' . $this->api_key
    //         )
    //     );

    //     $data = array(
    //         'assistant_id' => $assistant_id,
    //         'metadata' => array(
    //             'wp_user_id' => get_current_user_id(),
    //             'wp_source' => 'wp_admin'
    //         )
    //     );

    //     curl_setopt($ch, CURLOPT_POSTFIELDS, wp_json_encode($data));

    //     $output = $this->curlOutput($ch);
    //     $this->checkError($output);

    //     echo wp_json_encode($this->response);
    //     wp_die();
    // }

    public function createRun()
    {
        $this->checkNonce('create_run');

        $thread_id = $_POST['thread_id'];
        $assistant_id = $_POST['assistant_id'];
        
        $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/runs';

        $data = array(
            'assistant_id' => $assistant_id,
            'metadata' => array(
                'wp_user_id' => get_current_user_id(),
                'wp_source' => 'wp_admin'
            )
        );

        $response = wp_remote_post($url, array(
            'headers' => array(
                'OpenAI-Beta' => 'assistants=v1',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key
            ),
            'body' => wp_json_encode($data),
            'timeout' => 15,
        ));

        if (is_wp_error($response)) {
            $this->response['success'] = false;
            $this->response['message'] = __('Request to OpenAI API failed.', 'buddybot');
        } else {
            $body = wp_remote_retrieve_body($response);
            $output = json_decode($body);

            if ($output) {
                $this->response['success'] = true;
            } else {
                $this->response['success'] = false;
                $this->response['message'] = __('Unable to create run.', 'buddybot');
            }
        }

        echo wp_json_encode($this->response);
        wp_die();
    }


    // public function retrieveRun()
    // {
    //     $this->checkNonce('retrieve_run');

    //     $thread_id = $_POST['thread_id'];
    //     $run_id = $_POST['run_id'];
        
    //     $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/runs/' . $run_id;

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

    //     $this->tokensMessage();

    //     echo wp_json_encode($this->response);
    //     wp_die();
    // }

    public function retrieveRun()
    {
        $this->checkNonce('retrieve_run');

        $thread_id = $_POST['thread_id'];
        $run_id = $_POST['run_id'];
        
        $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/runs/' . $run_id;

        $response = wp_remote_get($url, array(
            'headers' => array(
                'OpenAI-Beta' => 'assistants=v1',
                'Content-Type' => 'application/json',
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

            if ($output) {
                $this->response['success'] = true;
            } else {
                $this->response['success'] = false;
                $this->response['message'] = __('Unable to retrieve run.', 'buddybot');
            }
        }

        $this->tokensMessage();

        echo wp_json_encode($this->response);
        wp_die();
    }


    private function tokensMessage()
    {
        if ($this->response['result']->status !== "completed") {
            return;
        }

        $prompt_tokens = absint($this->response['result']->usage->prompt_tokens);
        $completion_tokens = absint($this->response['result']->usage->completion_tokens);
        $total_tokens = absint($this->response['result']->usage->total_tokens);

        $message = __(
            sprintf(
                'Tokens Prompt: %1d. Completion: %2d. Total: %3d.',
                $prompt_tokens, $completion_tokens, $total_tokens
            ),
            'buddybot'
        );

        $this->response['tokens'] = $message;
    }

    // public function listMessages()
    // {
    //     $this->checkNonce('list_messages');

    //     $thread_id = $_POST['thread_id'];
    //     $limit = $_POST['limit'];
    //     $order = $_POST['order'];
    //     $after = '';
    //     $before = '';

    //     if (!empty($_POST['after'])) {
    //         $after = '&after=' . $_POST['after'];
    //     }

    //     if (!empty($_POST['before'])) {
    //         $before = '&before=' . $_POST['before'];
    //     }
        
    //     $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/messages?limit=' . $limit . '&order=' . $order . $after . $before;

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

    //     $this->messagesHtml($output->data);

    //     echo wp_json_encode($this->response);
    //     wp_die();
    // }

    public function listMessages()
    {
        $this->checkNonce('list_messages');

        $thread_id = $_POST['thread_id'];
        $limit = $_POST['limit'];
        $order = $_POST['order'];
        $after = '';
        $before = '';

        if (!empty($_POST['after'])) {
            $after = '&after=' . $_POST['after'];
        }

        if (!empty($_POST['before'])) {
            $before = '&before=' . $_POST['before'];
        }
        
        $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/messages?limit=' . $limit . '&order=' . $order . $after . $before;

        $response = wp_remote_get($url, array(
            'headers' => array(
                'OpenAI-Beta' => 'assistants=v1',
                'Content-Type' => 'application/json',
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

            if ($output && isset($output->data)) {
                $this->response['success'] = true;
                $this->messagesHtml($output->data);
            } else {
                $this->response['success'] = false;
                $this->response['message'] = __('Unable to list messages.', 'buddybot');
            }
        }

        echo wp_json_encode($this->response);
        wp_die();
    }


    private function messagesHtml($messages)
    {
        $html = '';
        $messages = array_reverse($messages);
        foreach ($messages as $message) {
            $html .= $this->chatBubbleHtml($message);
        }

        $this->response['html'] = $html;
    }

    private function chatBubbleHtml($message)
    {
        $chat_bubble = new \BuddyBot\Admin\Html\Elements\Playground\ChatBubble();
        $chat_bubble->setMessage($message);
        return $chat_bubble->getHtml();
    }

    // public function deleteThread()
    // {
    //     $this->checkNonce('delete_thread');
    //     $thread_id = $_POST['thread_id'];

    //     $url = 'https://api.openai.com/v1/threads/' . $thread_id;

    //     $ch = curl_init($url);

    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //         'Content-Type: application/json',
    //         'Authorization: Bearer ' . $this->api_key,
    //         'OpenAI-Beta: assistants=v1'
    //         )
    //     );

    //     $output = $this->curlOutput($ch);
    //     $this->checkError($output);
        
    //     if ($this->response['result']->deleted) {
    //         $this->response['success'] = true;
    //     } else {
    //         $this->response['success'] = false;
    //         $this->response['message'] = __('Unable to delete conversation.', 'buddybot');
    //     }

    //     if ($this->response['success']) {
    //         $this->sql->deleteThread($thread_id);
    //     }

    //     echo wp_json_encode($this->response);
    //     wp_die();
    // }

    public function deleteThread()
    {
        $this->checkNonce('delete_thread');
        $thread_id = $_POST['thread_id'];

        $url = 'https://api.openai.com/v1/threads/' . $thread_id;

        $response = wp_remote_request($url, array(
            'method' => 'DELETE',
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key,
                'OpenAI-Beta' => 'assistants=v1'
            ),
            'timeout' => 15,
        ));

        if (is_wp_error($response)) {
            $this->response['success'] = false;
            $this->response['message'] = __('Request to OpenAI API failed.', 'buddybot');
        } else {
            $body = wp_remote_retrieve_body($response);
            $output = json_decode($body);

            if ($output && isset($output->deleted) && $output->deleted) {
                $this->response['success'] = true;
            } else {
                $this->response['success'] = false;
                $this->response['message'] = __('Unable to delete conversation.', 'buddybot');
            }
        }

        if ($this->response['success']) {
            $this->sql->deleteThread($thread_id);
        }

        echo wp_json_encode($this->response);
        wp_die();
    }


    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_getAssistantOptions', array($this, 'getAssistantOptions'));
        add_action('wp_ajax_createThread', array($this, 'createThread'));
        add_action('wp_ajax_createMessage', array($this, 'createMessage'));
        add_action('wp_ajax_createRun', array($this, 'createRun'));
        add_action('wp_ajax_retrieveRun', array($this, 'retrieveRun'));
        add_action('wp_ajax_listMessages', array($this, 'listMessages'));
        add_action('wp_ajax_deleteThread', array($this, 'deleteThread'));
    }
}