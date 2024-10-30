<div class="p-5">

<?php

$buddybot_checks = new BuddyBot\Admin\InitialChecks();

if ($buddybot_checks->hasErrors()) {
    return;
}

$mo_datasync_page = new \BuddyBot\Admin\Html\Views\DataSync();
$mo_datasync_page->getHtml();
add_action('admin_footer', array($mo_datasync_page, 'pageJs'));

$mo_datasync_requests = new \BuddyBot\Admin\Requests\DataSync();
add_action('admin_footer', array($mo_datasync_requests, 'requestsJs'));
?>

</div>