<div class="p-5">

<?php

$buddybot_checks_data = ['custom_checks' => ['capability']];
$buddybot_checks = new BuddyBot\Admin\InitialChecks($buddybot_checks_data);

if ($buddybot_checks->hasErrors()) {
    return;
}

$mo_settings_page = new \BuddyBot\Admin\Html\Views\Settings();
$mo_settings_page->getHtml();
add_action('admin_footer', array($mo_settings_page, 'pageJs'));

$mo_settings_requests = new \BuddyBot\Admin\Requests\Settings();
add_action('admin_footer', array($mo_settings_requests, 'requestsJs'));
?>

</div>