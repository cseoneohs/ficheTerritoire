<?php

require_once __DIR__ . '/setHeaderTable.php';
require_once __DIR__ . '/setLibelTerritoire.php';
if (!function_exists('displayRpls')) {

    function displayRpls($dataset, $territoire, $fiche)
    {                
        foreach ($dataset as $data) {
            foreach ($data as $title => $table) {
                echo '<h4 class="text-center"> Nombre de logements ' . $title . '</h4>';
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
        if ((key($dataset) != 'rpls') || !isset($dataCarto)) {
            return;
        }

        $variable['var'] = $variable['lib'] = ['Part offerts à la location', 'Vacants %', 'Offerts à la location'];
        $symb = ['Part offerts à la location' => '%', 'Vacants %' => '%', 'Offerts à la location' => ''];
        $dataJsonAll = array();
        foreach ($dataset['rpls']['rpls_detail'][$_SESSION['perimetre']['anneeRpls']] as $key => $value) {

            for ($i = 0; $i < count($variable['var']); $i++) {
                if (!isset($value[$variable['var'][$i]])) {
                    return;
                }
                $dataJsonAll[$i][$key] = ['var' => !is_null($value[$variable['var'][$i]]) ? round($value[$variable['var'][$i]]) : 0];
            }
        }

        require_once (dirname(dirname(__FILE__)) . '/carto/displayCarto.php');
    }

}
