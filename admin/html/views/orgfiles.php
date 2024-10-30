<?php

namespace BuddyBot\Admin\Html\Views;

final class OrgFiles extends \BuddyBot\Admin\Html\Views\MoRoot
{
    public function getHtml()
    {
        $heading = __('Files', 'megaform-openai');
        $this->pageHeading($heading);
        $this->pageBtns();
        $this->filesTable();
    }

    public function pageBtns()
    {
        $addfile_page = get_admin_url() . 'admin.php?page=buddybot-addfile';
        echo '<div class="mb-3">';
        echo '<a class="btn btn-dark btn-sm" role="button"';
        echo 'href="' . esc_url($addfile_page) . '"';
        echo '>';
        echo esc_html(__('Add File', 'buddybot'));
        echo '</a>';
        echo '</div>';
    }

    private function filesTable()
    {
        echo '<table class="buddybot-org-files-table table table-sm">';
        $this->tableHeader();
        $this->tableBody();
        echo '</table>';
    }

    private function tableHeader()
    {
        echo '<thead>';
        echo '<tr>';
        echo '<th scope="col">' . esc_html(__('No.', 'buddybot')) . '</th>';
        echo '<th scope="col"></th>';
        echo '<th scope="col">' . esc_html(__('File Name', 'buddybot')) . '</th>';
        echo '<th scope="col">' . esc_html(__('Purpose', 'buddybot')) . '</th>';
        echo '<th scope="col">' . esc_html(__('Size', 'buddybot')) . '</th>';
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
        echo '<div class="spinner-border text-dark d-flex justify-content-center mx-auto" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>';
        echo '</td>';
        echo '</tbody>';
    }
    
}