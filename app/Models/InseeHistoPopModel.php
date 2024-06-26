<?php

namespace App\Models;

use App\Models\FicheModel;

class InseeHistoPopModel extends FicheModel
{
    public $perimetre = null;
    //clause where supplementaire de la requete

    private $andGeo = '';
    private $dataSource = 'data_cc_serie_histo_insee';
    private $nbAnnee = 5;
    private $anneePopInsee = null;
    private $anneePopInseeFirst = null;

    /**
     * le nom de la table contenant les variables, leur libellé et leurs modalités
     * @var string
     */
    private $geoEtude = null;
    public $data = array();
    public $ficheType = null;

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
        $this->anneePopInsee = $this->perimetre['anneeInseeHistoPop'];
        $this->anneePopInseeFirst = $this->anneePopInsee - $this->nbAnnee - 1;
        $this->anneePopInseeFirst_1 = $this->anneePopInseeFirst - $this->nbAnnee;
        $this->geoEtude = $this->perimetre['codeEtude'];
        $this->geoData = ($_SESSION['territoireEtude'] == 'commune') ? $this->perimetre['codeEtude'] : $this->perimetre['codeGeo'];
        reset($this->geoEtude);
        $data = array();
        //pour chaque commune etudie
        foreach ($this->geoEtude as $key => $value) {
            $insee = str_replace(',', "','", $this->geoData[$key]);
            $this->andGeo = " AND code_insee IN ('" . $insee . "') ";
            $data = $this->arrayMergeRecursiveMy($data, $this->getData($value));
        }

        //ce a quoi on compare
        if (isset($this->perimetre['perimComp']) && is_array($this->perimetre['perimComp'])) {
            //pour chaque territoire auquel on eut comparer
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
        $dataTemp = array();
        $data = array();
        $sql0 = "SELECT
                SUM(p_n_10_pop) AS aa_population_n_10,
                SUM(p_n_5_pop) AS ab_population_n_5,
                SUM(p_annee_pop) AS ac_population_annee,
                SUM(p_n_10_rp) AS ha__menage_n_10,
                SUM(p_n_5_rp) AS hb__menage_n_5,
                SUM(p_annee_rp) AS hc__menage_annee,
                SUM(p_n_10_pmen) as ja_pop_menage_n_10,
                SUM(p_n_5_pmen) as jb_pop_menage_n_5,
                SUM(p_annee_pmen) as jc_pop_menage_annee,
                SUM(p_n_10_pmen)/SUM(p_n_10_rp) AS la__2_taille_moy_menage_n_10,
                SUM(p_n_5_pmen)/SUM(p_n_5_rp) AS lb__2_taille_moy_menage_n_5,
                SUM(p_annee_pmen)/SUM(p_annee_rp) AS lc__2_taille_moy_menage_annee,
                SUM((nais_n_5_annee+nais_n_10_annee) - (dece_n_5_annee+dece_n_10_annee)) AS cb_solde_naturel_n_10,
                SUM(nais_n_5_annee - dece_n_5_annee) AS bb_solde_naturel_n_5,
                SUM(nais_n_10_annee - dece_n_10_annee) AS ad_solde_naturel_n_5_5,
                SUM(p_annee_pop - p_n_10_pop) AS solde_temp_n_10,
                SUM(p_annee_pop - p_n_5_pop) AS solde_temp,
                SUM(p_n_5_pop - p_n_10_pop) AS solde_temp_n_5_5,
                SUM(p_annee_pop - p_n_10_pop - ((nais_n_5_annee+nais_n_10_annee) - (dece_n_5_annee+dece_n_10_annee))) AS cd_solde_migratoire_n_10,
                SUM(p_annee_pop - p_n_5_pop - (nais_n_5_annee - dece_n_5_annee)) AS bd_solde_migratoire_n_5,
                SUM(p_n_5_pop - p_n_10_pop - (nais_n_10_annee - dece_n_10_annee)) AS ae_solde_migratoire_n_5_5,
                (SUM(p_annee_pmen)/SUM(p_annee_rp)) - (SUM(p_n_10_pmen)/SUM(p_n_10_rp)) AS oc__2_desserement_menage_n_10,
                (SUM(p_annee_pmen)/SUM(p_annee_rp)) - (SUM(p_n_5_pmen)/SUM(p_n_5_rp)) AS ob__2_desserement_menage,
                (SUM(p_n_5_pmen)/SUM(p_n_5_rp)) - (SUM(p_n_10_pmen)/SUM(p_n_10_rp)) AS oa__2_desserement_menage_n_5
                FROM " . $this->dataSource;
        $sql1 = $sql0 . " WHERE annee = " . $this->anneePopInsee . $this->andGeo;
        //echo $sql1;echo '<br>';
        $query1 = $this->db->query($sql1);
        $result1 = $query1->getResultArray();
        $dataTemp['insee_histo_pop'][$geo] = $result1[0];
        $dataTemp['insee_histo_pop'][$geo]['aca__2_evol_annuel_pop_n_5_5'] = ($dataTemp['insee_histo_pop'][$geo]['aa_population_n_10'] != 0) ? ($this->getEvol($dataTemp['insee_histo_pop'][$geo]['ab_population_n_5'] / $dataTemp['insee_histo_pop'][$geo]['aa_population_n_10'], 5, true) * 100) : '';
        $dataTemp['insee_histo_pop'][$geo]['baa__2_evol_annuel_pop_n_5'] = ($dataTemp['insee_histo_pop'][$geo]['ab_population_n_5'] != 0) ? ($this->getEvol($dataTemp['insee_histo_pop'][$geo]['ac_population_annee'] / $dataTemp['insee_histo_pop'][$geo]['ab_population_n_5'], 5) * 100) : '';
        $dataTemp['insee_histo_pop'][$geo]['caa__2_evol_annuel_pop_n_10'] = ($dataTemp['insee_histo_pop'][$geo]['aa_population_n_10'] != 0) ? ($this->getEvol($dataTemp['insee_histo_pop'][$geo]['ac_population_annee'] / $dataTemp['insee_histo_pop'][$geo]['aa_population_n_10'], 10) * 100) : '';
        $dataTemp['insee_histo_pop'][$geo]['adb__2_tx_var_naturel_an_n_5_5'] = $this->getTxVarNaturelAn($dataTemp, $geo, 5, true);
        $dataTemp['insee_histo_pop'][$geo]['bc__2_tx_var_naturel_an_n_5'] = $this->getTxVarNaturelAn($dataTemp, $geo, 5);
        $dataTemp['insee_histo_pop'][$geo]['cc__2_tx_var_naturel_an_n_10'] = $this->getTxVarNaturelAn($dataTemp, $geo, 10);
        $dataTemp['insee_histo_pop'][$geo]['af__2_tx_var_migratoire_an_n_5_5'] = $this->getTxVarMigratoireAn($dataTemp, $geo, 5, true);
        $dataTemp['insee_histo_pop'][$geo]['be__2_tx_var_migratoire_an_n_5'] = $this->getTxVarMigratoireAn($dataTemp, $geo, 5);
        $dataTemp['insee_histo_pop'][$geo]['ce__2_tx_var_migratoire_an_n_10'] = $this->getTxVarMigratoireAn($dataTemp, $geo, 10);
        $dataTemp['insee_histo_pop'][$geo]['nc__2_evol_annuel_menage'] = ($dataTemp['insee_histo_pop'][$geo]['ha__menage_n_10'] != 0) ? ($this->getEvol($dataTemp['insee_histo_pop'][$geo]['hc__menage_annee'] / $dataTemp['insee_histo_pop'][$geo]['ha__menage_n_10'], 10) * 100) : '';
        $dataTemp['insee_histo_pop'][$geo]['nb__2_evol_annuel_menage'] = ($dataTemp['insee_histo_pop'][$geo]['hb__menage_n_5'] != 0) ? ($this->getEvol($dataTemp['insee_histo_pop'][$geo]['hc__menage_annee'] / $dataTemp['insee_histo_pop'][$geo]['hb__menage_n_5'], 5) * 100) : '';
        $dataTemp['insee_histo_pop'][$geo]['na__2_evol_annuel_menage'] = ($dataTemp['insee_histo_pop'][$geo]['ha__menage_n_10'] != 0) ? ($this->getEvol($dataTemp['insee_histo_pop'][$geo]['hb__menage_n_5'] / $dataTemp['insee_histo_pop'][$geo]['ha__menage_n_10'], 5, true) * 100) : '';
        unset($dataTemp['insee_histo_pop'][$geo]['solde_temp']);
        unset($dataTemp['insee_histo_pop'][$geo]['solde_temp_n_10']);
        unset($dataTemp['insee_histo_pop'][$geo]['solde_temp_n_5_5']);
        ksort($dataTemp['insee_histo_pop'][$geo], SORT_NATURAL);
        $n = 0;
        foreach ($dataTemp['insee_histo_pop'][$geo] as $key => $value) {
            $n++;
            $indice = ($n < 19) ? 1 : 2;
            $data['insee_histo_pop']['part_' . $indice][$geo][$key] = floatval($value);
        }
        if (empty($this->tRubrique[0]) && $this->ficheType == 'detail') {
            $this->tRubrique[0]['var_croise_lib'] = "Evolution de la population " . $this->anneePopInseeFirst_1 . ' - ' . $this->anneePopInseeFirst . ' - ' . $this->anneePopInsee;
            $this->tRubrique[0]['var_croise_ancre'] = 'evol_pop';
        }
        return $data;
    }

    /**
     * calcul de l'evolution de la population pour l'insee
     * @param type $param
     * @param int $nbAnnee sur combien d'année
     * @param boolean $beforeNow 5 ans auparavant
     * @return float
     */
    private function getEvol($param, $nbAnnee, $beforeNow = false)
    {
        if ($beforeNow === true) {
            if (($this->anneePopInseeFirst - $this->anneePopInseeFirst_1) != 0) {
                return (pow(($param), (1 / ($this->anneePopInseeFirst - $this->anneePopInseeFirst_1)))) - 1;
            } else {
                return false;
            }
        }
        $anneeFirst = ($nbAnnee == 5) ? $this->anneePopInseeFirst : $this->anneePopInseeFirst_1;
        if (($this->anneePopInsee - $anneeFirst) != 0) {
            return (pow(($param), (1 / ($this->anneePopInsee - $anneeFirst)))) - 1;
        } else {
            return false;
        }
    }

    /**
     *
     * @param array $data tableau contenant les données
     * @param string $geo
     * @param int $nbAnnee sur combien d'année
     * @param boolean $beforeNow 5 ans auparavant
     * @return float Taux variation migratoire annuel
     */
    private function getTxVarMigratoireAn($data, $geo, $nbAnnee, $beforeNow = false)
    {
        $popAnneeDepart = ($beforeNow === true) ? 'ab_population_n_5' : 'ac_population_annee';
        if ($beforeNow === true) {
            $anneePopFirst = 'aa_population_n_10';
            $varSoldenat = 'ad_solde_naturel_n_5_5';
            $varEvolPop = 'aca__2_evol_annuel_pop_n_5_5';
            $nAnnee = 5;
        } else {
            $anneePopFirst = ($nbAnnee == 5) ? 'ab_population_n_5' : 'aa_population_n_10';
            $varSoldenat = ($nbAnnee == 5) ? 'bb_solde_naturel_n_5' : 'cb_solde_naturel_n_10';
            $varEvolPop = ($nbAnnee == 5) ? 'baa__2_evol_annuel_pop_n_5' : 'caa__2_evol_annuel_pop_n_10';
            $nAnnee = ($nbAnnee == 5) ? 5 : 10;
        }
        if ($data['insee_histo_pop'][$geo][$popAnneeDepart] == $data['insee_histo_pop'][$geo][$anneePopFirst]) {
            if ($data['insee_histo_pop'][$geo][$popAnneeDepart] == 0) {
                $result = null;
            } else {
                $result = 100 * (($data['insee_histo_pop'][$geo][$popAnneeDepart] - $data['insee_histo_pop'][$geo][$anneePopFirst]) - ($data['insee_histo_pop'][$geo][$varSoldenat])) / ($nAnnee * $data['insee_histo_pop'][$geo][$popAnneeDepart]);
            }
        } else {
            $result = ($data['insee_histo_pop'][$geo][$varEvolPop] * (($data['insee_histo_pop'][$geo][$popAnneeDepart] - $data['insee_histo_pop'][$geo][$anneePopFirst]) - ($data['insee_histo_pop'][$geo][$varSoldenat]))) / ($data['insee_histo_pop'][$geo][$popAnneeDepart] - $data['insee_histo_pop'][$geo][$anneePopFirst]);
        }
        return $result;
    }

    /**
     *
     * @param array $data tableau contenant les données
     * @param string $geo
     * @param int $nbAnnee sur combien d'année
     * @param boolean $beforeNow 5 ans auparavant
     * @return flat Taux variation naturel annuel
     */
    private function getTxVarNaturelAn($data, $geo, $nbAnnee, $beforeNow = false)
    {
        if ($beforeNow === true) {
            $varEvolPop = 'aca__2_evol_annuel_pop_n_5_5';
            $varSoldenat = 'ad_solde_naturel_n_5_5';
            $soldeTemp = 'solde_temp_n_5_5';
        } else {
            $varEvolPop = ($nbAnnee == 5) ? 'baa__2_evol_annuel_pop_n_5' : 'caa__2_evol_annuel_pop_n_10';
            $varSoldenat = ($nbAnnee == 5) ? 'bb_solde_naturel_n_5' : 'cb_solde_naturel_n_10';
            $soldeTemp = ($nbAnnee == 5) ? 'solde_temp' : 'solde_temp_n_10';
        }
        if ($data['insee_histo_pop'][$geo][$soldeTemp] == 0) {
            $result = null;
        } else {
            $result = $data['insee_histo_pop'][$geo][$varEvolPop] * $data['insee_histo_pop'][$geo][$varSoldenat] / $data['insee_histo_pop'][$geo][$soldeTemp];
        }
        return $result;
    }
}
