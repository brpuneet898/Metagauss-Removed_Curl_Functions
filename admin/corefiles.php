<?php
namespace BuddyBot\Admin;

use BuddyBot\Traits\Singleton;

final class CoreFiles
{
    use Singleton;

    protected $config;
    protected $posts = array();
    protected $comments = array();

    protected function setConfig()
    {
        $this->config = \BuddyBot\MoConfig::getInstance();
    }

    protected function setPosts()
    {
        $this->posts = array(
            'local_path' => $this->config->getRootPath() . 'data/posts.txt',
            'remote_name' => 'WP Posts',
            'wp_option_name' => 'buddybot-posts-remote-file-id'
        );
    }

    protected function setComments()
    {
        $this->comments = array(
            'local_path' => $this->config->getRootPath() . 'data/posts.txt',
            'remote_name' => 'WP Comments',
            'wp_option_name' => 'buddybot-comments-remote-file-id'
        );
    }

    public function getLocalPath($type)
    {
        if (!empty($this->$type['local_path'])) {
            return $this->$type['local_path'];
        } else {
            return false;
        }
    }

    public function getRemoteName($type)
    {
        return $this->$type['remote_name'];
    }

    public function getWpOptionName($type)
    {
        return $this->$type['wp_option_name'];
    }
    
}