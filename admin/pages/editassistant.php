<div class="p-5">

<?php

$buddybot_checks = new BuddyBot\Admin\InitialChecks();

if ($buddybot_checks->hasErrors()) {
    return;
}

$mo_assistant_page = new \BuddyBot\Admin\Html\Views\EditAssistant();
$mo_assistant_page->getHtml();

$mo_assistant_requests = new \BuddyBot\Admin\Requests\EditAssistant();
add_action('admin_footer', array($mo_assistant_requests, 'requestsJs'));
?>

</div>