<?php

$territoire = array();
foreach ($fiche->perimetre['codeEtude'] as $code) {
    $territoire[] = $code;
}
if (isset($fiche->perimetre['perimComp']) && !empty($fiche->perimetre['perimComp'])) {
    ksort($fiche->perimetre['perimComp']);
    foreach ($fiche->perimetre['perimComp'] as $value) {
        $territoire[] = $value;
    }
    if (in_array('secteur', $fiche->perimetre['perimComp'])) {
        $territoire = classerTerritoire($fiche, $territoire);
    }
}
ob_start();
//$n = 0;
//var_dump($dataSet);
$data = $dataSet[$source];
//foreach ($dataSet as $source => $data) {
    switch ($source) {
        case 'fd_logemt':
            echo '<div class="row">';
            echo '<div class="col" id="' . $tCroisement['var_croise_ancre'] . '">';
            echo '<h1>Fiche ' . $fiche->ficheType . ' :: ' . $tCroisement['var_croise_lib'] . ' :: ' . $fiche->perimetre['annee'] . '</h1>';
            displayFdLogement($data, $territoire, $fiche);
            echo '</div>';
            echo '</div>';
            break;
        case 'insee_histo_pop':
            $annee = $_SESSION['perimetre']['anneeInseeHistoPop'];
            $annee_5 = ($annee - 6);
            $annee_10 = ($annee_5 - 5);
            echo '<div class="row">';
            echo '<div class="col" id="' . $tCroisement[0]['var_croise_ancre'] . '">';
            echo '<h1>Fiche ' . $fiche->ficheType . ' ::  Evolution de la population ' . $annee_10 . ' - '. $annee_5 . ' - ' . $annee . ' </h1>';
            displayInseeHistoPop($data['insee_histo_pop'], $territoire, $fiche);
            echo '</div>';
            echo '</div>';
            break;
        case 'sitadel_commence':
            foreach ($tCroisement as $key => $value) {                
                $tCroisement[$key]['var_croise_ancre'] = str_replace('nb_log_autorises', 'nb_log_commences', $value['var_croise_ancre']);
                $tCroisement[$key]['var_croise_lib'] = str_replace('autorisés', 'commencés', $value['var_croise_lib']);
            }
            unset($tCroisement[0]);
            echo '<div class="row">';
            echo '<div class="col" id="sitadel_commence">';
            echo '<h1>Fiche ' . $fiche->ficheType . ' ::  Sit@del Logements commencés </h1>';
            displaySitadel($data, $territoire, $fiche, $tCroisement);
            echo '</div>';
            echo '</div>';
            break;
        case 'sitadel_commence_neuf_ancien':
            echo '<div class="row">';
            echo '<div class="col" id="sitadel_commence_neuf_ancien">';
            echo '<h1>Fiche ' . $fiche->ficheType . ' ::  Sit@del Logements commencés neuf/ancien ordinaire ' . $fiche->perimetre['anneeSitadelNeufAncien'] . '</h1>';
            displaySitadelOrdinaire($data, $territoire, $fiche, $tCroisement);
            echo '</div>';
            echo '</div>';
            break;
        case 'sitadel_commence_utilisation':
            echo '<div class="row">';
            echo '<div class="col" id="sitadel_commence_utilisation">';
            echo '<h1>Fiche ' . $fiche->ficheType . ' ::  Sit@del Logements commencés utilisation ordinaire ' . $fiche->perimetre['anneeSitadelUtilisation'] . '</h1>';
            displaySitadelOrdinaire($data, $territoire, $fiche, $tCroisement);
            echo '</div>';
            echo '</div>';
            break;
        case 'sitadel_autorise':
            echo '<div class="row">';
            echo '<div class="col" id="sitadel_autorise">';
            echo '<h1>Fiche ' . $fiche->ficheType . ' ::  Sit@del Logements autorisés </h1>';
            displaySitadel($data, $territoire, $fiche, $tCroisement);
            echo '</div>';
            echo '</div>';
            break;
        case 'rpls':
            echo '<div class="row">';
            echo '<div class="col" id="' . $tCroisement[0]['var_croise_ancre'] . '">';
            echo '<h1>Fiche ' . $fiche->ficheType . ' ::  RPLS </h1>';
            displayRpls($data, $territoire, $fiche);
            echo '</div>';
            echo '</div>';
            break;
        case 'sne':
            echo '<div class="row">';
            $annee = array_keys($data['sne_detail']);
            echo '<div class="col" id="sne_detail">';
            echo '<h1>Fiche ' . $fiche->ficheType . ' ::  Demande au 1/1/' . $annee[0] . ' NB d’attributions en  ' . $annee[0] . '</h1>';
            displaySne($data, $territoire, $fiche, $tCroisement);
            echo '</div>';
            echo '</div>';
            break;
        case 'filosofi':
            echo '<div class="row">';
            echo '<div class="col" id="filosofi">';
            echo '<h1>Fiche ' . $fiche->ficheType . ' ::  FiLoSoFi </h1>';
            displayFilosofi($data, $territoire, $fiche, $tCroisement);
            echo '</div>';
            echo '</div>';
            break;
        case 'artificialisation':
            echo '<div class="row">';
            $annee = array_keys($data['artificialisation_detail']);
            echo '<div class="col" id="artificialisation_detail">';
            echo '<h1>Fiche ' . $fiche->ficheType . ' ::  Artificialisation des sols :: 2009-1/1/' . $annee[0] . ' </h1>';
            displayArtificialisation($data, $territoire, $fiche, $tCroisement);
            echo '</div>';
            echo '</div>';
            break;
        case 'lovac':
            echo '<div class="row">';
            echo '<div class="col" id="lovac">';
            echo '<h1>Fiche ' . $fiche->ficheType . ' :: LOVAC au 01/01/' . $fiche->perimetre['anneeLovac'].'</h1>';
            displayLovac($data, $territoire, $fiche, $tCroisement);
            echo '</div>';
            echo '</div>';
            break;
        default :
            break;
    }
    //$n++;
//}
ob_end_flush();
