<?php

require_once __DIR__ . '/setLibelTerritoire.php';
if (!function_exists("displaySitadelOrdinaire")) {

    function displaySitadelOrdinaire($dataset, $territoire, $fiche, $tCroisement = null)
    {
        $n = 0;
        foreach ($dataset as $contexte => $data) {

            foreach ($data as $title => $table) {
                if (is_array($tCroisement) && $n >= 0) {
                    echo '<div class="wrap table-responsive-sm" id="' . $tCroisement[$n]['var_croise_ancre'] . '">';
                } else {
                    echo '<div class="wrap table-responsive-sm">';
                }
                echo '<h4 class="text-center"> Nombre de logements en date réelle en ' . $title . '</h4>';
                $firstItem = key($table);
                echo '<table class="table table-sm table-bordered">';
                echo '<thead>';
                echo '<tr>';
                echo '<th></th><th>Territoire</th>';
                foreach ($table[$firstItem] as $key => $value) {
                    if (strpos($key, "_nb") || strpos($key, "_total")) {
                        echo '<th>' . str_replace('_', ' ', str_replace('_nb', '', $key)) . '</th>';
                    } else {
                        echo '<th></th>';
                    }
                }
                echo '</tr>';
                echo '<tr>';
                echo '<th></th><th></th>';
                foreach ($table[$firstItem] as $key => $value) {
                    if (strpos($key, "_nb") || strpos($key, "_total")) {
                        echo '<th>Nb</th>';
                    } else {
                        echo '<th>%</th>';
                    }
                }
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                //parcours du tableau contenant tous les territoires a afficher (communes, secteurs, epci, scot, dept...)
                foreach ($territoire as $terr) {
                    $toDisplay = $code = true;
                    $libel = setLibelTerritoire($terr, $fiche, $toDisplay, $code);
                    if (!$toDisplay) {
                        continue;
                    }
                    foreach ($table as $codeGeo => $values) {
                        if ($codeGeo == $terr) {
                            echo '<tr>';
                            echo '<td class="territoire">' . $code . '</td>';
                            echo '<td class="territoire">' . $libel . '</td>';
                            foreach ($values as $value) {
                                $val = is_null($value) ? '' : round($value, 1);
                                echo '<td>' . $val . '</td>';
                            }
                            echo '</tr>';
                        }
                    }
                }
                echo '</tbody>';
                echo '</table>';
                echo '</div>';
                $n++;
            }
        }
    }

}