<?php

require_once __DIR__ . '/setLibelTerritoire.php';
if (!function_exists('displayFdLogement')) {

    function displayFdLogement($data, $territoire, $fiche)
    {
        foreach ($data as $title => $table) {
            echo '<h4 class="text-center">' . $title . '</h4>';
            echo '<table class="table table-bordered">';
            echo '<thead>';
            for ($i = 0; $i < 1; $i++) {
                echo '<tr class="active">';
                echo '<th></th><th></th>';
                foreach ($table as $key1 => $value1) {
                    echo '<th class="th_1">' . $key1 . '</th><th class="th_2"></th>';
                }
                echo '</tr>';
                echo '<tr class="active">';
                echo '<th></th><th>Territoire</th>';
                foreach ($table as $key1 => $value1) {
                    echo '<th>Nb</th><th>%</th>';
                }
                echo '</tr>';
            }
            echo '</thead>';
            echo '<tbody>';
            //parcours du tableau contenant tous les territoires a afficher (communes, secteurs, epci, scot, dept...)
            foreach ($territoire as $terr) {
                $toDisplay = true;
                $libel = setLibelTerritoire($terr, $fiche, $toDisplay);
                if ($toDisplay) {
                    $code = isset($isCommune) ? $isCommune : '';
                    echo '<tr>';
                    echo '<td class="territoire">' . $code . '</td><td class="territoire">' . $libel . '</td>';
                    foreach ($table as $key1 => $value1) {
                        foreach ($value1 as $key2 => $value2) {
                            if (strstr($key2, '_')) {
                                $key2 = strstr($key2, '_');
                                $key2 = str_replace('_', '', $key2);
                                $table[$key1][$key2] = $value2;
                            }
                        }
                    }
                    foreach ($table as $key1 => $value1) {
                        echo '<td>' . round($value1[$terr]['nb'], 0) . '</td><td>' . round($value1[$terr]['pc'], 0) . '</td>';
                    }
                    echo '</tr>';
                }
            }
            $isCommune = null;
            echo '</tbody>';
            echo '</table>';
        }
    }

}