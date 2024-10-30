<?php

namespace BuddyBot\Admin\Html\Views\Settings;

class MoRoot extends \BuddyBot\Admin\Html\Views\MoRoot
{
    protected function setSql()
    {
        $this->sql = \BuddyBot\Admin\Sql\Settings::getInstance(); 
    }
    
    public function getHtml()
    {
        return '';
    }

    protected function optionHtml(string $id = '', string $label = '', string $control = '', string $description = '')
    {
        $html = '<tr>';
		$html .= '<th>';
		$html .= '<label for="' . esc_attr($id) . '">';
        $html .= esc_html($label);
		$html .= '</label>';
		$html .= '</th>';
		$html .= '<td>';
		$html .= $control;
		$html .= '<p class="description">';
		$html .= esc_html($description);
        $html .= '</p>';
		$html .= '</td>';
	    $html .= '</tr>';
        
        return $html;
    }
}