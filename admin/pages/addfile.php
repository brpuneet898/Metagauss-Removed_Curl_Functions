<div class="p-5">

<?php

$buddybot_checks = new BuddyBot\Admin\InitialChecks();

if ($buddybot_checks->hasErrors()) {
    return;
}

$mo_addfile_page = new \BuddyBot\Admin\Html\Views\AddFile();
$mo_addfile_page->getHtml();
add_action('admin_footer', array($mo_addfile_page, 'pageJs'));

$mo_addfile_requests = new \BuddyBot\Admin\Requests\AddFile();
add_action('admin_footer', array($mo_addfile_requests, 'requestsJs'));
?>

</div>