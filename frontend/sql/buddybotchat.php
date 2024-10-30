<?php
namespace BuddyBot\Frontend\Sql;

class BuddybotChat extends \BuddyBot\Frontend\Sql\Moroot
{
    public function getDefaultBuddybotId()
    {
        $table = $this->config->getDbTable('chatbot');

        global $wpdb;

        $id = $wpdb->get_var(
            $wpdb->prepare(
                'SELECT id FROM %i LIMIT 1', $table
            )
        );

        return $id;
    }

    public function getChatbot($chatbot_id)
    {
        $table = $this->config->getDbTable('chatbot');

        global $wpdb;

        $chatbot = $wpdb->get_row(
            $wpdb->prepare(
                'SELECT * FROM %i WHERE id = %d', $table, $chatbot_id
            )
        );

        return $chatbot;
    }

    public function getConversationsByUserId($user_id)
    {
        global $wpdb;
        $conversations = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM %i WHERE user_id=%d',
                $this->config->getDbTable('threads'), $user_id
            )
        );

        return $conversations;
    }

    public function saveThreadInDb($thread_id)
    {
        $table = $this->config->getDbTable('threads');
        $data = array(
            'thread_id' => $thread_id,
            'user_id' => get_current_user_id(),
            'created' => current_time('mysql', true)
        );

        global $wpdb;
        return $wpdb->insert($table, $data, array('%s', '%d', '%s'));
    }

    public function updateThreadName($thread_id, $thread_name)
    {
        $table = $this->config->getDbTable('threads');
        
        if (strlen($thread_name) > 100) {
            $thread_name = substr($thread_name, 100);
        }

        $data = array('thread_name' => $thread_name);
        $where = array('thread_id' => $thread_id);

        global $wpdb;
        $wpdb->update($table, $data, $where, array('%s'), array('%s'));
    }

    public function deleteThread($thread_id)
    {
        $table = $this->config->getDbTable('threads');
        $where = array('thread_id' => $thread_id);

        global $wpdb;
        return $wpdb->delete($table, $where, ['%s']);
    }

    public function isThreadOwner($thread_id, $user_id)
    {
        $table = $this->config->getDbTable('threads');

        global $wpdb;

        $thread_owner_id = $wpdb->get_var(
            $wpdb->prepare(
                'SELECT user_id FROM %i WHERE thread_id = %s', $table, $thread_id
            )
        );

        if ($user_id === absint($thread_owner_id)) {
            return true;
        } else {
            return false;
        }
    }
}