<?php

require_once __DIR__ . '/setLibelTerritoire.php';
if (!function_exists('displayEvolPopInsee')) {

    function displayEvolPopInsee($table, $territoire, $fiche, $decimal)
    {
        //parcours du tableau contenant tous les territoires a afficher (communes, secteurs, epci, scot, dept...)
        foreach ($territoire as $terr) {
            $toDisplay = $code = true;
            $libel = setLibelTerritoire($terr, $fiche, $toDisplay, $code);
            if (!$toDisplay) {
                continue;
            }
            foreach ($table as $codeGeo => $table2) {
                if ($codeGeo == $terr) {
                    echo '<tr>';
                    echo '<td class="territoire">' . $code . '</td><td class="territoire">' . $libel . '</td>';
                    foreach ($table2 as $var => $value) {
                        $round = strstr($var, '_2_') ? $decimal : 0;
                        $val = ($round == $decimal) ? number_format($value, $round, ',', ' ') : round($value, $round);
                        echo '<td>' . $val . '</td>';
                    }
                    echo '</tr>';
                }
            }
        }
    }

}