<?php
/**
 * Vue périmètre (sélection des sources et périmètres géographiques)
 */
$attributes = array('class' => '', 'id' => 'form_perimetre');
if (isset($reponse)) {
    echo '<p class="text-success">' . $reponse . '</p>';
}
$terr = ($_SESSION['territoireEtude'] == 'commune') ? 'la commune' : 'l\'EPCI';
if (isset($_SESSION['reponseImport'])) {
    echo '<p class="text-success">' . $_SESSION['reponseImport'] . '</p>';
}
?>
<div class="row justify-content-center">
    <div class="col-10 form">
        <h1>Production d'une fiche à <?php echo $terr; ?></h1>
        <div class="card bg-light mb-3 pb-3">
            <div class="card-header"><h2>Choisir le(s) périmètre(s) d'étude</h2></div>
            <?php
            if (isset($validation)) {
                if (is_array($validation)) {
                    $validation = $validation[0];
                }
                echo '<div class="text-warning">' . $validation . '</div>';
            }
            ?>
            <?php echo form_open('perimetre', $attributes); ?>
            <div class="card-body">
                <h4 class="card-title">Sources de données</h4>
                <div class="form-row">
                    <div class="form-check form-check-inline col-md-4">
                        <input class="form-check-input" type="checkbox" id="fd_logemt" name="fd_logemt">
                        <label class="form-check-label" for="fd_logemt">Recensement Insee</label>
                    </div>
                    <div class="col-md-2">
                        <select class="custom-select" id="perimAnnee" name="perimAnnee">
                            <?php
                            foreach ($annee as $key => $value) {
                                echo '<option value="' . $value['annee'] . '">' . $value['annee'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6"></div>
                </div>
                <div class="form-row">
                    <div class="form-check form-check-inline col-md-4">
                        <input class="form-check-input" type="checkbox" id="insee_histo_pop" name="insee_histo_pop">
                        <label class="form-check-label" for="insee_histo_pop">Evol. pop. ménages Insee</label>
                    </div>
                    <div class="col-md-2">
                        <select class="custom-select" id="perimAnneeInseeHistoPop" name="perimAnneeInseeHistoPop">
                            <?php
                            foreach ($anneeInseeHistoPop as $key => $value) {
                                if ($value['annee'] > 2015) {
                                    echo '<option value="' . $value['annee'] . '">' . $value['annee'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6"></div>
                </div>
                <div class="form-row">
                    <div class="form-check form-check-inline col-md-4">
                        <input class="form-check-input" type="checkbox" id="sitadel_commence" name="sitadel_commence">
                        <label class="form-check-label" for="sitadel_commence">Sit@del commencé</label>
                    </div>
                    <div class="col-md-2">
                        <select class="custom-select" id="perimAnneeSitadel" name="perimAnneeSitadel">
                            <?php
                            $anneeMin  = min($anneeSitadel);
                            foreach ($anneeSitadel as $key => $value) {
                                $disabled = $value['annee'] > $anneeMin['annee'] + 4 ? '' : 'disabled';
                                echo '<option '.$disabled.' value="' . $value['annee'] . '">' . $value['annee'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6"></div>
                </div>
                <div class="form-row">
                    <div class="form-check form-check-inline col-md-4">
                        <input class="form-check-input" type="checkbox" id="sitadel_commence_neuf_ancien" name="sitadel_commence_neuf_ancien">
                        <label class="form-check-label" for="sitadel_commence_neuf_ancien">Sit@del commencé neuf ancien</label>
                    </div>
                    <div class="col-md-2">
                        <select class="custom-select" id="perimAnneeSitadelNeufAncien" name="perimAnneeSitadelNeufAncien">
                            <?php
                            foreach ($anneeSitadelNeufAncien as $key => $value) {
                                echo '<option value="' . $value['annee'] . '">' . $value['annee'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6"></div>
                </div>
                <div class="form-row">
                    <div class="form-check form-check-inline col-md-4">
                        <input class="form-check-input" type="checkbox" id="sitadel_commence_utilisation" name="sitadel_commence_utilisation">
                        <label class="form-check-label" for="sitadel_commence_utilisation">Sit@del commencé utilisation</label>
                    </div>
                    <div class="col-md-2">
                        <select class="custom-select" id="perimAnneeSitadelUtilisation" name="perimAnneeSitadelUtilisation">
                            <?php
                            foreach ($anneeSitadelUtilisation as $key => $value) {
                                echo '<option value="' . $value['annee'] . '">' . $value['annee'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6"></div>
                </div>
                <div class="form-row">
                    <div class="form-check form-check-inline col-md-4">
                        <input class="form-check-input" type="checkbox" id="sitadel_autorise" name="sitadel_autorise">
                        <label class="form-check-label" for="sitadel_autorise">Sit@del autorisé</label>
                    </div>
                    <div class="col-md-2">
                        <select class="custom-select" id="perimAnneeSitadelAutorise" name="perimAnneeSitadelAutorise">
                            <?php
                            $anneeMin  = min($anneeSitadelAutorise);
                            foreach ($anneeSitadelAutorise as $key => $value) {
                                $disabled = $value['annee'] > $anneeMin['annee'] + 4 ? '' : 'disabled';
                                echo '<option '.$disabled.' value="' . $value['annee'] . '">' . $value['annee'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6"></div>
                </div>
                <div class="form-row">
                    <div class="form-check form-check-inline col-md-4">
                        <input class="form-check-input" type="checkbox" id="rpls" name="rpls">
                        <label class="form-check-label" for="rpls">RPLS</label>
                    </div>
                    <div class="col-md-2">
                        <select class="custom-select" id="perimAnneeRpls" name="perimAnneeRpls">
                            <?php
                            foreach ($anneeRpls as $key => $value) {
                                echo '<option value="' . $value['annee'] . '">' . $value['annee'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6"></div>
                </div>
                <div class="form-row">
                    <div class="form-check form-check-inline col-md-4">
                        <input class="form-check-input" type="checkbox" id="sne" name="sne">
                        <label class="form-check-label" for="sne">SNE</label>
                    </div>
                    <div class="col-md-2">
                        <select class="custom-select" id="perimAnneeSne" name="perimAnneeSne">
                            <?php
                            foreach ($anneeSne as $key => $value) {
                                echo '<option value="' . $value['annee'] . '">' . $value['annee'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6"></div>
                </div>
                <div class="form-row">
                    <div class="form-check form-check-inline col-md-4">
                        <input class="form-check-input" type="checkbox" id="filosofi" name="filosofi">
                        <label class="form-check-label" for="filosofi">FiLoSoFi</label>
                    </div>
                    <div class="col-md-2">
                        <select class="custom-select" id="perimAnneeFilosofi" name="perimAnneeFilosofi">
                            <?php
                            foreach ($anneeFilosofi as $key => $value) {
                                echo '<option value="' . $value['annee'] . '">' . $value['annee'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6"></div>
                </div>
                <div class="form-row">
                    <div class="form-check form-check-inline col-md-4">
                        <input class="form-check-input" type="checkbox" id="artificialisation" name="artificialisation">
                        <label class="form-check-label" for="artificialisation">Artificialisation</label>
                    </div>
                    <div class="col-md-2">
                        <select class="custom-select" id="perimAnneeArtificialisation" name="perimAnneeArtificialisation">
                            <?php
                            foreach ($anneeArtificialisation as $key => $value) {
                                echo '<option value="' . $value['annee'] . '">' . $value['annee'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6"></div>
                </div>
                <div class="form-row">
                    <div class="form-check form-check-inline col-md-4">
                        <input class="form-check-input" type="checkbox" id="lovac" name="lovac">
                        <label class="form-check-label" for="lovac">LOVAC</label>
                    </div>
                    <div class="col-md-2">
                        <select class="custom-select" id="perimAnneeLovac" name="perimAnneeLovac">
                            <?php
                            foreach ($anneeLovac as $key => $value) {
                                echo '<option value="' . $value['annee'] . '">' . $value['annee'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6"></div>
                </div>
            </div>
            <div class="card-body">
                <h4 class="card-title">Territoire étudié</h4>
                <div class="form-row" id="selectDept">
                    <label class="col-md-4 col-form-label" for="perimEtuDep">Département&nbsp;<i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top" title="Choix multiple possible en maintenant la touche [Contrôle] appuyée et en cliquant sur les éléments choisis"></i></label>
                    <div class="col-md-8">
                        <select class="multiple form-control" id="perimEtuDep" name="perimEtuDep[]" multiple="multiple">
                            <option value=""></option>
                            <?php
                            foreach ($dept as $key => $value) {
                                echo '<option value="' . $key . '">' . $key . ' - ' . $value . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <?php if ($_SESSION['territoireEtude'] == 'commune') : ?>
                    <div class="form-row" id="selectScot">
                        <label class="col-md-4 col-form-label" for="perimEtuScot">SCOT</label>
                        <div class="col-md-8">
                            <select class="custom-select" id="perimEtuScot" name="perimEtuScot">
                                <option value=""></option>
                                <?php
                                if (isset($scot) && is_array($scot)) {
                                    foreach ($scot as $key => $value) {
                                        echo '<option value="' . $value['codegeo'] . '">' . $value['libel'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="form-row" id="selectEpci">
                    <label class="col-md-4 col-form-label" for="perimEtuEpci">EPCI&nbsp;<i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top" title="Choix multiple possible en maintenant la touche [Contrôle] appuyée et en cliquant sur les éléments choisis"></i></label>
                    <div class="col-md-8">
                        <select class="multiple form-control" id="perimEtuEpci" name="perimEtuEpci[]" multiple="multiple">
                            <?php
                            if (isset($tEpci) AND is_array($tEpci)) {
                                foreach ($tEpci as $key => $value) {
                                    echo '<option value="' . $value['code_epci'] . '">' . $value['lib_epci'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <?php if ($_SESSION['territoireEtude'] == 'commune') : ?>
                    <div class="form-row" id="selectSecteur">
                        <label class="col-md-4 col-form-label" for="perimEtuSect">Secteur</label>
                        <div class="col-md-8">
                            <select class="multiple form-control" id="perimEtuSect" name="perimEtuSect[]" multiple="multiple">
                                <option value=""></option>
                                <?php
                                if (isset($tSect) AND is_array($tSect)) {
                                    foreach ($tSect as $key => $value) {
                                        echo '<option value="' . $value['codegeo'] . '">' . $value['libel'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row" id="selectCommune">
                        <label class="col-md-4 col-form-label" for="perimEtuCom">Communes&nbsp;<span class="glyphicon glyphicon-info-sign" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Choix multiple possible en maintenant la touche [Contrôle] appuyée et en cliquant sur les éléments choisis"></span></label>
                        <div class="col-md-8">
                            <select class="multiple form-control" id="perimEtuCom" name="perimEtuCom[]" multiple="multiple">
                                <option value=""></option>
                                <?php
                                if (isset($tComm) AND is_array($tComm)) {
                                    foreach ($tComm as $key => $value) {
                                        echo '<option value="' . $value['codegeo'] . '">' . $value['libgeo'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row" id="selectIris">
                        <label class="col-md-4 col-form-label" for="perimEtuIris">IRIS</label>
                        <div class="col-md-8">
                            <select class="custom-select" id="perimEtuIris" name="perimEtuIris">
                                <option value=""></option>

                            </select>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <h4 class="card-title">Territoire de comparaisons</h4>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="chkTerritoire[6]" id="chkFrance" value="france" />
                    <label class="form-check-label pr-3" for="chkFrance">France</label>
                    <input class="form-check-input" type="checkbox" name="chkTerritoire[5]" id="chkRegion" value="region" />
                    <label class="form-check-label pr-3" for="chkRegion">Région</label>
                    <input class="form-check-input" type="checkbox" name="chkTerritoire[4]" id="chkDpt" value="departement" />
                    <label class="form-check-label pr-3" for="chkDpt">Département</label>
                    <?php if ($_SESSION['territoireEtude'] == 'commune') { ?>
                        <input class="form-check-input" type="checkbox" name="chkTerritoire[3]" id="chkScot" value="scot" />
                        <label class="form-check-label pr-3" for="chkScot">SCOT</label>
                        <input class="form-check-input" type="checkbox" name="chkTerritoire[2]" id="chkEpci" value="epci" />
                        <label class="form-check-label pr-3" for="chkEpci">EPCI</label>
                        <span title="Sélectionner un secteur dans la liste des secteurs">
                        <input class="form-check-input" type="checkbox" name="chkTerritoire[1]" id="chkSecteur" disabled value="secteur" />
                        <label class="form-check-label pr-3" for="chkSecteur">Secteur</label>
                        </span>
                        <input class="form-check-input" type="checkbox" name="chkTerritoire[0]" id="chkCommune" value="commune" />
                        <label class="form-check-label pr-3" for="chkCommune">Commune</label>
                    <?php } ?>
                </div>
            </div>
            <div class="card-body">
                <fieldset class="fiche">
                    <h4 class="card-title">Fiches à produire</h4>
                    <div class="form-check form-check-inline">
                        <label class="form-check-label pr-3" for="chkDetail">
                            <input class="form-check-input" type="radio" id="chkDetail" name="chkFiche[]" value="detail" checked />Fiche détaillée</label>
                        <label class="form-check-label" for="chkSynthese">
                            <input class="form-check-input" type="radio" id="chkSynthese" name="chkFiche[]" value="synthese" />Fiche de synthèse</label>
                    </div>
                </fieldset>
                <div class="clear"></div>
                <fieldset class="var_fd_logemt" id="var_fd_logemt" style="display: none;">
                    <h4 class="card-title">Variables fd_logemt</h4>
                    <div class="var_fd_logemt_col center-block">
                        <?php foreach ($varFdLogement as $key => $var) { ?>
                            <label class="btn btn-outline-dark btn-block" for="<?php echo $var['var_id']; ?>">
                                <input class="form-check-input pl-3" type="checkbox" id="<?php echo $var['var_id']; ?>" checked name="var_fd_logemt[]" value="<?php echo $var['var_id']; ?>" /><br><?php echo $var['var_croise_lib']; ?>
                            </label>
                        <?php } ?>
                    </div>
                    <button type="button" class="btn btn-outline-secondary" id="btn_toggle_var_fd_logemt" data-toggle="button">Basculer</button>
                    <button type="button" class="btn btn-secondary" id="btn_all_var_fd_logemt">Tout</button>
                </fieldset>
                <div class="clear"></div>
                <input type="submit" class="btn btn-primary float-right" value="Envoyer" title="Soumettre le choix des périmètres étudiés" />
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-10 form">
        <div class="card bg-light mb-3 pb-3">
            <div class="card-header">
                <h2>Importer des secteurs ou des SCOT dans la BDD</h2>
            </div>
            <div class="card-body">
                <div id="importScot">
                    <a href=" <?php echo base_url('/ressource/ts_geo_scot.csv') ?>">Télécharger le csv pour importer un nouveau SCOT</a>
                </div>
                <div id="importSecteur">
                    <a href=" <?php echo base_url('/ressource/ts_geo_secteur.csv') ?>">Télécharger le csv pour importer un nouveau secteur</a>
                </div>
                <form id="importGeo" name="importGeo" method="post" enctype="multipart/form-data" action="<?php echo base_url('perimetre/importGeoFile'); ?>">
                    <input type="hidden" name="type" value="importGeo" />
                    <input type="hidden" value="importGeo" />
                    <fieldset>
                        <div class="form-row">
                            <label class="col-sm-5 col-form-label" for="selectGeo">Choisir le niveau géographique à créer</label>
                            <div class="col-sm-5">
                                <select class="custom-select" id="selectGeo" name="selectGeo">
                                    <option value=""></option>
                                    <option value="secteur">secteur</option>
                                    <option value="scot">scot</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <label class="col-sm-5 col-form-label" for="importFileGeo">Fichier à importer</label>
                            <div class="col-sm-5">
                                <input id="importFileGeo" name="importFileGeo" type="file" accept="text/csv" onchange="checkfile(this);" />
                            </div>
                        </div>
                        <p class="p-3 mb-2 bg-light text-dark">Sélectionner tout d'abord le niveau géographique à importer pour obtenir le modèle de fichier csv.&nbsp; Les colonnes code_insee et code_dep doivent être aux formats texte et avoir respectivement une taille de 5 caractères et 2 caractères minimum. Reporter autant de fois qu'il y a de code commune renseigné les libellés secteur ou SCOT sur la première colonne et idem pour le département ou l'EPCI sur la troisième colonne. Le séparateur de champ doit être un ";".<br>Les noms de fichiers doivent être ts_geo_scot.csv ou ts_geo_secteur.csv</p>

                    </fieldset>
                    <input type="submit" class="btn btn-primary float-right" value="Envoyer" title="Soumettre l'importation des secteurs ou des SCOT dans la BDD" />
                </form>
            </div>
        </div>
    </div>
</div>

