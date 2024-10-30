<?php

namespace BuddyBot\Admin\Sql;

use \BuddyBot\Traits\Singleton;

class MoRoot extends \BuddyBot\Admin\MoRoot
{
    use Singleton;
    protected $table;

    protected $response = array('');

    protected function setResponse()
    {
        $this->response = array(
            'success' => false,
            'message' => '',
            'result' => ''
        );
    }

    protected function returnResponse()
    {
        global $wpdb;
        if ($wpdb->last_error) {
            $this->response['success'] = false;
            $this->response['message'] = $wpdb->last_error;
          } else {
            $this->response['success'] = true;
          }

        return $this->response;
    }

    public function getItemById(string $type, int $id, string $output = 'OBJECT')
    {
        $table = $this->config->getDbTable($type);

        if ($table === false) {
            return false;
        }

        global $wpdb;
        $item = $wpdb->get_row(
            $wpdb->prepare(
                'SELECT * FROM %i WHERE id=%d',
                $table, absint($id)
            ), $output
        );

        return $item;
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