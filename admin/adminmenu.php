<?php
namespace BuddyBot\Admin;

final class AdminMenu extends \BuddyBot\Admin\MoRoot
{
    public function topLevelMenu()
    {
        $this->mainMenuItem();
        $this->playgroundSubmenuItem();
        // $this->wizardSubmenuItem();
        // $this->orgFilesSubmenuItem();
        $this->assistantsSubmenuItem();
        // $this->addFileSubmenuItem();
        $this->dataSyncSubmenuItem();
        $this->settingsSubmenuItem();
    }

    public function hiddenMenu()
    {
        $this->editAssistantSubmenuItem();
        $this->defaultBuddyBotWizard();
    }

    public function mainMenuItem()
    {
        add_menu_page(
            'BuddyBot',
            'BuddyBot',
            'manage_options',
            'buddybot-chatbot',
            array($this, 'appMenuPage'),
            'dashicons-superhero',
            6
        );
    }

    public function playgroundSubmenuItem()
    {
        add_submenu_page(
            'buddybot-chatbot',
            __('Playground', 'buddybot'),
            __('Playground', 'buddybot'),
            'manage_options',
            'buddybot-playground',
            array($this, 'playgroundMenuPage'),
            1
        );
    }

    public function wizardSubmenuItem()
    {
        add_submenu_page(
            'buddybot-chatbot',
            __('Wizard', 'buddybot'),
            __('Wizard', 'buddybot'),
            'manage_options',
            'buddybot-wizard',
            array($this, 'wizardMenuPage'),
            1
        );
    }

    public function orgFilesSubmenuItem()
    {
        add_submenu_page(
            'buddybot-chatbot',
            __('Files', 'buddybot'),
            __('Files', 'buddybot'),
            'manage_options',
            'buddybot-files',
            array($this, 'filesMenuPage'),
            1
        );
    }

    public function addFileSubmenuItem()
    {
        add_submenu_page(
            'buddybot-chatbot',
            __('Add File', 'buddybot'),
            __('Add File', 'buddybot'),
            'manage_options',
            'buddybot-addfile',
            array($this, 'addFileMenuPage'),
            1
        );
    }

    public function dataSyncSubmenuItem()
    {
        add_submenu_page(
            'buddybot-chatbot',
            __('Data Sync', 'buddybot'),
            __('Data Sync', 'buddybot'),
            'manage_options',
            'buddybot-datasync',
            array($this, 'dataSyncMenuPage'),
            1
        );
    }

    public function assistantsSubmenuItem()
    {
        add_submenu_page(
            'buddybot-chatbot',
            __('Assistants', 'buddybot'),
            __('Assistants', 'buddybot'),
            'manage_options',
            'buddybot-assistants',
            array($this, 'assistantsMenuPage'),
            1
        );
    }

    public function editAssistantSubmenuItem()
    {
        add_submenu_page(
            '',
            __('Edit Assistant', 'buddybot'),
            __('Edit Assistant', 'buddybot'),
            'manage_options',
            'buddybot-assistant',
            array($this, 'EditAssistantMenuPage'),
            1
        );
    }

    public function defaultBuddyBotWizard()
    {
        add_submenu_page(
            '',
            __('Default Buddybot', 'buddybot'),
            __('Default Buddybot', 'buddybot'),
            'manage_options',
            'buddybot-defaultwizard',
            array($this, 'defaultBuddybotWizardMenuPage'),
            1
        );
    }

    public function settingsSubmenuItem()
    {
        add_submenu_page(
            'buddybot-chatbot',
            __('Settings', 'buddybot'),
            __('Settings', 'buddybot'),
            'manage_options',
            'buddybot-settings',
            array($this, 'settingsMenuPage'),
            6
        );
    }

    public function appMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/chatbot.php');
    }

    public function filesMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/orgfiles.php');
    }

    public function playgroundMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/playground.php');
    }

    public function wizardMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/wizard.php');
    }

    public function addFileMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/addfile.php');
    }

    public function dataSyncMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/datasync.php');
    }

    public function assistantsMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/assistants.php');
    }

    public function editAssistantMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/editassistant.php');
    }

    public function defaultBuddyBotWizardMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/defaultbuddybotwizard.php');
    }

    public function settingsMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/settings.php');
    }

    public function __construct()
    {
        add_action( 'admin_menu', array($this, 'topLevelMenu'));
        add_action( 'admin_menu', array($this, 'hiddenMenu'));
    }
}