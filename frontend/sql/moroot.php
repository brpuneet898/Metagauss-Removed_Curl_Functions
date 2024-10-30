<?php
namespace BuddyBot\Frontend\Sql;

use \BuddyBot\Traits\Singleton;

class MoRoot extends \BuddyBot\Frontend\Moroot
{
    use singleton;

    protected function isOptionSet($name)
    {
        global $wpdb;
        return $wpdb->get_var(
            $wpdb->prepare(
                'SELECT EXISTS(SELECT 1 FROM %i WHERE option_name=%s LIMIT 1)',
                $this->config->getDbTable('settings'), $name
            )
        );
    }

    public function getOption($option_name, $default_value = '')
    {
        if ($this->isOptionSet($option_name)) {
            global $wpdb;
            return $wpdb->get_var(
                $wpdb->prepare(
                    'SELECT option_value FROM %i WHERE option_name = %s LIMIT 1',
                    $this->config->getDbTable('settings'),
                    $option_name
                )
            );
        } else {
            return $default_value;
        }
    }
}