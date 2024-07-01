<?php

namespace App\Models;

/**
 * Description of Fiche
 *
 * @author christian
 *
 */
use CodeIgniter\Model;

/**
 * Model de base contenant des méthode utilisées par les autres class Model
 */
class FicheModel extends Model
{

    public $perimetre = null;
    public $ficheType = null;
    protected $tRubrique = array();

    public function __construct()
    {
        $this->db = \Config\Database::connect();

        /**
         * DEBUG ONLY
         */
        if ((defined('ENVIRONMENT') && ENVIRONMENT == 'development') || defined('DEBUG')) {
            //$this->dataSource = $this->dataSourceTest;
        }

        $this->setPerimetre();
        //var_dump($this->perimetre);exit;
    }

    /**
     * Initialise le perimetre
     */
    private function setPerimetre()
    {
        if (!isset($_SESSION['perimetre'])) {
            return;
        }
        $this->ficheType = isset($_SESSION['fiche']) ? $_SESSION['fiche'] : null;
        $this->perimetre = isset($_SESSION['perimetre']) ? $_SESSION['perimetre'] : null;
        $tperimComp = $_SESSION['perimetreComp'] ? $_SESSION['perimetreComp'] : null;
        if (is_null($tperimComp)) {
            return;
        }
        foreach ($tperimComp as $value) {
            $this->perimetre['perimComp'][] = $value;
        }
    }

    /**
     * version de array_merge_recursive conservant les cles des tableaux
     * @return array
     */
    protected function arrayMergeRecursiveMy()
    {
        $arrays = func_get_args();
        $base = array_shift($arrays);

        foreach ($arrays as $array) {
            reset($base); //important
            foreach ($array as $key => $value) {
                if (is_array($value) && @is_array($base[$key])) {
                    $base[$key] = $this->arrayMergeRecursiveMy($base[$key], $value);
                } else {
                    $base[$key] = $value;
                }
            }
        }

        return $base;
    }

    protected function getGeoComp($geo)
    {
        switch ($geo) {
            case 'commune':
                if (count($this->perimetre["code"]) == 1) {
                    $whereComp = "code_insee LIKE'" . $this->perimetre["code"][0] . "'";
                } else {
                    $code = array();
                    foreach ($this->perimetre["code"] as $value) {
                        $code[] = '"' . $value . '"';
                    }
                    $codeEtude = implode(',', $code);
                    $whereComp = "code_insee IN (" . $codeEtude . ")";
                }
                break;
            case 'france':
                $whereComp = ' 1';
                break;
            case strstr($geo, 'region'):                
                $region = ltrim(strstr($geo, 'region'), 'region');
                $whereComp = 'code_insee IN (SELECT codegeo FROM ts_geo_commune WHERE commune_reg =' . $region . ')';
                break;
            case strstr($geo, 'departement'):
                $dept = ltrim(strstr($geo, 'departement'), 'departement');
                $whereComp = 'code_insee IN (SELECT codegeo FROM ts_geo_commune WHERE commune_dep = "' . $dept . '")';
                break;
            case 'epci':
                $epci = array_unique($this->perimetre['epci']);
                $listEpci = "'";
                $listEpci .= implode("','", $epci);
                $listEpci .= "'";
                $whereComp = 'code_insee IN (SELECT codegeo FROM ts_geo_commune WHERE commune_epci IN (' . $listEpci . '))';
                break;
            case 'scot':
                $code = $this->perimetre['scot'][$this->perimetre['codeEtude'][0]]['codegeo'];
                $whereComp = 'code_insee IN (' . $code . ')';
                break;
            case 'secteur':
                $secteur = $this->perimetre['secteur'][key($this->perimetre['secteur'])]['libel'];
                $sql0 = 'SELECT codegeo FROM ts_geo_secteur WHERE libel LIKE "' . $secteur . '"';
                $query = $this->db->query($sql0);
                $result0 = $query->getResultArray();
                if (strpos($result0[0]['codegeo'], ',')) {
                    $listCommunes = str_replace(',', "','", $result0[0]['codegeo']);
                    $listCommunes = "'" . $listCommunes . "'";
                } else {
                    $listCommunes = $result0[0]['codegeo'];
                }
                $whereComp = 'code_insee IN (' . $listCommunes . ')';
                break;
            default :
                break;
        }
        $whereComp = " AND " . $whereComp;
        return $whereComp;
    }

    public function getSousRubrique()
    {
        return $this->tRubrique;
    }

    public function getVarLibel($param)
    {
        $sql = "SELECT variable, libelle FROM ts_var_libelle WHERE source LIKE '" . $param . "'";
        $query = $this->db->query($sql);
        $result = array();
        foreach ($query->getResultArray() as $row) {
            if (array_key_exists($row['libelle'], $result)) {
                $result[$row['libelle'] . '__'] = $row['variable'];
            } else {
                $result[$row['libelle']] = $row['variable'];
            }
        }
        return $result;
    }
}
