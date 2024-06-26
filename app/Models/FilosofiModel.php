<?php


namespace App\Models;

use App\Models\FicheModel;

class FilosofiModel extends FicheModel
{
    public $perimetre = null;
    private $andGeo = '';
    private $dataSource = 'data_filosofi';
    public $data = array();
    public $ficheType = null;
    private $annee = null;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     * @return array
     */
    public function process()
    {
        $data = array();
        $this->annee = $this->perimetre['anneeFilosofi'];
        //pour chaque commune etudie
        foreach ($this->perimetre['codeEtude'] as $key => $value) {
            $insee = str_replace(',', "','", $this->perimetre['codeEtude'][$key]);
            $this->andGeo = " AND codgeo IN ('" . $insee . "') ";
            $data = $this->arrayMergeRecursiveMy($data, $this->getData($value));
        }
        //var_dump($data);exit;
        //ce a quoi on compare
        if (isset($this->perimetre['perimComp']) && is_array($this->perimetre['perimComp'])) {
            //pour chaque territoire auquel on eut comparer
            foreach ($this->perimetre['perimComp'] as $value) {
                if ($value == 'secteur' || $value == 'commune' || $value == 'scot') {
                    continue;
                }
                $this->andGeo = $this->getGeoCompFilosofi($value);
                if ($value != 'secteur') {
                    $data = $this->arrayMergeRecursiveMy($data, $this->getData($value));
                }
            }
        }
        return $data;
    }

    private function getGeoCompFilosofi($geo)
    {
        switch ($geo) {
            case 'france':
                $whereComp = ' codgeo=1';
                break;
            case 'region':
                $region = $this->perimetre['region'][0];
                $whereComp = 'geo_level="reg" AND codgeo =' . $region;
                break;
            case 'departement':
                $dpt = $this->perimetre['departement'][0];
                $whereComp = 'geo_level="dep" AND codgeo LIKE "' . $dpt . '"';
                break;
            case 'epci':
                $epci = $this->perimetre['epci'][0];
                $whereComp = 'geo_level="epci" AND codgeo =' . $epci;
                break;
            default:
                $whereComp = null;
        }
        $whereComp = isset($whereComp) ? " AND " . $whereComp : false;
        return $whereComp;
    }

    /**
     * requetes des donnees
     * @param  string $geo
     * @return array
     */
    private function getData($geo)
    {
        $fiche = ($this->ficheType == 'detail') ? 'filosofi_detail' : 'filosofi_synthese';
        $data = array();
        $sql = "SELECT (`med`) AS 'Médiane du niveau de vie (€)', (`pimp`) AS 'Part des ménages fiscaux imposés (%)', (`tp60`) AS 'Taux de pauvreté-Ensemble (%)' FROM " . $this->dataSource . " WHERE annee = " . $this->annee . $this->andGeo;
        $query1 = $this->db->query($sql);
        $result1 = $query1->getResultArray();
        $data[$fiche][$this->annee][$geo] = $result1 ? $result1[0] : array('Médiane du niveau de vie (€)' => 0, 'Part des ménages fiscaux imposés (%)' => 0, 'Taux de pauvreté-Ensemble (%)' => 0);
        if (empty($this->tRubrique[0]) && $this->ficheType == 'detail') {
            $this->tRubrique[0]['var_croise_lib'] = "Niveau de vie " . $this->annee;
            $this->tRubrique[0]['var_croise_ancre'] = 'filosofi';
        }
        return $data;
    }
}
