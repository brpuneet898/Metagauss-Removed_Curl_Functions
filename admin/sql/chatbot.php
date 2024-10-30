<?php

namespace BuddyBot\Admin\Sql;

class Chatbot extends \BuddyBot\Admin\Sql\MoRoot
{
    protected function setTable()
    {
        $this->table = $this->config->getDbTable('chatbot');
    }

    public function getFirstChatbotId()
    {
        global $wpdb;
        $chatbot = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT id FROM %i ORDER BY id ASC LIMIT 1',
                $this->table
            )
        );
        if(empty($chatbot)) {
            return false;
        } else {
            return $chatbot[0]->id;
        }
    }

    public function createChatbot($chatbot_data)
    {
        $data = $chatbot_data;
        global $wpdb;
        $insert = $wpdb->insert(
            $this->table,
            $data,
            array('%s', '%s', '%s')
        );

        if ($insert !== false) {
            return $wpdb->insert_id;
        } else {
            return false;
        }

    }

    public function updateChatbot($chatbot_data)
    {
        $where = array('id'=> $chatbot_data['id']);
        $data = $chatbot_data;
        unset($data['id']);
        
        global $wpdb;
        $update = $wpdb->update(
            $this->table,
            $data,
            $where,
            array('%s', '%s', '%s'),
            array('%d')
        );

        return $update;

    }
}