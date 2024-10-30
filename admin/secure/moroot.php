<?php

namespace BuddyBot\Admin\Secure;

class MoRoot extends \BuddyBot\Admin\MoRoot
{
    protected $errors = array();
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

    public function getErrors()
    {
        return $this->errors;
    }

    public function secureData($data)
    {
        $clean_data = array();

        if(!is_array($data)) {
            $this->errors[] = __('Data should be in array format.', 'buddybot');
            return;
        }

        foreach ($data as $name => $value) {
            $method = 'clean' . str_replace('_','',$name);
            $clean_data[$name] = $this->$method($value);
        }

        return $clean_data;
    }
}