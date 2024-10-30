<div class="p-5">

<?php

$buddybot_checks = new BuddyBot\Admin\InitialChecks();

if ($buddybot_checks->hasErrors()) {
    return;
}

$mo_wizard_page = new \BuddyBot\Admin\Html\Views\Wizard();
$mo_wizard_page->getHtml();

$mo_wizard_requests = new \BuddyBot\Admin\Requests\Wizard();
add_action('admin_footer', array($mo_wizard_requests, 'requestsJs'));
?>

</div>