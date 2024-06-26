<?php

require_once __DIR__ . '/setHeaderTable.php';
require_once __DIR__ . '/setLibelTerritoire.php';
if (!function_exists('displaySitadel')) {

    function displaySitadel($dataset, $territoire, $fiche, $tCroisement = null)
    {
        $n = 0;
        foreach ($dataset as $contexte => $data) {

            foreach ($data as $title => $table) {
                if (is_array($tCroisement) && isset($tCroisement[$n - 1]) && $n > 0) {
                    echo '<div class="wrap" id="' . $tCroisement[$n - 1]['var_croise_ancre'] . '">';
                } else {
                    echo '<div class="wrap">';
                }
                echo '<h4 class="text-center"> Nombre de logements en date réelle en ' . $title . '</h4>';
                echo '<table class="table table-bordered">';
                echo '<thead>';
                echo setHeader($table);
                echo '</thead>';
                echo '<tbody>';
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
                                $round = ($var == 'ind_constr') ? 1 : 0;
                                $val = is_null($value) ? '' : round($value, $round);
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
