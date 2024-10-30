<?php
namespace BuddyBot;

use BuddyBot\Traits\Singleton;
final class bbOptions
{
    use Singleton;

    protected $config;
    protected $table;

    protected function setConfig()
    {
        $this->config = \BuddyBot\MoConfig::getInstance();
    }

    protected function setTable()
    {
        $this->table = $this->config->getDbTable('settings');
    }

    public function getOption(string $name, string $fallback = '')
    {
        global $wpdb;
        $option_value =  $wpdb->get_var(
            $wpdb->prepare(
                'SELECT option_value FROM %i WHERE option_name = %s',
                $this->table, $name
            )
        );

        if ($option_value === null) {
            return $fallback;
        }

        $option_value = $this->decryptKey($name, $option_value);
        return $option_value;
    }

    private function decryptKey($name, $option_value)
    {
        $method = 'decrypt' . str_replace('_', '', $name);

        if (method_exists($this, $method)) {
            return $this->$method($option_value);
        } else {
            return $option_value;
        }
    }

    protected function decryptOpenAiApiKey($option_value)
    {
        $cipher = 'aes-128-cbc';
        $config = MoConfig::getInstance();
        $key = $config->getProp('c_key');

        return openssl_decrypt(
            $option_value,
            $cipher,
            $key,
            0,
            '6176693754375346'
        );
    }
}