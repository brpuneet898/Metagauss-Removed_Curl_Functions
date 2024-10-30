<?php

namespace BuddyBot\Admin\Html\Modals;

class MoRoot extends \BuddyBot\Admin\Html\MoRoot
{
    protected $modal_id = '';

    public function getHtml()
    {
        echo '<div class="modal" tabindex="-1" id="' . esc_attr($this->modal_id) . '" style="z-index: 9999999;">';
        echo '<div class="modal-dialog">';
        echo '<div class="modal-content border-0 rounded-1">';
        $this->modalHeader();
        $this->modalBody();
        $this->modalFooter();
        echo '</div></div></div>';
    }

    protected function modalHeader()
    {
        echo '<div class="modal-header border-0">';
        echo '<div class="modal-title">';
        echo esc_html($this->modalTitle());
        echo '</div>';
        echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
        echo '</div>';
    }

    protected function modalBody()
    {
        echo '<div class="modal-body">';
        $this->bodyContent();
        echo '</div>';
    }

    protected function modalFooter()
    {
        echo '<div class="modal-footer border-0">';
        $this->footerContent();
        echo '</div>';
    }

    protected function bodyContent()
    {

    }

    protected function footerContent()
    {

    }
}