<?php

require_once __DIR__ . '/setHeaderTable.php';
require_once __DIR__ . '/setLibelTerritoire.php';
if (!function_exists('displayFilosofi')) {

    function displayFilosofi($dataset, $territoire, $fiche)
    {
        foreach ($dataset as $contexte => $data) {
            foreach ($data as $title => $table) {
                echo '<h4 class="text-center"> Niveau de vie ' . $title . '</h4>';
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
                                $val = is_null($value) ? '' : round($value, 0);
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

if (!function_exists('displayCarto')) {

    /**
     *
     * @param array $dataset les données métier
     * @param array $dataCarto les données géo-spatiales
     */
    function displayCarto($dataset, $dataCarto)
    {
        if ((key($dataset) != 'filosofi') || !isset($dataCarto)) {
            return;
        }
        foreach ($dataset['filosofi']['filosofi_detail'][$_SESSION['perimetre']['anneeFilosofi']] as $key => $value) {
            foreach (array_keys($value) as $var) {
                $libels[] = $var;
            }
            break;
        }
        $variable['lib'] = $variable['var'] = $libels;
        $symb = ['Médiane du niveau de vie (€)' => '€', 'Part des ménages fiscaux imposés (%)' => '%', 'Taux de pauvreté-Ensemble (%)' => '%'];
        $dataJsonAll = array();
        foreach ($dataset['filosofi']['filosofi_detail'][$_SESSION['perimetre']['anneeFilosofi']] as $key => $value) {
            for ($i = 0; $i < count($variable['var']); $i++) {
                $dataJsonAll[$i][$key] = ['var' => !is_null($value[$variable['var'][$i]]) ? round($value[$variable['var'][$i]]) : 0];
            }
        }
        require_once (dirname(dirname(__FILE__)) . '/carto/displayCarto.php');
    }

}