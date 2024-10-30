<?php
namespace BuddyBot\Frontend\Views\Bootstrap\BuddybotChat;

trait DeleteConversation
{
    protected function deleteConversationModalHtml()
    {
        $html  = '<div class="modal fade" id="buddybot-single-conversation-delete-modal" tabindex="-1" aria-hidden="true">';
        $html .= '<div class="modal-dialog modal-dialog-centered">';
        $html .= '<div class="modal-content small">';
        $html .= $this->deleteConversationModalHeader();
        $html .= $this->deleteConversationModalBody();
        $html .= $this->deleteConversationModalFooter();
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    protected function deleteConversationModalHeader()
    {
        $html  = '<div class="modal-header border-0">';
        $html .= '<div class="modal-title fw-bold">';
        $html .= __('Delete Conversation', 'buddybot');
        $html .= '</div>';
        $html .= '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
        $html .= '</div>';
        return $html;
    }

    protected function deleteConversationModalBody()
    {
        $html  = '<div class="modal-body">';
        $html .= '<div>';
        $html .= __('This will delete this conversation along with all its messages. Do you wish to proceed?', 'buddybot');
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    protected function deleteConversationModalFooter()
    {
        $html  = '<div class="modal-footer border-0">';
        $html .= '<button id="buddybot-single-conversation-delete-thread-modal-btn" type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">';
        $html .= __('Yes, delete conversation', 'buddybot');
        $html .= '</button>';
        $html .= '</div>';
        return $html;
    }
}