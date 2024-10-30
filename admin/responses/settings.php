<?php

namespace BuddyBot\Admin\Responses;

class Settings extends \BuddyBot\Admin\Responses\MoRoot
{
    public function getOptions()
    {
        $this->checkNonce('get_options');

        $section = sanitize_text_field($_POST['section']);
        $section_class = '\BuddyBot\Admin\Html\Views\Settings\\' . $section;
        $selection_object = new $section_class();
        $this->response['success'] = true;
        $this->response['html'] = $selection_object->getHtml();
        print_r($this->response);
        wp_die();
    }

    public function saveSettings()
    {
        $this->checkNonce('save_settings');

        $options_data = $_POST['options_data'];

        if (!is_array($options_data)) {
            $this->response['success'] = false;
            $this->response['message'] = array(__('Invalid data.', 'buddybot'));
            $this->response['errors'] = array(__('Data must be in array format.', 'buddybot'));
            echo wp_json_encode($this->response);
            wp_die();
        }

        $secure_class = '\BuddyBot\Admin\Secure\Settings\\' . $_POST['section'];
        $secure = new $secure_class();
        $options = $secure->secureData($options_data);
        $errors = $secure->getErrors();

        if (count($errors) > 0) {
            $this->response['success'] = false;
            $this->response['message'] = array(__('There was a problem with options data.', 'buddybot'));
            $this->response['errors'] = $errors;
            echo wp_json_encode($this->response);
            wp_die();
        }

        foreach ($options as $option_name => $option_value) {
            $this->sql->saveOption($option_name, $option_value);
        }

        $this->response['success'] = true;
        echo wp_json_encode($this->response);
        wp_die();
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_getOptions', array($this, 'getOptions'));
        add_action('wp_ajax_saveSettings', array($this, 'saveSettings'));
    }
}