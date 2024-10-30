<div class="p-5">

<?php

$buddybot_checks = new BuddyBot\Admin\InitialChecks();

if ($buddybot_checks->hasErrors()) {
    return;
}

$mo_files_page = new \BuddyBot\Admin\Html\Views\OrgFiles();
$mo_files_page->getHtml();

$mo_files_requests = new \BuddyBot\Admin\Requests\OrgFiles();
add_action('admin_footer', array($mo_files_requests, 'requestsJs'));
?>

</div>