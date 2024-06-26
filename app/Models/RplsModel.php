<?php

namespace App\Models;

use App\Models\FicheModel;

class RplsModel extends FicheModel
{

    public $perimetre = null;
    private $andGeo = '';
    private $dataSource = 'data_rpls';
    private $geoEtude = null;
    public $data = array();
    public $ficheType = null;
    private $annee = null;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * requete dans les tables temporaires et construction du jeu de donnees
     * @param string $where
     * @return array
     */
    public function process()
    {
        $this->geoEtude = $this->perimetre['codeEtude'];
        $this->geoData = ($_SESSION['territoireEtude'] == 'commune') ? $this->perimetre['codeEtude'] : $this->perimetre['codeGeo'];
        reset($this->geoEtude);
        $data = array();
        $this->annee = $this->perimetre['anneeRpls'];

        //pour chaque commune etudie
        foreach ($this->geoEtude as $key => $value) {
            $insee = str_replace(',', "','", $this->geoData[$key]);
            $this->andGeo = " AND data_rpls.code_insee IN ('" . $insee . "') ";
            if ($this->annee < 2020) {
                $data = $this->arrayMergeRecursiveMy($data, $this->getDataOld($value));
            } else {
                $data = $this->arrayMergeRecursiveMy($data, $this->getData($value));
            }
        }

        //ce a quoi on compare

        if (isset($this->perimetre['perimComp']) && is_array($this->perimetre['perimComp'])) {
            //pour chaque territoire auquel on eut comparer
            foreach ($this->perimetre['perimComp'] as $value) {
                if ($value == 'france') {
                    $this->andGeo = " AND data_rpls.code_insee is TRUE";
                } else {
                    $this->andGeo = substr_replace($this->getGeoComp($value), " " . $this->dataSource . ".", 4, 1);
                }
                if ($this->annee < 2020) {
                    $data = $this->arrayMergeRecursiveMy($data, $this->getDataOld($value));
                } elseif ($this->annee >= 2020) {
                    $data = $this->arrayMergeRecursiveMy($data, $this->getData($value));
                }
            }
        }
        return $data;
    }

    /**
     * requetes des donnees à la fois dans data_rpls et dans data_cc_serie_histo_insee
     * @param  string $geo
     * @return array
     */
    private function getData($geo)
    {
        $fiche = ($this->ficheType == 'detail') ? 'rpls_detail' : 'rpls_synthese';
        $data = array();
        $anneeHistoInsee = $this->perimetre['anneeInseeHistoPop'];
        $goeInsee = str_replace("AND data_rpls.code_insee", "", $this->andGeo);
        $sql = "SELECT SUM(nb_log_parc_loc_bailleur_soc) AS 'Ensemble parc social' , "
                . "SUM(nb_log_parc_loc_loue + nb_log_parc_loc_vacant) AS 'Offerts à la location',  "
                . "(SELECT SUM(data_cc_serie_histo_insee.p_annee_rp) FROM data_cc_serie_histo_insee WHERE data_cc_serie_histo_insee.code_insee" . $goeInsee . " AND data_cc_serie_histo_insee.annee=" . $anneeHistoInsee . ") AS 'RP dernière année', "
                . "(100*(SUM(nb_log_parc_loc_loue + nb_log_parc_loc_vacant))/(SELECT SUM(data_cc_serie_histo_insee.p_annee_rp) FROM data_cc_serie_histo_insee WHERE data_cc_serie_histo_insee.code_insee" . $goeInsee . " AND data_cc_serie_histo_insee.annee=" . $anneeHistoInsee . ")) AS 'Part offerts à la location', SUM(nb_log_parc_loc_loue) AS 'Loués nb', "
                . "(SUM(nb_log_parc_loc_loue)/SUM(nb_log_parc_loc_loue + nb_log_parc_loc_vacant))*100 AS 'Loués %', "
                . "SUM(nb_log_parc_loc_vacant) AS 'Vacants nb', "
                . "(SUM(nb_log_parc_loc_vacant)/SUM(nb_log_parc_loc_loue + nb_log_parc_loc_vacant))*100 AS 'Vacants %' FROM " . $this->dataSource . " , data_cc_serie_histo_insee WHERE " . $this->dataSource . ".code_insee= data_cc_serie_histo_insee.code_insee  AND data_cc_serie_histo_insee.annee=" . $anneeHistoInsee . " AND " . $this->dataSource . ".annee=" . $this->annee . $this->andGeo;
        $query1 = $this->db->query($sql);
        $result1 = $query1->getResultArray();
        $data[$fiche][$this->annee][$geo] = $result1 ? $result1[0] : array('nb_log_parc_loc_bailleur_soc' => 0, 'nb_log_parc_loc_soc' => 0);
        if (empty($this->tRubrique[0]) && $this->ficheType == 'detail') {
            $this->tRubrique[0]['var_croise_lib'] = "Nombre de logements " . $this->annee;
            $this->tRubrique[0]['var_croise_ancre'] = 'nb_log_rpls';
        }
        return $data;
    }

    /**
     * requetes des donnees
     * @param  string $geo
     * @return array
     */
    private function getDataOld($geo)
    {
        $fiche = ($this->ficheType == 'detail') ? 'rpls_detail' : 'rpls_synthese';
        $data = array();
        $sql = "SELECT SUM(nb_log_parc_loc_bailleur_soc) AS 'Nombre de logements du parc locatif des bailleurs sociaux', SUM(nb_log_parc_loc_soc) AS 'Nombre de logements du parc locatif social' FROM " . $this->dataSource . " WHERE annee = " . $this->annee . $this->andGeo;
        $query1 = $this->db->query($sql);
        $result1 = $query1->getResultArray();
        $data[$fiche][$this->annee][$geo] = $result1 ? $result1[0] : array('nb_log_parc_loc_bailleur_soc' => 0, 'nb_log_parc_loc_soc' => 0);
        if (empty($this->tRubrique[0]) && $this->ficheType == 'detail') {
            $this->tRubrique[0]['var_croise_lib'] = "Nombre de logements " . $this->annee;
            $this->tRubrique[0]['var_croise_ancre'] = 'nb_log_rpls';
        }
        return $data;
    }
}
