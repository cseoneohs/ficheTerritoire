<?php


namespace App\Models;

use App\Models\FicheModel;

class SitadelCommenceOrdinaireModel extends FicheModel
{
    public $perimetre = null;
    private $andGeo = '';
    private $dataSource = '';
    private $geoEtude = null;
    public $data = array();
    public $ficheType = null;
    private $annee = null;
    private $nbAnnee = 6;
    private $tAnnee = array();
    protected $tRubrique = array();

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * requete dans les tables temporaires et construction du jeu de donnees
     * @param string $where
     * @return array
     */
    public function process($contexte)
    {
        $this->dataSource = ($contexte == "sitadel_commence_neuf_ancien") ? "data_sitadel_commence_neuf_ancien_ordinaire" : "data_sitadel_commence_utilisation_ordinaire";
        $this->libRub = ($contexte == "sitadel_commence_neuf_ancien") ? "commencés neuf ancien" : "commencé utilisation ordinaire";
        $this->geoEtude = $this->perimetre['codeEtude'];
        $this->geoData = ($_SESSION['territoireEtude'] == 'commune') ? $this->perimetre['codeEtude'] : $this->perimetre['codeGeo'];
        reset($this->geoEtude);
        $data = array();
        $this->annee = ($contexte == "sitadel_commence_neuf_ancien") ? $this->perimetre['anneeSitadelNeufAncien'] : $this->perimetre['anneeSitadelUtilisation'];
        $this->tAnnee = $this->getAnnee();
        $annees = array();
        foreach ($this->tAnnee as $value) {
            $annees[] = '"' . $value['annee'] . '"';
        }
        $this->listAnnee = implode(',', $annees);
        //pour chaque commune etudie
        foreach ($this->geoEtude as $key => $value) {
            $insee = str_replace(',', "','", $this->geoData[$key]);
            $this->andGeo = " AND code_insee IN ('" . $insee . "') ";
            $data = $this->arrayMergeRecursiveMy($data, $this->getData($value));
        }
        //ce a quoi on compare
        if (isset($this->perimetre['perimComp']) && is_array($this->perimetre['perimComp'])) {
            //pour chaque territoire auquel on peut comparer
            foreach ($this->perimetre['perimComp'] as $value) {
                $this->andGeo = $this->getGeoComp($value);
                $data = $this->arrayMergeRecursiveMy($data, $this->getData($value));
            }
        }
        return $data;
    }

    /**
     * requetes des donnees
     * @param  string $geo
     * @return array
     */
    private function getData($geo)
    {
        $data = [];
        if ($this->dataSource == "data_sitadel_commence_neuf_ancien_ordinaire") {
            $data = $this->getDataNeufAncien($geo);
        } else {
            $data = $this->getDataUtilisation($geo);
        }
        return $data;
    }

    /**
     * requête sur data_sitadel_commence_neuf_ancien_ordinaire
     * @param string $geo
     * @return array le tableau contenant les données
     */
    private function getDataNeufAncien($geo)
    {
        $data = array();
        $totConstruction = 'COALESCE(commences_individuels_purs_construction_nouvelle, 0)+'
                . 'COALESCE(commences_individuels_groupes_construction_nouvelle, 0)+'
                . 'COALESCE(commences_collectifs_construction_nouvelle, 0)';
        $totBatExistant = 'COALESCE(commences_individuels_purs_construction_sur_batiment_existant, 0)+'
                . 'COALESCE(commences_individuels_groupes_construction_sur_batiment_existant, 0)+'
                . 'COALESCE(commences_collectifs_construction_sur_batiment_existant, 0)';
        $totCommences = 'COALESCE(commences_individuels_purs_construction_nouvelle, 0)+'
                . 'COALESCE(commences_individuels_groupes_construction_nouvelle, 0)+'
                . 'COALESCE(commences_collectifs_construction_nouvelle, 0)+'
                . 'COALESCE(commences_individuels_purs_construction_sur_batiment_existant, 0)+'
                . 'COALESCE(commences_individuels_groupes_construction_sur_batiment_existant, 0)+'
                . 'COALESCE(commences_collectifs_construction_sur_batiment_existant, 0)';
        $sql = 'SELECT '
                . 'SUM(`commences_individuels_purs_construction_nouvelle`) AS "commences_individuels_purs_construction_nouvelle_nb", '
                . '(SUM(`commences_individuels_purs_construction_nouvelle`)/ SUM(`commences_individuels_purs_total`))*100 AS "commences_individuels_purs_construction_nouvelle_pc",'
                . 'SUM(`commences_individuels_purs_construction_sur_batiment_existant`) AS "commences_individuels_purs_construction_sur_batiment_existant_nb", '
                . '(SUM(`commences_individuels_purs_construction_sur_batiment_existant`)/ SUM(`commences_individuels_purs_total`))*100 AS "commences_individuels_purs_construction_sur_batiment_existant_pc", '
                . 'SUM(`commences_individuels_purs_total`) AS "commences_individuels_purs_total", '
                ///
                . 'SUM(commences_individuels_groupes_construction_nouvelle) AS "commences_individuels_groupes_construction_nouvelle_nb", '
                . '(SUM(commences_individuels_groupes_construction_nouvelle) / SUM(commences_individuels_groupes_total))*100 AS "commences_individuels_groupes_construction_nouvelle_pc", '
                . 'SUM(commences_individuels_groupes_construction_sur_batiment_existant) AS "commences_individuels_groupes_construction_sur_batiment_existant_nb", '
                . '(SUM(commences_individuels_groupes_construction_sur_batiment_existant) / SUM(commences_individuels_groupes_total))*100 AS "commences_individuels_groupes_construction_sur_batiment_existant_pc", '
                . 'SUM(`commences_individuels_groupes_total`) AS "commences_individuels_groupes_total",'
                ///
                . 'SUM(commences_collectifs_construction_nouvelle) AS "commences_collectifs_construction_nouvelle_nb", '
                . '(SUM(commences_collectifs_construction_nouvelle) / SUM(commences_collectifs_total))*100 AS "commences_collectifs_construction_nouvelle_pc", '
                . 'SUM(commences_collectifs_construction_sur_batiment_existant) AS "commences_collectifs_construction_sur_batiment_existant_nb", '
                . '(SUM(commences_collectifs_construction_sur_batiment_existant) / SUM(commences_collectifs_total))*100 AS "commences_collectifs_construction_sur_batiment_existant_pc", '
                . 'SUM(`commences_collectifs_total`) AS "commences_collectifs_total", '
                ///
                . 'SUM(' . $totConstruction . ') AS "tot_construction_nouvelle_nb", '
                . '(SUM(' . $totConstruction . ') / SUM(' . $totCommences . '))*100 AS "tot_construction_nouvelle_pc", '
                . 'SUM(' . $totBatExistant . ') AS "tot_batiment_existant_nb", '
                . '(SUM(' . $totBatExistant . ') / SUM(' . $totCommences . '))*100 AS "tot_batiment_existant_pc", '
                . 'SUM(' . $totCommences . ') AS "tot_commences_total"'
                . ' FROM ' . $this->dataSource;
        $sql1 = $sql . " WHERE annee IN(" . $this->listAnnee . ")" . $this->andGeo;
        $query1 = $this->db->query($sql1);
        $result1 = $query1->getResultArray();
        $data['sitadel_commence_neuf_ancien_ordinaire_detail'][$this->tAnnee[5]['annee'] . ' - ' . $this->tAnnee[0]['annee']][$geo] = $result1[0];
        if ($this->ficheType == 'detail') {
            $i = 0;
            $this->tRubrique[$i]['var_croise_lib'] = "Nombre de logements " . $this->libRub . " en date réelle en " . $this->tAnnee[5]['annee'] . ' - ' . $this->tAnnee[0]['annee'];
            $this->tRubrique[$i]['var_croise_ancre'] = 'neuf_ancien_' . $i;
            $i++;
            foreach ($this->tAnnee as $val) {
                $sql2 = $sql . " WHERE annee = " . $val['annee'] . $this->andGeo;
                $query2 = $this->db->query($sql2);
                $result = $query2->getResultArray();
                $data['sitadel_commence_neuf_ancien_ordinaire_detail'][$val['annee']][$geo] = $result[0];
                //if (empty($this->tRubrique[$i])) {
                //$lib = $i == 0 ? ' - ' .$this->anneePopInsee : '';
                $this->tRubrique[$i]['var_croise_lib'] = "Nombre de logements " . $this->libRub . " en date réelle en " . $val['annee'];
                $this->tRubrique[$i]['var_croise_ancre'] = 'neuf_ancien_' . $i;
                //}
                $i++;
            }
        }
        return $data;
    }

    /**
     * requête sur data_sitadel_commence_utilisation_ordinaire
     * @param string $geo
     * @return array le tableau contenant les données
     */
    private function getDataUtilisation($geo)
    {
        $data = array();
        $totCommencesIndGroupe = 'COALESCE(commences_individuels_purs_occupation_personnelle, 0)+'
                . 'COALESCE(commences_individuels_purs_vente, 0)+'
                . 'COALESCE(commences_individuels_purs_location, 0)+'
                . 'COALESCE(commences_individuels_purs_location_vente, 0)+'
                . 'COALESCE(commences_individuels_purs_non_rempli, 0)';
        $totCommnecesInd = 'COALESCE(commences_individuels_groupes_occupation_personnelle, 0)+'
                . 'COALESCE(commences_individuels_groupes_vente, 0)+'
                . 'COALESCE(commences_individuels_groupes_location, 0)+'
                . 'COALESCE(commences_individuels_groupes_location_vente, 0)+'
                . 'COALESCE(commences_individuels_groupes_non_rempli, 0)';
        $totCommencesCollectifs = 'COALESCE(commences_collectifs_occupation_personnelle, 0)+'
                . 'COALESCE(commences_collectifs_vente, 0)+'
                . 'COALESCE(commences_collectifs_location, 0)+'
                . 'COALESCE(commences_collectifs_location_vente, 0)+'
                . 'COALESCE(commences_collectifs_non_rempli, 0)';
        $totTotal = ' COALESCE(commences_individuels_purs_occupation_personnelle, 0)+'
                . ' COALESCE(commences_individuels_groupes_occupation_personnelle, 0)+'
                . ' COALESCE(commences_collectifs_occupation_personnelle, 0)+'
                . ' COALESCE(commences_individuels_purs_vente, 0)+'
                . ' COALESCE(commences_individuels_groupes_vente, 0)+'
                . ' COALESCE(commences_collectifs_vente, 0)+'
                . ' COALESCE(commences_individuels_purs_location, 0)+'
                . ' COALESCE(commences_individuels_groupes_location, 0)+'
                . ' COALESCE(commences_collectifs_location, 0)+'
                . ' COALESCE(commences_individuels_purs_location_vente, 0)+'
                . ' COALESCE(commences_individuels_groupes_location_vente, 0)+'
                . ' COALESCE(commences_collectifs_location_vente, 0)+'
                . ' COALESCE(commences_individuels_purs_non_rempli, 0)+'
                . ' COALESCE(commences_individuels_groupes_non_rempli, 0)+'
                . ' COALESCE(commences_collectifs_non_rempli, 0)';
        $sql = 'SELECT '
                ///Individuel pur
                . 'SUM(commences_individuels_purs_occupation_personnelle) AS "commences_individuels_purs_occupation_personnelle_nb",'
                . '(SUM(commences_individuels_purs_occupation_personnelle)/ SUM(' . $totCommencesIndGroupe . '))*100 AS "commences_individuels_purs_occupation_personnelle_pc",'
                . 'SUM(commences_individuels_purs_vente) AS "commences_individuels_purs_vente_nb",'
                . '(SUM(commences_individuels_purs_vente)/ SUM(' . $totCommencesIndGroupe . ')) *100 AS "commences_individuels_purs_vente_pc",'
                . 'SUM(commences_individuels_purs_location) AS "commences_individuels_purs_location_nb",'
                . '(SUM(commences_individuels_purs_location)/ SUM(' . $totCommencesIndGroupe . ')) *100 AS "commences_individuels_purs_location_pc",'
                . 'SUM(commences_individuels_purs_location_vente) AS "commences_individuels_purs_location_vente_nb",'
                . '(SUM(commences_individuels_purs_location_vente)/ SUM(' . $totCommencesIndGroupe . ')) *100 AS "commences_individuels_purs_location_vente_pc",'
                . 'SUM(commences_individuels_purs_non_rempli) AS "commences_individuels_purs_non_rempli_nb",'
                . '(SUM(commences_individuels_purs_non_rempli)/ SUM(' . $totCommencesIndGroupe . ')) *100 AS "commences_individuels_purs_non_rempli_pc",'
                ///individuel groupe
                . 'SUM(commences_individuels_groupes_occupation_personnelle) AS "commences_individuels_groupes_occupation_personnelle_nb",'
                . '(SUM(commences_individuels_groupes_occupation_personnelle)/ SUM(' . $totCommnecesInd . '))*100 AS "commences_individuels_groupes_occupation_personnelle_pc",'
                . 'SUM(commences_individuels_groupes_vente) AS "commences_individuels_groupes_vente_nb",'
                . '(SUM(commences_individuels_groupes_vente)/ SUM(' . $totCommnecesInd . ')) *100 AS "commences_individuels_groupes_vente_pc",'
                . 'SUM(commences_individuels_groupes_location) AS "commences_individuels_groupes_location_nb",'
                . '(SUM(commences_individuels_groupes_location)/ SUM(' . $totCommnecesInd . ')) *100 AS "commences_individuels_groupes_location_pc",'
                . 'SUM(commences_individuels_groupes_location_vente) AS "commences_individuels_groupes_location_vente_nb",'
                . '(SUM(commences_individuels_groupes_location_vente)/ SUM(' . $totCommnecesInd . ')) *100 AS "commences_individuels_groupes_location_vente_pc",'
                . 'SUM(commences_individuels_groupes_non_rempli) AS "commences_individuels_groupes_non_rempli_nb",'
                . '(SUM(commences_individuels_groupes_non_rempli)/ SUM(' . $totCommnecesInd . ')) *100 AS "commences_individuels_groupes_non_rempli_pc",'
                ///collectif
                . 'SUM(commences_collectifs_occupation_personnelle) AS "commences_collectifs_occupation_personnelle_nb",'
                . '(SUM(commences_collectifs_occupation_personnelle)/ SUM(' . $totCommencesCollectifs . '))*100 AS "commences_collectifs_occupation_personnelle_pc",'
                . 'SUM(commences_collectifs_vente) AS "commences_collectifs_vente_nb",'
                . '(SUM(commences_collectifs_vente)/ SUM(' . $totCommencesCollectifs . ')) *100 AS "commences_collectifs_vente_pc",'
                . 'SUM(commences_collectifs_location) AS "commences_collectifs_location_nb",'
                . '(SUM(commences_collectifs_location)/ SUM(' . $totCommencesCollectifs . ')) *100 AS "commences_collectifs_location_pc",'
                . 'SUM(commences_collectifs_location_vente) AS "commences_collectifs_location_vente_nb",'
                . '(SUM(commences_collectifs_location_vente)/ SUM(' . $totCommencesCollectifs . ')) *100 AS "commences_collectifs_location_vente_pc",'
                . 'SUM(commences_collectifs_non_rempli) AS "commences_collectifs_non_rempli_nb",'
                . '(SUM(commences_collectifs_non_rempli)/ SUM(' . $totCommencesCollectifs . ')) *100 AS "commences_collectifs_non_rempli_pc", '
                ///total
                . 'SUM(COALESCE(commences_individuels_purs_occupation_personnelle,0)+COALESCE(commences_individuels_groupes_occupation_personnelle,0))+COALESCE(commences_collectifs_occupation_personnelle,0) AS "total_occupation_personnelle_nb",'
                . '(SUM(COALESCE(commences_individuels_purs_occupation_personnelle,0)+COALESCE(commences_individuels_groupes_occupation_personnelle,0)+COALESCE(commences_collectifs_occupation_personnelle,0))/ SUM(' . $totTotal . '))*100 AS "total_occupation_personnelle_pc",'
                . 'SUM(COALESCE(commences_individuels_purs_vente,0)+COALESCE(commences_individuels_groupes_vente,0)+COALESCE(commences_collectifs_vente,0)) AS "total_vente_nb",'
                . '(SUM(COALESCE(commences_individuels_purs_vente,0)+COALESCE(commences_individuels_groupes_vente,0)+COALESCE(commences_collectifs_vente,0))/ SUM(' . $totTotal . '))*100 AS "total_vente_pc",'
                . 'SUM(COALESCE(commences_individuels_purs_location,0)+COALESCE(commences_individuels_groupes_location,0)+COALESCE(commences_collectifs_location,0)) AS "total_location_nb",'
                . '(SUM(COALESCE(commences_individuels_purs_location,0)+COALESCE(commences_individuels_groupes_location,0)+COALESCE(commences_collectifs_location,0))/ SUM(' . $totTotal . '))*100 AS "total_location_pc",'
                . 'SUM(COALESCE(commences_individuels_purs_location_vente,0)+COALESCE(commences_individuels_groupes_location_vente,0)+COALESCE(commences_collectifs_location_vente,0)) AS "total_location_vente_nb",'
                . '(SUM(COALESCE(commences_individuels_purs_location_vente,0)+COALESCE(commences_individuels_groupes_location_vente,0)+COALESCE(commences_collectifs_location_vente,0))/ SUM(' . $totTotal . '))*100 AS "total_location_vente_pc",'
                . 'SUM(COALESCE(commences_individuels_purs_non_rempli,0)+COALESCE(commences_individuels_groupes_non_rempli,0)+COALESCE(commences_collectifs_non_rempli,0)) AS "total_non_rempli_nb",'
                . '(SUM(COALESCE(commences_individuels_purs_non_rempli,0)+COALESCE(commences_individuels_groupes_non_rempli,0)+COALESCE(commences_collectifs_non_rempli,0))/ SUM(' . $totTotal . '))*100 AS "total_non_rempli_pc",'
                . 'SUM(' . $totTotal . ') AS "total_total" FROM ' . $this->dataSource;
        $sql1 = $sql . " WHERE annee IN(" . $this->listAnnee . ")" . $this->andGeo;
        $query1 = $this->db->query($sql1);
        $result1 = $query1->getResultArray();
        $data['sitadel_commence_utilisation_ordinaire_detail'][$this->tAnnee[5]['annee'] . ' - ' . $this->tAnnee[0]['annee']][$geo] = $result1[0];
        if ($this->ficheType == 'detail') {
            $i = 0;
            $this->tRubrique[$i]['var_croise_lib'] = "Nombre de logements " . $this->libRub . " en date réelle en " . $this->tAnnee[5]['annee'] . " - " . $this->tAnnee[0]['annee'];
            $this->tRubrique[$i]['var_croise_ancre'] = 'utilisation_' . $i;
            $i++;
            foreach ($this->tAnnee as $val) {
                $sql2 = $sql . " WHERE annee = " . $val['annee'] . $this->andGeo;
                $query2 = $this->db->query($sql2);
                $result = $query2->getResultArray();
                $data['sitadel_commence_utilisation_ordinaire_detail'][$val['annee']][$geo] = $result[0];
                //if (empty($this->tRubrique[$i])) {
                //$lib = $i == 0 ? ' - ' .$this->anneePopInsee : '';
                $this->tRubrique[$i]['var_croise_lib'] = "Nombre de logements " . $this->libRub . " en date réelle en " . $val['annee'];
                $this->tRubrique[$i]['var_croise_ancre'] = 'utilisation_' . $i;
                //}
                $i++;
            }
        }
        return $data;
    }

    /**
     *
     * @return array
     */
    private function getAnnee()
    {
        $perimAnnee = $this->dataSource == 'data_sitadel_commence_neuf_ancien_ordinaire' ? $this->perimetre['anneeSitadelNeufAncien'] : $this->perimetre['anneeSitadelUtilisation'];
        $sql = "SELECT DISTINCT(annee) FROM " . $this->dataSource . " WHERE annee <= " . $perimAnnee . " order by annee DESC limit " . $this->nbAnnee;
        $query = $this->db->query($sql);
        $result = $query->getResultArray();
        return($result);
    }
}
