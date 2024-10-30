<?php

namespace BuddyBot\Admin\Sql;

class Settings extends \BuddyBot\Admin\Sql\MoRoot
{
    protected function setTable()
    {
        $this->table = $this->config->getDbTable('settings');
    }

    protected function isOptionSet($name)
    {
        global $wpdb;
        return $wpdb->get_var(
            $wpdb->prepare(
                'SELECT EXISTS(SELECT 1 FROM %i WHERE option_name=%s LIMIT 1)',
                $this->table, $name
            )
        );
    }

    public function saveOption($name, $value)
    {

        $value = $this->encryptKey($name, $value);

        global $wpdb;

        if ($this->isOptionSet($name)) {
            $wpdb->update(
                $this->table,
                array(
                    'option_value' => maybe_serialize($value),
                    'last_editor' => get_current_user_id(),
                    'edited_on' => current_time('mysql', true)
                ),
                array('option_name' => $name),
                array('%s', '%d', '%s'),
                array('%s')
            );
        } else {
            $wpdb->insert(
                $this->table,
                array(
                    'option_name' => $name,
                    'option_value' => maybe_serialize($value),
                    'last_editor' => get_current_user_id(),
                    'edited_on' => current_time('mysql', true)
                ),
                array('%s', '%s', '%d', '%s')
            );
        }
    }

    private function encryptKey($name, $value)
    {
        $method = 'encrypt' . str_replace('_', '', $name);

        if (method_exists($this, $method)) {
            return $this->$method($value);
        } else {
            return $value;
        }
    }

    private function encryptOpenAiApiKey($option_value)
    {
        $cipher = 'aes-128-cbc';
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);

        $key = $this->config->getProp('c_key');

        return openssl_encrypt(
            $option_value,
            $cipher,
            $key,
            0,
            '6176693754375346'
        );
    }
}