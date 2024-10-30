<div class="p-5">

<?php

$buddybot_checks = new BuddyBot\Admin\InitialChecks();

if ($buddybot_checks->hasErrors()) {
    return;
}

$default_buddybot_wizard_page = new \BuddyBot\Admin\Html\Views\DefaultBuddyBotWizard();
$default_buddybot_wizard_page->getHtml();

$default_buddybot_wizard_requests = new \BuddyBot\Admin\Requests\DefaultBuddyBotWizard();
add_action('admin_footer', array($default_buddybot_wizard_requests, 'requestsJs'));

?>

</div>