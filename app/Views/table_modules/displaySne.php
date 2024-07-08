<?php

require_once __DIR__ . '/setLibelTerritoire.php';
if (!function_exists('displaySne')) {

    function displaySne($dataset, $territoire, $fiche)
    {
        foreach ($dataset as $contexte => $data) {

            foreach ($dataset as $data) {
                foreach ($data as $title => $table) {
                    echo '<table class="table table-bordered">';
                    echo '<thead>';
                    echo '<tr class="active">';
                    echo '<th></th><th>Territoire</th>';
                    echo '<th>Demandeurs</th>';
                    echo '<th>Attributions</th>';
                    echo '<th>Pression</th>';
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
                        foreach ($table as $codeGeo => $table2) {
                            if ($codeGeo == $terr) {
                                echo '<tr>';
                                echo '<td class="territoire">' . $code . '</td><td class="territoire">' . $libel . '</td>';
                                foreach ($table2 as $value) {
                                    $val = is_null($value) ? '' : round($value, 1);
                                    echo '<td>' . $val . '</td>';
                                }
                                echo '</tr>';
                            }
                        }
                    }
                    echo '</tbody>';
                    echo '</table>';
                }
            }
        }
    }

}