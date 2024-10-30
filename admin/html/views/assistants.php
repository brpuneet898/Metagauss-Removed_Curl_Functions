<?php

namespace BuddyBot\Admin\Html\Views;

final class Assistants extends \BuddyBot\Admin\Html\Views\MoRoot
{
    public function getHtml()
    {
        $heading = __('Assistants', 'megaform-openai');
        $this->pageHeading($heading);
        $this->pageBtns();
        $this->alertContainer();
        $this->assistantsTable();
    }

    public function pageBtns()
    {
        $add_assistant_page = get_admin_url() . 'admin.php?page=buddybot-assistant';
        echo '<div class="mb-3">';
        echo '<a class="btn btn-dark btn-sm" role="button"';
        echo 'href="' . esc_url($add_assistant_page) . '"';
        echo '>';
        echo esc_html(__('Create New Assistant', 'buddybot'));
        echo '</a>';
        echo '</div>';
    }

    private function assistantsTable()
    {
        echo '<table class="buddybot-org-assistants-table table table-sm">';
        $this->tableHeader();
        $this->tableBody();
        echo '</table>';
    }

    private function tableHeader()
    {
        echo '<thead>';
        echo '<tr>';
        echo '<th scope="col">' . esc_html(__('No.', 'buddybot')) . '</th>';
        echo '<th scope="col">' . esc_html(__('Name', 'buddybot')) . '</th>';
        echo '<th scope="col">' . esc_html(__('Description', 'buddybot')) . '</th>';
        echo '<th scope="col">' . esc_html(__('Model', 'buddybot')) . '</th>';
        echo '<th scope="col">' . esc_html(__('ID', 'buddybot')) . '</th>';
        echo '<th scope="col"></th>';
        echo '</tr>';
        echo '</thead>';
    }

    private function tableBody()
    {
        echo '<tbody>';
        echo '<tr>';
        echo '<td colspan="6" class="p-5">';
        echo '<div class="spinner-border text-dark d-flex justify-content-center mx-auto" role="status">';
        echo '<span class="visually-hidden">Loading...</span>';
        echo '</div>';
        echo '</td>';
        echo '</tbody>';
    }
    
}