<?php

require_once __DIR__ . '/setHeaderTable.php';
require_once __DIR__ . '/setLibelTerritoire.php';
if (!function_exists('displayArtificialisation')) {

    /**
     * Affichage Artificialisation des sols
     * @param array $dataset
     * @param array $territoire
     * @param object $fiche
     * @param array $tCroisement
     */
    function displayArtificialisation($dataset, $territoire, $fiche, $tCroisement)
    {
        $n = 0;
        foreach ($dataset as $contexte => $data) {

            foreach ($data as $tTable) {
                foreach ($tTable as $title => $table) {
                    if (is_array($tCroisement)) {
                        echo '<div class="wrap" id="' . $tCroisement[$n]['var_croise_ancre'] . '">';
                    } else {
                        echo '<div class="wrap">';
                    }
                    echo '<h4 class="text-center">  ' . $title . '</h4>';
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
                                foreach ($table2 as $value) {
                                    $value0 = $value == 0 ? 0 : number_format($value, 5, ",", "");
                                    $val = strstr($value0, ',0000') ? strstr($value0, ',0000', true) : $value0;
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

}

