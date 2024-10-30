<?php
namespace BuddyBot\Frontend\Views\Bootstrap\BuddybotChat;

trait SingleConversation
{
    protected function singleConversationHtml()
    {
        $html = '<div id="buddybot-single-conversation-wrapper" class="container-fluid">';
        $html .= $this->conversationActions();
        $html .= $this->messagesBox();
        $html .= $this->statusBar();
        $html .= $this->newMessageInput();
        $html .= '</div>';
        return $html;
    }

    private function conversationActions()
    {
        $html = '<div class="d-flex justify-content-between align-items-center">';
        
        $html .= '<div class="d-flex">';

        $html .= '<button id="buddybot-single-conversation-back-btn" class="bg-transparent border-0 shadow-0 text-dark p-0" role="button">';
        $html .= $this->mIcon('arrow_back_ios');
        $html .= '</button>';

        $html .= '</div>';

        $html .= '<div id="buddybot-single-conversation-top-spinners" class="d-flex">';

        $html .= '<div class="spinner-grow spinner-grow-sm me-1" role="status"><span class="visually-hidden">Loading...</span></div>';
        $html .= '<div class="spinner-grow spinner-grow-sm me-1" role="status"><span class="visually-hidden">Loading...</span></div>';
        $html .= '<div class="spinner-grow spinner-grow-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

        $html .= '</div>';
        
        
        $html .= '<div class="d-flex align-items-center">';
        
        $html .= '<button id="buddybot-single-conversation-load-messages-btn" class="bg-transparent border-0 shadow-0 text-dark p-0 mx-1" role="button">';
        $html .= $this->mIcon('refresh');
        $html .= '</button>';

        $html .= '<button id="buddybot-single-conversation-delete-thread-btn" class="bg-transparent border-0 shadow-0 text-dark p-0 mx-1" role="button" ';
        $html .= 'data-bs-toggle="modal" data-bs-target="#buddybot-single-conversation-delete-modal">';
        $html .= $this->mIcon('delete');
        $html .= '</button>';
        
        $html .= '</div>';
        
        $html .= '</div>';
        return $html;
    }

    private function statusBar()
    {
        $html = '<div id="buddybot-single-conversation-status-bar">';
        $html .= '</div>';
        return $html;
    }

    private function messagesBox()
    {
        $html = '<div id="buddybot-single-conversation-messages-wrapper" class="mb-4 small" style="max-height:400px;overflow:auto;">';
        $html .= '</div>';
        return $html;
    }

    private function newMessageInput()
    {
        $html = '<div id="buddybot-single-conversation-new-messages-wrapper" class="">';
        
        $html .= '<div class="">';
        $html .= '<textarea id="buddybot-single-conversation-user-message" class="form-control rounded-4 p-3 border-bottom border-dark border-2 shadow-0" rows="3" ';
        $html .= 'placeholder="' . __('Type your question here.', 'buddybot') . '">';
        $html .= '</textarea>';
        $html .= '</div>';

        $html .= '<div class="text-center mt-3">';
        $html .= '<button id="buddybot-single-conversation-send-message-btn" type="button" class="btn btn-dark py-3 px-4 rounded-2">';
        $html .= __('Send', 'buddybot');
        $html .= '</button>';
        $html .= '</div>';
        
        $html .= '</div>';
        return $html;
    }
}