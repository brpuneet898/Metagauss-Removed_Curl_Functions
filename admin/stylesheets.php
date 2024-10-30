<?php
namespace BuddyBot\Admin;

use BuddyBot\Traits\Singleton;

final class StyleSheets extends \BuddyBot\Admin\MoRoot
{
    use Singleton;

    protected function isInternalPage()
    {
        if (key_exists('page', $_GET) and strpos($_GET['page'], 'buddybot') === 0) {
            return true;
        } else {
            return false;
        }
    }

    protected function pluginLevelScripts() 
    {
        $bootstrap_css = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css';
        $bootstrap_js = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js';
        $jquery_js = 'https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js';
        $material_symbols = 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,300,0,0';
        
        if ($this->isInternalPage()) {
            wp_enqueue_style($this->config::PREFIX . '-material-symbols-css', $material_symbols);
            wp_enqueue_style($this->config::PREFIX . '-bootstrap-css', $bootstrap_css);
            wp_enqueue_script($this->config::PREFIX . '-bootstrap-js', $bootstrap_js);
            wp_enqueue_script($this->config::PREFIX . '-jquery-js', $jquery_js);
            wp_enqueue_style($this->config::PREFIX . '-global-css', $this->config->getRootUrl() . 'admin/css/buddybot.css');

        }
    }

    protected function pageLevelScripts()
    {
        if ($this->isInternalPage()) {
            $css_file_name = str_replace('buddybot-','', $_GET['page']);
            $css_file_url = $this->config->getRootUrl() . 'admin/css/' . $css_file_name . '.css';
            wp_enqueue_style($_GET['page'], $css_file_url);
        }
    }

    public function adminStyleSheets()
    {
        $this->pluginLevelScripts();
        $this->pageLevelScripts();
    }

    public function __construct()
    {
        $this->setAll();
        add_action('init', array($this, 'adminStyleSheets'));
    }
}