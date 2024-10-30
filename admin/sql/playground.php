<?php

namespace BuddyBot\Admin\Sql;

class Playground extends \BuddyBot\Admin\Sql\MoRoot
{
    public function getThreadsByUserId($user_id = 0)
    {
        if (!is_user_logged_in()) {
            return;
        }

        $user_id = absint($user_id);
        $table = $this->config->getDbTable('threads');

        if ($user_id < 1) {
            $user_id = get_current_user_id();
        }

        global $wpdb;
        $this->response['result'] = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM %i WHERE user_id=%d ORDER BY id DESC',
                $table,
                $user_id
            )
        );

        return $this->returnResponse();
    }

    public function saveThreadId($thread_id)
    {
        if (!is_user_logged_in()) {
            return;
        }

        $table = $this->config->getDbTable('threads');
        
        $data = array(
            'thread_id' => $thread_id,
            'user_id' => get_current_user_id(),
            'created' => current_time('mysql', true)
        );

        $format = array('%s', '%d');

        global $wpdb;
        $insert = $wpdb->insert($table, $data, $format);
        return $insert;
    }

    public function updateThreadName($thread_id, $thread_name)
    {
        $table = $this->config->getDbTable('threads');
        
        if (strlen($thread_name) > 100) {
            $thread_name = substr($thread_name, 100);
        }

        $data = array('thread_name' => $thread_name);
        $where = array('thread_id' => $thread_id);
        $format = array('%s');
        $where_format = array('%s');

        global $wpdb;
        $wpdb->update($table, $data, $where, $format, $where_format);
    }

    public function deleteThread($thread_id)
    {
        $table = $this->config->getDbTable('threads');
        $where = array('thread_id' => $thread_id);
        $format = array('%s');

        global $wpdb;
        return $wpdb->delete($table, $where, $format);
    }
}