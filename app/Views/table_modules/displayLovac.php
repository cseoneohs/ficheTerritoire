<?php

require_once __DIR__ . '/setLibelTerritoire.php';
if (!function_exists('displayLovac')) {
    /**
     * Affichage des données LOVAC au 01/01
     * @param array $dataset
     * @param array $territoire
     * @param object $fiche
     * @param array $tCroisement
     */
    function displayLovac($dataset, $territoire, $fiche, $tCroisement = null)
    {

        $n = 0;
        foreach ($dataset as $contexte => $data) {
            foreach ($data as $title => $table) {
                if (is_array($tCroisement)) {
                    echo '<div class="wrap" id="' . $tCroisement[$n]['var_croise_ancre'] . '">';
                } else {
                    echo '<div class="wrap">';
                }
                echo '<table class="table table-bordered">';
                echo '<thead>';
                echo '<tr class="active">';
                echo '<th></th><th>Territoire</th>';
                $table2 = $table[array_key_first($table)];
                foreach ($table2 as $var => $tab) {
                    echo '<th>' . $var . '</th>';
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
                    foreach ($table as $codeGeo => $table2) {
                        if ($codeGeo == $terr) {                           
                            echo '<tr>';
                            echo '<td class="territoire">' . $code . '</td><td class="territoire">' . $libel . '</td>';
                            foreach ($table2 as $var => $value) {
                                if (is_null($value)) {
                                    $val = '';
                                } else {
                                    $val = str_contains($var, 'Taux') ? number_format($value, 2, ",") : round($value, 0);
                                }                                
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

