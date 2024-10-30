<?php
namespace BuddyBot\Frontend\Views\Bootstrap;

use BuddyBot\Traits\Singleton;
use BuddyBot\Frontend\Views\Bootstrap\BuddybotChat\SecurityChecks;
use BuddyBot\Frontend\Views\Bootstrap\BuddybotChat\SingleConversation;
use BuddyBot\Frontend\Views\Bootstrap\BuddybotChat\DeleteConversation;

class BuddybotChat extends \BuddyBot\Frontend\Views\Bootstrap\MoRoot
{
    use Singleton;
    use SecurityChecks;
    use SingleConversation;
    use DeleteConversation;
    protected $conversations;
    protected $chatbot;
    protected $timezone;

    public function shortcodeHtml($atts, $content = null)
    {
        $this->atts = shortcode_atts( array(
            'id' => $this->buddybotId()
        ), $atts );

        $html = $this->securityChecksHtml();

        if (!$this->errors) {
            $html .= $this->deleteConversationModalHtml();
            $html .= $this->alertsHtml();
            $html .= $this->assistantId();
            $html .= $this->conversationListWrapper();
            $html .= $this->singleConversationHtml();
        }

        return $html;
    }

    protected function buddybotId()
    {
        $id = $this->sql->getDefaultBuddybotId();
        return $id;
    }

    protected function alertsHtml()
    {
        $html = '<div class="buddybot-chat-conversation-alert alert alert-danger small" data-bb-alert="danger" role="alert">';
        $html .= '</div>';
        return $html;
    }

    protected function assistantId()
    {
        $html  = '<input id="buddybot-chat-conversation-assistant-id" type="hidden" ';
        $html .= 'value="' . esc_attr($this->chatbot->assistant_id) . '">';
        return $html;
    }

    private function conversationListWrapper()
    {
        $html  = '<div id="buddybot-chat-conversation-list-header" class="d-flex justify-content-start align-items-center">';
        
        $html .= '<div class="small fw-bold me-2">';
        $html .= __('Select Conversation or', 'buddybot');
        $html .= '</div>';
        
        $html .= '<button id="buddybot-chat-conversation-start-new" type="button" class="btn btn-dark btn-sm px-3 rounded-2">';
        $html .= __('Start New', 'buddybot');
        $html .= '</button>';
        
        $html .= '</div>';

        $html .= '<div id="buddybot-chat-conversation-list-loader" class="text-muted">';
        $html .= __('Loading conversations...', 'buddybot');
        $html .= '</div>';

        $html .= '<div id="buddybot-chat-conversation-list-wrapper">';
        $html .= '</div>';
        return $html;
    }

    public function conversationList($timezone)
    {
        $this->timezone = $timezone;

        $user_id = get_current_user_id();
        $this->conversations = $this->sql->getConversationsByUserId($user_id);
        
        if (!empty($this->conversations)) {
            $this->listHtml();
        } else {
            $this->noCoversationHistoryHtml();
        }
    }

    protected function listHtml()
    {
        echo '<ol class="list-group list-group-numbered small px-0">';

        foreach ($this->conversations as $conversation) {
            echo '<li class="list-group-item list-group-item-action m-0 d-flex justify-content-between align-items-start bg-transparent"';
            echo 'data-bb-threadid="' . esc_html($conversation->thread_id) . '" role="button">';
            echo '<div class="ms-2 me-auto">';
            echo '<div class="fw-bold">' . esc_html($conversation->thread_name) . '</div>';
            echo '<div class="text-muted small text-start">' . esc_html($this->conversationDate($conversation->created)) . '</div>';
            echo '</div>';
            echo '</li>';
        }
        
        echo '</ol>';
    }

    protected function conversationDate($date_string)
    {
        $timezone = new \DateTimeZone($this->timezone);
        $timestamp = strtotime($date_string);
        return wp_date(get_option('date_format') . ' ' . get_option('time_format'), $timestamp, $timezone);
    }

    protected function noCoversationHistoryHtml()
    {
        $img_url = $this->config->getRootUrl() . 'frontend/images/buddybotchat/bootstrap/zero-conversations.svg';
        echo '<div class="mt-4 text-center">';
        
        echo '<div class="my-4">';
        echo '<img width="250" src="' . esc_url($img_url) . '">';
        echo '</div>';
        
        echo '<div>';
        esc_html_e('Sorry, you have no past conversations. Please start a new one.', 'buddybot');
        echo '</div>';
        
        echo '</div>';
    }
}