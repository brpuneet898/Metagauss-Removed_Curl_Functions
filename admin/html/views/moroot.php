<?php

namespace BuddyBot\Admin\Html\Views;

class MoRoot extends \BuddyBot\Admin\Html\MoRoot
{
    protected $sql;

    protected function setSql()
    {
        $class_name = (new \ReflectionClass($this))->getShortName();
        $file_path = $this->config->getRootPath() . 'admin/sql/' . strtolower($class_name) . '.php';

        if (file_exists($file_path)) {
            $class_name = '\BuddyBot\Admin\Sql\\' . $class_name;
            $this->sql = $class_name::getInstance(); 
        }
    }

    protected function alertContainer()
    {
        echo '<div id="buddybot-alert-container" class="alert alert-danger small w-50" role="alert" style="display:none;">';
        echo '</div>';
    }

    protected function moSpinner()
    {
        echo '<div class="buddybot-dataload-spinner spinner-border spinner-border-sm text-primary" role="status">';
        echo '<span class="visually-hidden">Loading...</span>';
        echo '</div>';
    }

    protected function pageModals()
    {
        
    }

    public function pageJs()
    {

    }
}