<?php

namespace App\Models;

use App\Models\FicheModel;

class SneModel extends FicheModel
{

    public $perimetre = null;
    private $andGeo = '';
    private $dataSource = 'data_sne';
    private $geoEtude = null;
    public $data = array();
    public $ficheType = null;
    private $annee = null;

    public function __construct()
    {
        parent::__construct();
        $this->tRubrique = array(0 => array(), 1 => array(), 2 => array(), 3 => array(), 4 => array(), 5 => array(), 6 => array(), 7 => array(), 8 => array(), 9 => array());
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
        $this->annee = $this->perimetre['anneeSne'];
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
        $fiche = ($this->ficheType == 'detail') ? 'sne_detail' : 'sne_synthese';
        $from = " FROM " . $this->dataSource . " WHERE annee = " . $this->annee . $this->andGeo;
        $dataSne = array();
        //Pression de la demande
        $sql0 = "SELECT SUM(attribution) AS 'attribution', SUM(demandeur) AS 'demandeur', (SUM(demandeur) / SUM(attribution)) as pression " . $from;
        $query0 = $this->db->query($sql0);
        $result0 = $query0->getResultArray();
        $dataSne[$fiche][$this->annee][$geo]['Demandeurs'] = $result0[0]['demandeur'];
        $dataSne[$fiche][$this->annee][$geo]['Attributions'] = $result0[0]['attribution'];
        $dataSne[$fiche][$this->annee][$geo]['Pression'] = $result0[0]['pression'];
        return $dataSne;
    }
}
