<?php


namespace App\Models;

use App\Models\FicheModel;

/**
 * Description of LovacModel
 *
 * @author christian
 */
class LovacModel extends FicheModel
{
    public $perimetre = null;
    private $andGeo = 0;
    private $dataSource = 'data_lovac';
    private $geoEtude = null;
    public $data = array();
    public $ficheType = null;
    private $annee = null;
    
    /**
     * requete dans la table et construction du jeu de donnees
     * @param string $where
     * @return array
     */
    public function process()
    {
        $this->geoEtude = $this->perimetre['codeEtude'];
        $this->geoData = ($_SESSION['territoireEtude'] == 'commune') ? $this->perimetre['codeEtude'] : $this->perimetre['codeGeo'];
        reset($this->geoEtude);
        $data = array();
        $this->annee = $this->perimetre['anneeLovac'];
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
        $fiche = ($this->ficheType == 'detail') ? 'lovac_detail' : 'lovac_synthese';
        $sql = "SELECT SUM(`nb_log_pp_nnnn`) as 'Nombre de logements du parc privé', "
                . "SUM(`nb_logvac_pp_0101nn`) as 'Nombre de logements vacants du parc privé', "
                . "SUM(`nb_logvac_pp_c_0101nn`) as 'Nombre de logements du parc privé vacants depuis moins de deux ans', "
                . "SUM(`nb_logvac_2a_0101nn`) as 'Nombre de logements du parc privé vacants depuis deux ans ou plus', "
                . "SUM(`nb_logvac_pp_0101nn`) / SUM(`nb_log_pp_nnnn`)*100 as 'Taux de logements vacants du parc privé', "
                . "SUM(`nb_logvac_pp_c_0101nn`) / SUM(`nb_log_pp_nnnn`)*100 as 'Taux de logements du parc privé vacants depuis moins de deux ans', "
                . "SUM(`nb_logvac_2a_0101nn`) / SUM(`nb_log_pp_nnnn`)*100 as 'Taux de logements du parc privé vacants depuis deux ans ou plus' FROM " . $this->dataSource . " WHERE annee = " . $this->annee . $this->andGeo;
        $data = array();
        $query1 = $this->db->query($sql);
        $result1 = $query1->getResultArray();
        $data[$fiche][$this->annee][$geo] = $result1 ? $result1[0] : array('nb_log_pp_nnnn' => 0, 'nb_logvac_pp_0101nn' => 0, 'nb_logvac_pp_c_0101nn' => 0, 'nb_logvac_2a_0101nn' => 0, 'prop_logvac_pp_0101nn' => 0, 'prop_logvac_pp_c_0101nn' => 0, 'prop_logvac_pp_2a_0101nn' => 0);
        if (empty($this->tRubrique[0]) && $this->ficheType == 'detail') {
            $this->tRubrique[0]['var_croise_lib'] = "LOVAC au 1/1/" . $this->annee;
            $this->tRubrique[0]['var_croise_ancre'] = 'LOVAC';
        }
        return $data;
        
    }
}
