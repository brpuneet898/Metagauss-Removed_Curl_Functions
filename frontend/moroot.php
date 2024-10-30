<?php
namespace BuddyBot\Frontend;

class MoRoot extends \BuddyBot\MoRoot
{
   protected function mIcon($type = 'add')
    {
        $html = '<span class="material-symbols-outlined">';
        $html .= $type;
        $html .= '</span>';
        return $html;
    }
}