<?php

namespace BuddyBot;

class MoRoot
{
    public $config;
    protected $options;

    protected function setOptions()
    {
        $this->options = bbOptions::getInstance();
    }

    protected function setConfig()
    {
        $this->config = MoConfig::getInstance();
    }

    protected function setAll()
    {
        foreach ($this as $prop => $value) {
            $method = 'set' . str_replace('_', '', $prop);
            if (method_exists($this, $method)) {
                $this->$method();
            }
        }
    }

    public function __construct()
    {
        $this->setAll();
        
    }
}