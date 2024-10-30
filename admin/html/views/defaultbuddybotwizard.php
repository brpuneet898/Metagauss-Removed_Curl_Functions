<?php

namespace BuddyBot\Admin\Html\Views;

final class DefaultBuddyBotWizard extends \BuddyBot\Admin\Html\Views\MoRoot
{
    public function getHtml()
    {
        $heading = __('Set Default BuddyBot', 'buddybot');
        $this->pageHeading($heading);
        $this->wizardStartPrompt();
        $this->progressBarBlock();
        $this->messagesBlock();
    }

    private function wizardStartPrompt()
    {
        echo '<div class="text-start mb-3 d-flex justify-content-center">';
        echo '<div class="card p-0" style="width: 18rem;">';
        echo '<div class="card-header small">';
        esc_html_e('Step 1', 'buddybot');
        echo '</div>';
        echo '<div class="card-body">';
        echo '<h5 class="card-title">';
        esc_html_e('Select Data');
        echo '</h5>';
        echo '<p class="card-text">';
        esc_html_e('Select the data you wish to send to OPENAI server to train your AI assistant.', 'buddybot');
        $this->dataSelectCheckboxes();
        echo '</p>';
        echo '<button id="buddybot-default-wizard-start-btn" class="btn btn-primary w-100">';
        esc_html_e('Start', 'buddybot');
        echo '</button>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    private function dataSelectCheckboxes()
    {
        echo '<div id="buddybot-default-wizard-data-types-selection" class="my-4">';

        echo '<div class="form-check small">';
        echo '<input class="align-text-bottom" type="checkbox" value="posts" id="buddybot-default-wizard-data-type-posts">';
        echo '<label class="" for="buddybot-default-wizard-data-type-posts">';
        esc_html_e('Posts', 'butddybot');
        echo '</label>';
        echo '</div>';

        echo '<div class="form-check small">';
        echo '<input class="align-text-bottom" type="checkbox" value="comments" id="buddybot-default-wizard-data-type-comments">';
        echo '<label class="" for="buddybot-default-wizard-data-type-comments">';
        esc_html_e('Comments', 'butddybot');
        echo '</label>';
        echo '</div>';

        echo '</div>';
    }

    private function progressBarBlock()
    {
        echo '<div class="row">';

        echo '<div class="col-md-4 text-end">';
        $this->moIcon('flag');
        echo '</div>';

        echo '<div class="col-md-4 text-end">';
        $this->moIcon('flag');
        echo '</div>';

        echo '<div class="col-md-4 text-end">';
        $this->moIcon('flag');
        echo '</div>';

        echo '<div class="col-md-12">';
        echo '<div class="progress" role="progressbar" style="height: 5px;">';
        echo '<div class="progress-bar" style="width: 0%"></div>';
        echo '</div>';
        echo '</div>';

        echo '</div>';
    }

    private function messagesBlock() {
        echo '<div id="buddybot-default-wizard-alerts" class="my-4">';
        $this->standardMessage();
        $this->failureMessage();
        echo '</div>';
    }

    private function standardMessage()
    {
        echo '<div id="buddybot-default-wizard-success-alert" class="text-muted text-center small visually-hidden" role="alert">';
        echo 'A simple danger alert—check it out!';
        echo '</div>';
    }

    private function failureMessage()
    {
        echo '<div id="buddybot-default-wizard-failure-alert"  class="text-danger text-center small visually-hidden" role="alert">';
        echo 'A simple danger alert—check it out!';
        echo '</div>';
    }
    
}