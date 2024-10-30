<?php
namespace BuddyBot\Admin;

final class InitialChecks extends \BuddyBot\Admin\MoRoot
{

    private $data;
    private $errors = 0;

    protected $capabilities;

    protected $html = '';

    protected function setCapabilities()
    {
        $this->capabilities = array(
            'settings' => 'manage_options'
        );
    }

    protected function addAlert($error_text = '')
    {
        $this->html .= '<div class="alert alert-danger small mb-3" role="alert">';
        $this->html .= $error_text;
        $this->html .= '</div>';
    }

    private function capabilityCheck()
    {
        $capability = 'manage_options';

        $page = str_replace('buddybot-', '', $_GET['page']);

        if (array_key_exists($page, $this->capabilities)) {
            $capability = $this->capabilities[$page];
        }

        if (!current_user_can($capability)) {
            $this->errors += 1;
            $this->addAlert(
                __('You are not authorized to access this page.', 'buddybot')
            );
        }
    }

    private function openaiApikeyCheck()
    {
        $key = $this->options->getOption('openai_api_key', '');

        if (empty($key)) {
            $this->errors += 1;
            $this->addAlert(
                __(sprintf('OpenAI API Key Missing. Please save it <a href="%s">here.</a>', admin_url('admin.php?page=buddybot-settings')), 'buddybot')
            );
        }
    }

    private function curlCheck()
    {
        if (!extension_loaded('curl')) {
            $this->errors += 1;
            $this->addAlert(
                __('Curl PHP extension not installed. Communication with OpenAI server not possible.', 'buddybot')
            );
        }
    }

    public function hasErrors()
    {
        if ($this->errors > 0) {
            return true;
        } else {
            return false;
        }
    }

    private function runChecks()
    {
        if (!empty($this->data['custom_checks']) and is_array($this->data['custom_checks'])) {
            foreach ($this->data['custom_checks'] as $custom_check) {
                $method = $custom_check . 'Check';
                if (method_exists($this, $method)) {
                    $this->$method();
                } else {
                    $this->errors += 1;
                    $this->addAlert(
                        __('Invalid custom check requested.', 'buddybot')
                    );
                }
            }
        } else {
            $this->capabilityCheck();
            $this->openaiApikeyCheck();
            $this->curlCheck();
        }
    }

    public function __construct($data = '')
    {
        $this->data = $data;
        $this->setAll();
        $this->runChecks();
        echo wp_kses_post($this->html);
    }
}