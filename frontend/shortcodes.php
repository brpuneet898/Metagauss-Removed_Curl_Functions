<?php
namespace BuddyBot\Frontend;

final class ShortCodes extends \BuddyBot\Frontend\MoRoot
{
    protected $shortcodes;
    protected $frontend_theme;

    protected function setShortcodes()
    {
        $this->shortcodes = array(
            'buddybot_chat'
        );
    }

    protected function setFrontendTheme()
    {
        $this->frontend_theme = 'bootstrap';
    }

    private function addShortCodes()
    {
        foreach ($this->shortcodes as $shortcode) {
            $class = str_replace('_', '', $shortcode);

            $this->enqueuePluginStyle();
            $this->enqueuePluginScript();
            $this->enqueueViewStyle($class);
            $this->enqueueViewScript($class);

            $view_class = 'BuddyBot\Frontend\Views\\' . $this->frontend_theme . '\\' . $class;
            $view = $view_class::getInstance();
            add_shortcode($shortcode, array($view, 'shortcodeHtml'));

            $js_class = 'BuddyBot\Frontend\requests\\' . $class;
            $js = $js_class::getInstance();
            add_action('wp_footer', array($js, 'localJs'));
        }
    }

    private function enqueuePluginStyle()
    {
        wp_enqueue_style(
            'buddybot-material-symbols',
            'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,300,0,0'
        );

        switch ($this->frontend_theme) {
            case 'bootstrap':
                wp_enqueue_style(
                    'buddybot-bootstrap-style',
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css'
                );
                break;
        }
    }

    private function enqueuePluginScript()
    {
        wp_enqueue_script('buddybot-jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js');

        switch ($this->frontend_theme) {
            case 'bootstrap':
                wp_enqueue_script(
                    'buddybot-bootstrap-script',
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'
                );
                break;
        }
    }

    private function enqueueViewStyle($file)
    {
        $file_path = $this->config->getRootPath() . 'frontend/css/' . $file . '.css';
        if (file_exists($file_path)) {
            $file_url = $this->config->getRootUrl() . 'frontend/css/' . $file . '.css';
            wp_enqueue_style('buddybot-style-' . $file, $file_url);
        }

        $file_path = $this->config->getRootPath() . 'frontend/css/' . $this->frontend_theme . '/' . $file . '.css';
        if (file_exists($file_path)) {
            $file_url = $this->config->getRootUrl() . 'frontend/css/'  . $this->frontend_theme . '/' . $file . '.css';
            wp_enqueue_style('buddybot-style-' . $this->frontend_theme . '-' . $file, $file_url);
        }
    }

    private function enqueueViewScript($file)
    {
        $file_path = $this->config->getRootPath() . 'frontend/js/' . $file . '.js';
        if (file_exists($file_path)) {
            $file_url = $this->config->getRootUrl() . 'frontend/js/' . $file . '.js';
            wp_enqueue_style('buddybot-script-' . $file, $file_url);
        }

        $file_path = $this->config->getRootPath() . 'frontend/js/' . $this->frontend_theme . '/' . $file . '.js';
        if (file_exists($file_path)) {
            $file_url = $this->config->getRootUrl() . 'frontend/js/'  . $this->frontend_theme . '/' . $file . '.js';
            wp_enqueue_style('buddybot-script-' . $this->frontend_theme . '-' . $file, $file_url);
        }
    }

    public function __construct()
    {
        $this->setAll();
        $this->addShortCodes();
    }
}