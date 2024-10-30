<?php
namespace BuddyBot\Frontend\Views;

class MoRoot extends \BuddyBot\Frontend\MoRoot
{
    protected $sql;

    protected function setSql()
    {
        $class_name = (new \ReflectionClass($this))->getShortName();
        $file_path = $this->config->getRootPath() . 'frontend/sql/' . strtolower($class_name) . '.php';

        if (file_exists($file_path)) {
            $class_name = '\BuddyBot\Frontend\Sql\\' . $class_name;
            $this->sql = $class_name::getInstance(); 
        }
    }

    public function shortcodeHtml($atts, $content = null)
    {
        $html = '<div class="alert alert-warning" role="alert">';
        $html .= __('No HTML found for this view.', 'buddybot');
        $html .= '</div>';
        return $html;
    }
}