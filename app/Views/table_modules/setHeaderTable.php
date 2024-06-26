<?php

/**
 * Les entêtes de colonnes des tableaux
 * @param array $table
 * @return string
 */
if (!function_exists('setHeader')) {

    function setHeader($table)
    {
        $html = '<tr class="active">';
        $html .= '<th></th><th>Territoire</th>';
        $table2 = $table[array_key_first($table)];
        foreach ($table2 as $var => $tab) {
            $html .= '<th>' . $var . '</th>';
        }
        $html .= '</tr>';
        return $html;
    }

}
