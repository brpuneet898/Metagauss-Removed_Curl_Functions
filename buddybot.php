<?php
/**
 * Plugin Name:       BuddyBot AI - Custom AI Assistant and Chat Agent
 * Description:       Create and connect BuddyBot with AI Assistant, syncronize site data and publish on the frontend.
 * Version:           1.0.0
 * Requires at least: 6.2
 * Requires PHP:      8.0
 * Author URI:        https://profiles.wordpress.org/buddybot/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       buddybot
 * Domain Path:       /languages
 * Network:           False
*/

namespace BuddyBot;

//exit if the file is accessed directly.
if (!defined('WPINC')) die;

require_once(ABSPATH . 'wp-admin/includes/plugin.php');

function fileNotFound($file) {
    \deactivate_plugins(plugin_basename(__FILE__));
    wp_die();
}

if (is_readable(plugin_dir_path(__FILE__) . 'loader.php')) {
    require_once plugin_dir_path(__FILE__) . 'loader.php';
} else {
    fileNotFound(plugin_dir_path(__FILE__) . 'loader.php');
}

spl_autoload_register(array(__NAMESPACE__ . '\Loader', 'loadClass'));

//----------Admin Code--------//

if (is_admin()) {
    $buddybot_admin_menu = new Admin\AdminMenu();
    $buddybot_admin_stylesheets = new Admin\StyleSheets();
    $buddybot_chatbot_responses = new Admin\Responses\ChatBot();
    $buddybot_datasync_responses = new Admin\Responses\DataSync();
    $buddybot_assistants_responses = new Admin\Responses\Assistants();
    $buddybot_assistant_responses = new Admin\Responses\EditAssistant();
    $buddybot_playground_responses = new Admin\Responses\Playground();
    $buddybot_default_wizard_responses = new Admin\Responses\DefaultBuddyBotWizard();
    $buddybot_settings_responses = new Admin\Responses\Settings();
}

//----------Public Code--------//

if (!is_admin()) {
    $buddybot_shortcodes = new Frontend\ShortCodes();
}

$buddybot_responses = new Frontend\Responses\BuddybotResponses();

$buddybot_db = new MoDb();
register_activation_hook(__FILE__, array($buddybot_db, 'installPlugin'));