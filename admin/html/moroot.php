<?php

namespace BuddyBot\Admin\Html;

class MoRoot extends \BuddyBot\Admin\MoRoot
{
    protected function pageHeading($heading)
    {
        echo '<div class="mb-3">';
        echo '<h3>';
        echo esc_html($heading);
        echo '</h3>';
        echo '</div>';
    }

    protected function loaderBtn(string $type = 'primary', string $id = '', string $label = '')
    {
        $class = 'btn btn-' . $type;
        echo '<button id="' . esc_attr($id) . '" class="' . esc_attr($class) . '" type="button">';
        
        echo '<span class="buddybot-loaderbtn-label">';
        echo esc_html($label);
        echo '</span>';

        echo '<span class="buddybot-loaderbtn-spinner spinner-border spinner-border-sm visually-hidden" aria-hidden="true"></span>';
        
        echo '</button>';
    }

    protected function moIcon($icon)
    {
        echo '<span class="material-symbols-outlined" style="font-size:20px;vertical-align:sub;">';
        echo esc_html($icon);
        echo '</span>';
    }
    public function getHtml()
    {
        
    }
}