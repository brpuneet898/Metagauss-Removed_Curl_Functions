<?php

namespace BuddyBot\Admin\Html\Elements\Chatbot;

class AssistantList extends \BuddyBot\Admin\Html\Elements\Chatbot\MoRoot
{
    protected $item;

    public function listItem($item)
    {
        $this->item = $item;
    }

    public function getHtml()
    {
        $html = '<a href="#" class="list-group-item list-group-item-action" data-bs-dismiss="modal" ';
        $html .= 'data-mgao-id="' . esc_attr($this->item->id) . '" data-mgao-name="' . esc_attr($this->item->name) . '">';
        $html .= $this->assistantName();
        $html .= $this->assistantId();
        $html .= $this->createdOn();
        $html .= '</a>';
        return $html;
    }

    private function assistantName()
    {
        $name = $this->item->name;

        if (empty($name)) {
            $name = __('Unnamed', 'buddybot');
        }

        $html = '<div class="small fw-bold">';
        $html .= $name;
        $html .= '</div>';
        return $html;
    }

    private function assistantId()
    {
        $html = '<div class="small">';
        $html .= $this->item->id;
        $html .= '</div>';
        return $html;
    }

    private function createdOn()
    {
        $format = get_option('date_format') . ' ' . get_option('time_format');
        $html = '<div class="small text-muted">';
        $html .= __('Created On', 'buddybot');
        $html .= ' ';
        $html .= wp_date($format, $this->item->created_at);
        $html .= '</div>';
        return $html;
    }
}