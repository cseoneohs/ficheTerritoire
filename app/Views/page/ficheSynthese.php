<?php
//var_dump($dataSet);
ob_start();
$title = 'Fiche ' . $fiche->ficheType;
echo '<h1>' . $title . '</h1>';
foreach ($fiche->perimetre['codeEtude'] as $code) {
    $territoire = array();
    $territoire[] = $code;
    if (isset($fiche->perimetre['perimComp']) && !empty($fiche->perimetre['perimComp'])) {
        ksort($fiche->perimetre['perimComp']);
        foreach ($fiche->perimetre['perimComp'] as $value) {
            if ($value != 'secteur') {
                $territoire[] = $value;
            }
        }
        if (in_array('secteur', $fiche->perimetre['perimComp'])) {
            $territoire = classerTerritoire($fiche, $territoire);
        }
    }
    echo '<div class="wrap" id="' . $code . '">';
    echo '<h2>' . array_search($territoire[0], $fiche->perimetre['labelEtude']) . '</h2>';
//printf("Temps d'attente : %f", xdebug_time_index());
    $n = 0;
    foreach ($dataSet as $source => $data) {
        switch ($source) {
            case 'insee_histo_pop':
                if ($n < 1) {
                    echo '<h3> :: insee_histo_pop :: ' . $fiche->perimetre['anneeInseeHistoPop'] . '</h3>';
                }
                displayInseeHistoPop($data['insee_histo_pop'], $territoire, $fiche);
                break;
            case 'fd_logemt':
                if ($n < 2) {
                    echo '<h3> :: fd_logemt :: ' . $fiche->perimetre['annee'] . '</h3>';
                }
                displayFdLogement($data, $territoire, $fiche);
                break;
            case 'sitadel_commence':
                if ($n < 3) {
                    echo '<h3> :: Sit@del Logements commencés :: ' . $fiche->perimetre['anneeSitadel'] . '</h3>';
                }
                displaySitadel($data, $territoire, $fiche);
                break;
            case 'sitadel_autorise':
                if ($n < 4) {
                    echo '<h3> :: Sit@del Logements autorisés :: ' . $fiche->perimetre['anneeSitadelAutorise'] . '</h3>';
                }
                displaySitadel($data, $territoire, $fiche);
                break;
            case 'rpls':
                if ($n < 5) {
                    echo '<h3> :: RPLS :: ' . $fiche->perimetre['anneeRpls'] . '</h3>';
                }
                displayRpls($data, $territoire, $fiche);
                break;
            case 'sne':
                if ($n < 6) {
                    echo '<h3> :: SNE au 31/12 :: ' . $fiche->perimetre['anneeSne'] . '</h3>';
                }
                displaySne($data, $territoire, $fiche);
                break;
            case 'filosofi':
                if ($n < 7) {
                    echo '<h3> :: FiLoSoFi :: ' . $fiche->perimetre['anneeFilosofi'] . '</h3>';
                }
                displayFilosofi($data, $territoire, $fiche);
                break;
            case 'artificialisation':
                if ($n < 8) {
                    echo '<h3> :: Artificialisation des sols :: 2009  :: ' . $fiche->perimetre['anneeArtificialisation'] . '</h3>';
                }
                displayArtificialisation($data, $territoire, $fiche, null);
                break;
            case 'sitadel_commence_neuf_ancien':
                if ($n < 9) {
                    echo '<h3> :: Sit@del Logements commencés neuf/ancien ordinaire  :: ' . $fiche->perimetre['anneeSitadelNeufAncien'] . '</h3>';
                }
                displaySitadelOrdinaire($data, $territoire, $fiche);
                break;
            case 'sitadel_commence_utilisation':
                if ($n < 10) {
                    echo '<h3> :: Sit@del Logements commencés utilisation ordinaire  :: ' . $fiche->perimetre['anneeSitadelUtilisation'] . '</h3>';
                }
                displaySitadelOrdinaire($data, $territoire, $fiche);
                break;
            case 'lovac':
                if ($n < 11) {
                    echo '<h3> :: LOVAC au 01/01/ :: ' . $fiche->perimetre['anneeLovac'] . '</h3>';
                }
                displayLovac($data, $territoire, $fiche);
                break;
        }
        $n++;
    }
    echo '</div>';
}
$html = ob_get_clean();
echo $html;
$exportToExcel = new App\Libraries\ExportToExcelSingle();
$filename = $exportToExcel->export($html, $title);
?>
<div class="d-print-none">
    <a class="btn btn-outline-secondary" role="button" data-toggle="tooltip" data-placement="top" title="Exporter le HTML tel que dans un seul onglet (rapide)" href="<?php echo base_url('/fiche/download/' . $filename); ?>">Télécharger le fichier</a>
<a class="btn btn-outline-secondary" data-toggle="tooltip" data-placement="top" title="Exporter les données dans différents onglets (lent)" href="<?php echo base_url('/fiche/export/'); ?>">Exporter le fichier</a>
</div>