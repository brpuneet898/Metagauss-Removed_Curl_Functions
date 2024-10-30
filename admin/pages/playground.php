<div class="p-5">

<?php

$buddybot_checks = new BuddyBot\Admin\InitialChecks();

if ($buddybot_checks->hasErrors()) {
    return;
}

$mo_playground_page = new \BuddyBot\Admin\Html\Views\Playground();
$mo_playground_page->getHtml();
add_action('admin_footer', array($mo_playground_page, 'pageJs'));

$mo_playground_requests = new \BuddyBot\Admin\Requests\Playground();
add_action('admin_footer', array($mo_playground_requests, 'requestsJs'));
?>

</div>