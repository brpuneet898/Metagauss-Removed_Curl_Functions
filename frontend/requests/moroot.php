<?php
namespace BuddyBot\Frontend\Requests;

use \BuddyBot\Traits\Singleton;

class MoRoot extends \BuddyBot\Frontend\Moroot
{

    use Singleton;

    public function localJs()
    {
        $name =  new \ReflectionClass($this);
        $name =  $name->getShortName();
        $js_id = 'buddybot-' . strtolower($name) . '-local-js';
        echo '<script id="' . esc_attr($js_id) . '">';
        $this->ajaxUrl();
        $this->shortcodeJs();
        echo '</script>';
    }

    protected function ajaxUrl()
    {
        $ajax_url = admin_url('admin-ajax.php');
        echo '
        const ajaxurl = "' . esc_url($ajax_url) . '";
        ';
    }
}