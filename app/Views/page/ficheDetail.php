<?php
echo $html;
?>
<div class="d-print-none">
    <a class="btn btn-outline-secondary" role="button" data-toggle="tooltip" data-placement="top" title="Exporter les données dans différents onglets (lent)" href="<?php echo base_url('/fiche/export/'); ?>">Exporter le fichier</a>
    <br><br>
</div>
<?php
if (($_SESSION['territoireEtude'] == "commune") && ($_SESSION['perimetre']['geo'] == "commune") && isset($dataCarto)) {
    displayCarto($dataSet, $dataCarto);
}

