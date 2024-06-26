<?php

require_once __DIR__ . '/setLibelTerritoire.php';
if (!function_exists('displaySne')) {

    function displaySne($dataset, $territoire, $fiche, $tCroisement = null)
    {
        $n = 0;
        foreach ($dataset as $contexte => $data) {

            foreach ($data as $tTable) {
                foreach ($tTable as $title => $table) {
                    if (is_array($tCroisement) && array_key_exists($n, $tCroisement)) {
                        echo '<div class="wrap" id="' . $tCroisement[$n]['var_croise_ancre'] . '">';
                    } else {
                        echo '<div class="wrap">';
                    }
                    echo '<h4 class="text-center">  ' . $title . '</h4>';
                    echo '<table class="table table-bordered">';
                    echo '<thead>';

                    switch ($title) {
                        case 'Ancienneté de la demande':
                            echo '<tr class="active">';
                            echo '<th></th><th>Territoire</th>';
                            echo '<th colspan="2">&lt; 1 an </th>';
                            echo '<th colspan="2"> 1 à &lt;  2 ans </th>';
                            echo '<th colspan="2"> 2 à &lt; 3 ans </th>';
                            echo '<th colspan="2"> 3 à &lt; 4 ans </th>';
                            echo '<th colspan="2"> 4 à &lt; 5 ans </th>';
                            echo '<th colspan="2"> 5 à &lt; 10 ans </th>';
                            echo '<th colspan="2">10 ans ou + </th>';
                            echo '<th colspan="2">Total</th>';
                            echo '</tr>';
                            echo '<tr class="active">';
                            echo '<th></th><th></th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '</tr>';
                            break;
                        case 'Type de logement demandé/attribué':
                            echo '<tr class="active">';
                            echo '<th></th><th>Territoire</th>';
                            echo '<th colspan="2">T1</th>';
                            echo '<th colspan="2">T2</th>';
                            echo '<th colspan="2">T3</th>';
                            echo '<th colspan="2">T4</th>';
                            echo '<th colspan="2">T5 + </th>';
                            echo '<th colspan="2">Total</th>';
                            echo '</tr>';
                            echo '<tr class="active">';
                            echo '<th></th><th></th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '</tr>';
                            break;
                        case 'Age du titulaire':
                            echo '<tr class="active">';
                            echo '<th></th><th>Territoire</th>';
                            echo '<th colspan="2">Moins de 30 ans</th>';
                            echo '<th colspan="2">30-39 ans</th>';
                            echo '<th colspan="2">40-49 ans</th>';
                            echo '<th colspan="2">50-65 ans</th>';
                            echo '<th colspan="2">65ans +</th>';
                            echo '<th colspan="2">Total</th>';
                            echo '</tr>';
                            echo '<tr class="active">';
                            echo '<th></th><th></th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '</tr>';
                            break;
                        case 'Taille du ménage':
                            echo '<tr class="active">';
                            echo '<th></th><th>Territoire</th>';
                            echo '<th colspan="2">1 personne</th>';
                            echo '<th colspan="2">2 personnes</th>';
                            echo '<th colspan="2">3 personnes</th>';
                            echo '<th colspan="2">4 personnes</th>';
                            echo '<th colspan="2">5 personnes et +</th>';
                            echo '<th colspan="2">Total</th>';
                            echo '</tr>';
                            echo '<tr class="active">';
                            echo '<th></th><th></th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '</tr>';
                            break;
                        case 'Composition du ménage':
                            echo '<tr class="active">';
                            echo '<th></th><th>Territoire</th>';
                            echo '<th colspan="2">Personnes seules</th>';
                            echo '<th colspan="2">Couples sans enfant</th>';
                            echo '<th colspan="2">Familles monoparentales 1 enfant</th>';
                            echo '<th colspan="2">Familles monoparentales 2 enfants</th>';
                            echo '<th colspan="2">Familles monoparentales 3 enfants  et +</th>';
                            echo '<th colspan="2">Couples avec 1 enfant</th>';
                            echo '<th colspan="2">Couples avec 2 enfants</th>';
                            echo '<th colspan="2">Couples avec 3 enfants et +</th>';
                            echo '<th colspan="2">Total</th>';
                            echo '</tr>';
                            echo '<tr class="active">';
                            echo '<th></th><th></th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '</tr>';
                            break;
                        case 'Motif de la demande':
                            echo '<tr class="active">';
                            echo '<th></th><th>Territoire</th>';
                            echo '<th colspan="2" title="Démolition, Logement non habitable, Logement repris, Procédure d\'expulsion, Sans logement propre">Problème lié au logement<span class="hidden-print glyphicon glyphicon-info-sign" aria-hidden="true"></span></th>';
                            echo '<th colspan="2" title="Divorce, séparation, Décohabitation, Futur couple,Regroupement familial, Rapprochement famille">Changements familiaux<span class="hidden-print glyphicon glyphicon-info-sign" aria-hidden="true"></span></th>';
                            echo '<th colspan="2">Raisons de santé / handicap</th>';
                            echo '<th colspan="2">Logement inadapté (taille / prix)</th>';
                            echo '<th colspan="2">Problèmes environnement/voisinage</th>';
                            echo '<th colspan="2">Lié à la situation professionnelle</th>';
                            echo '<th colspan="2" title="Violences familiales, Rapprochement services, Propriétaire en difficulté, Autre motif">Autres motifs<span class="hidden-print glyphicon glyphicon-info-sign" aria-hidden="true"></span></th>';
                            echo '<th colspan="2">Total</th>';
                            echo '</tr>';
                            echo '<tr class="active">';
                            echo '<th></th><th></th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '</tr>';
                            break;
                        case 'Statut Antérieur':
                            echo '<tr class="active">';
                            echo '<th></th><th>Territoire</th>';
                            echo '<th colspan="2">hlm</th>';
                            echo '<th colspan="2">Locataire du Parc Privé</th>';
                            echo '<th colspan="2">Propriétaire occupant</th>';
                            echo '<th colspan="2">Décohabitants</th>';
                            echo '<th colspan="2" title="Chez particulier, Logé gratuit, Sous-loc. ou hebergé temporaire">Hébergés<span class="hidden-print glyphicon glyphicon-info-sign" aria-hidden="true"></span></th>';
                            echo '<th colspan="2" title="Centre enfance famille, Résidence étudiant, RHVS, RS, foyer, Structure d\'hébergement">Foyer / Centre d\'hébergement<span class="hidden-print glyphicon glyphicon-info-sign" aria-hidden="true"></span></th>';
                            echo '<th colspan="2" title="Camping, caravaning, Hôtel, Sans abri, Squat">Statut précaires<span class="hidden-print glyphicon glyphicon-info-sign" aria-hidden="true"></span></th>';
                            echo '<th colspan="2">Total</th>';
                            echo '</tr>';
                            echo '<tr class="active">';
                            echo '<th></th><th></th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '</tr>';
                            break;
                        case 'Situation professionnelle du chef de ménage':
                            echo '<tr class="active">';
                            echo '<th></th><th>Territoire</th>';
                            echo '<th colspan="2" title="Artisan, profession libérale, CDI (ou fonctionnaire)">Emplois stables<span class="hidden-print glyphicon glyphicon-info-sign" aria-hidden="true"></span></th>';
                            echo '<th colspan="2" title="CDD, stage, intérim, apprenti">Emplois précaires<span class="hidden-print glyphicon glyphicon-info-sign" aria-hidden="true"></span></th>';
                            echo '<th colspan="2">Retraité</th>';
                            echo '<th colspan="2">Chômage</th>';
                            echo '<th colspan="2">Sans emploi</th>';
                            echo '<th colspan="2">Total</th>';
                            echo '</tr>';
                            echo '<tr class="active">';
                            echo '<th></th><th></th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '</tr>';
                            break;
                        case 'Revenus/ Plafonds PLUS':
                            echo '<tr class="active">';
                            echo '<th></th><th>Territoire</th>';
                            echo '<th colspan="2">&lt;= PLAI</th>';
                            echo '<th colspan="2">&gt; PLAI et =&lt; PLUS</th>';
                            echo '<th colspan="2">&gt; PLUS et =&lt; PLS</th>';
                            echo '<th colspan="2">&gt; PLS</th>';
                            echo '<th colspan="2">Total</th>';
                            echo '</tr>';
                            echo '<tr class="active">';
                            echo '<th></th><th></th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '<th>Nb</th><th>%</th>';
                            echo '</tr>';
                            break;
                    }


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
                                foreach ($table2 as $var=> $value) {
                                    $round = ($var == 'pression') ? 1 : 0;
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

}