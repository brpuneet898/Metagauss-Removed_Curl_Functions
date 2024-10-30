<?php

namespace BuddyBot\Traits;

trait Singleton
{
	protected $data;
	
    protected function setAll()
    {
        foreach ($this as $prop => $value) {
            $method = 'set' . str_replace('_', '', $prop);
            if (method_exists($this, $method)) {
                $this->$method();
            }
        }
    }

    public function getProp($prop_name)
    {
        if (property_exists($this, $prop_name)) {
            return $this->$prop_name;
        } else {
            return false;
        }
    }

    final public static function getInstance($data = null)
    {
        static $instances = array();
        $calledClass = get_called_class();

        if (!isset($instances[$calledClass]))
        {
            $instances[$calledClass] = new $calledClass($data);
        }

        return $instances[$calledClass];
    }
    
    final private function __clone()
    {
    }
    
    protected function __construct($data)
    {
    	$this->data = $data;
        $this->setAll();
    }
}