<?php

namespace BuddyBot;

class MoDb
{   
    protected $config;
    protected $charset;
    
    public function setPreliminaries()
    {
        global $wpdb;
        $this->charset = $wpdb->get_charset_collate();
        $this->config = MoConfig::getInstance();
    }
    
    private function addThreadsTable()
    {
        $table_name = $this->config->getDbTable('threads');
        $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        thread_id varchar(100),
        user_id mediumint(9),
        thread_name varchar(100),
        created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
        )  $this->charset;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($sql);
    }
    
    private function addChatbotTable()
    {
        $table_name = $this->config->getDbTable('chatbot');
        $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        chatbot_name varchar(256),
        chatbot_description varchar(1024),
        assistant_id varchar(100),
        author mediumint(9),
        created_on datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        last_editor mediumint(9),
        edited_on datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
        )  $this->charset;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($sql);
    }

    private function addSettingsTable()
    {
        $table_name = $this->config->getDbTable('settings');
        $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        option_name varchar(256),
        option_value text,
        last_editor mediumint(9),
        edited_on datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
        )  $this->charset;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($sql);
    }
    
    public function addTables()
    {
        $this->addThreadsTable();
        $this->addChatbotTable();
        $this->addSettingsTable();
    }
    
    public function installPlugin()
    {
        $this->setPreliminaries();
        $this->addTables();
    }
    
    public function __construct()
    {
    }
}