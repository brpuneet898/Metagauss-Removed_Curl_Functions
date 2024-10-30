<?php
namespace BuddyBot\Frontend\Views\Bootstrap\BuddybotChat;

trait SecurityChecks
{
    protected $errors = 0;

    protected function securityChecksHtml()
    {
        $html  = $this->isUserLoggedIn();
        $html .= $this->isOpenAiKeySet();
        $html .= $this->chatbotExists();
        return $html;
    }

    protected function isUserLoggedIn()
    {
        $check = is_user_logged_in();

        if (!$check) {
            $this->errors += 1;
            return $this->userNotLoggedIn();
        }
    }

    private function userNotLoggedIn()
    {
        $html = '<div class="alert alert-danger small" role="alert">';
        $html .= __('You must be logged in to use this feature.', 'buddybot');
        $html .= '</div>';
        return $html;
    }

    protected function isOpenAiKeySet()
    {
        $openai_api_key = $this->sql->getOption('openai_api_key', '');
        
        if (empty($openai_api_key)) {
            $this->errors += 1;
            $html = $this->opeaiApiKeyNotSet();
            return $html;
        }
    }

    private function openAiApiKeyNotSet()
    {
        $html = '<div class="alert alert-danger small" role="alert">';
        $html .= __('API Key Missing.', 'buddybot');
        $html .= '</div>';
        return $html;
    }

    private function chatbotExists()
    {
        $chatbot = $this->sql->getChatbot($this->atts['id']);

        if ($chatbot === null) {
            $this->errors += 1;
            $html = $this->invalidChatbot();
            return $html;
        } else {
            $this->chatbot = $chatbot;
        }
    }

    private function invalidChatbot()
    {
        $html = '<div class="alert alert-danger small" role="alert">';
        $html .= __('Invalid Chatbot ID. Unable to proceed.', 'buddybot');
        $html .= '</div>';
        return $html;
    }
}