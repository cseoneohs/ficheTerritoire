<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Query;

class PerimetreModel extends Model
{

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * recherche les annees renseignees dans la BDD pour FD_LOGEMT
     * @return array
     */
    public function selectAnneeInsee()
    {
        $sql = "SELECT * FROM ts_annee ORDER BY annee DESC";
        $query = $this->db->query($sql);
        $result = $query->getResultArray();
        return($result);
    }

    /**
     * recherche les annees renseignees dans la BDD pour l'artificialisation des sols
     * @return array
     */
    public function selectAnneeArtificialisation()
    {
        $sql = "SELECT * FROM ts_annee_artificialisation ORDER BY annee DESC LIMIT 1";
        $query = $this->db->query($sql);
        $result = $query->getResultArray();
        return($result);
    }

    /**
     * recherche de la liste des variables que l'on affiche pour la fiche detail avec fd_logement
     * @return array
     */
    public function selectVarFdLogement()
    {
        $sql = "SELECT * FROM ts_var_croise_fd_logement ORDER BY var_croise_ordre ASC";
        $query = $this->db->query($sql);
        $result = $query->getResultArray();
        return($result);
    }

    /**
     * recherche les annees renseignees dans la table $table
     * @return array
     */
    public function selectAnneeData($table)
    {
        $sql = "SELECT DISTINCT(annee) FROM " . $table . " WHERE 1 ORDER BY annee DESC";
        $query = $this->db->query($sql);
        $result = $query->getResultArray();
        return($result);
    }

    /**
     * ordre des différents niveaux géographiques
     * @return array
     */
    public function selectOrdre()
    {
        $sql = 'SELECT * FROM ts_geo_ordre ORDER BY ordre';
        $query = $this->db->query($sql);
        $result = $query->getResultArray();
        return($result);
    }

    /**
     * recherche des régions
     * @return array
     */
    public function selectRegion($codeRegion = null)
    {
        if (is_null($codeRegion)) {
            $where = " WHERE 1";
        } else {
            $cRegion = array_unique($codeRegion);
            $code = array();
            foreach ($cRegion as $value) {
                $code[] = '"' . $value . '"';
            }
            $region = implode(',', $code);
            $where = ' WHERE code_region IN (' . $region . ')';
        }
        $sql = "SELECT code_region, lib_region FROM ts_geo_region " . $where;
        $query = $this->db->query($sql);
        $result = $query->getResultArray();
        foreach ($result as $values) {
            $reg[$values['code_region']] = $values['lib_region'];
        }
        return $reg;
    }
    
    /**
     * recherche des départements
     * @return array
     */
    public function selectDept($codedept = null)
    {
        if (is_null($codedept)) {
            $where = " WHERE 1";
        } else {
            $cDept = array_unique($codedept);
            $code = array();
            foreach ($cDept as $value) {
                $code[] = '"' . $value . '"';
            }
            $dept = implode(',', $code);
            $where = ' WHERE code_departement IN (' . $dept . ')';
        }
        $sql = "SELECT code_departement, lib_departement FROM ts_geo_departement " . $where;
        $query = $this->db->query($sql);
        $result = $query->getResultArray();
        foreach ($result as $values) {
            $dpt[$values['code_departement']] = $values['lib_departement'];
        }
        return $dpt;
    }

    /**
     * recherche des scot
     * @param string $codegeo code commune pour laquelle on recherche le secteur d'appartenance
     * @return array
     */
    public function selectScot($codegeo = null, $codedept = null)
    {
        if (!is_null($codegeo)) {
            $where = ' codegeo LIKE "%' . $codegeo . '%"';
        } elseif (!is_null($codedept)) {
            $code = array();
            foreach ($codedept as $value) {
                $code[] = '"' . $value . '"';
            }
            $dept = implode(',', $code);
            $where = " entiteSup IN (" . $dept . ")";
        } else {
            $where = ' 1 = 1';
        }
        $sql = "SELECT * FROM ts_geo_scot WHERE " . $where;
        $query = $this->db->query($sql);
        $result = $query->getResultArray();
        if (count($result) == 1 && !is_null($codegeo)) {
            return($result[0]);
        } else {
            return($result);
        }
    }

    /**
     * recherche des epci
     * @return array
     */
    public function selectEpci($codeEpci = null, $codedept = null)
    {
        if (is_null($codeEpci) && is_null($codedept)) {
            $sql = "SELECT code_epci, lib_epci FROM ts_geo_epci";
        } elseif (!is_null($codedept)) {
            $code = array();
            foreach ($codedept as $value) {
                $code[] = '"' . $value . '"';
            }
            $dept = implode(',', $code);
            $sql = 'SELECT ts_geo_epci.* FROM ts_geo_epci INNER JOIN ts_geo_commune ON ts_geo_commune.commune_epci= ts_geo_epci.code_epci WHERE ts_geo_commune.commune_dep IN (' . $dept . ') GROUP BY ts_geo_epci.code_epci';
        } else {
            $code = array();
            foreach ($codeEpci as $value) {
                $code[] = '"' . $value . '"';
            }
            $epci = implode(',', $code);
            $sql = 'SELECT lib_epci FROM ts_geo_epci WHERE code_epci IN (' . $epci . ')';
        }
        $query = $this->db->query($sql);
        $result = $query->getResultArray();
        if (is_null($codeEpci)) {
            return($result);
        } else {
            $lib = '';
            foreach ($result as $value) {
                $lib .= $value['lib_epci'] . ', ';
            }
            $libepci = rtrim($lib, ", ");
            return $libepci;
        }
    }

    /**
     * recherche des secteurs
     * @param string $codegeo code commune pour laquelle on recherche le secteur d'appartenance
     * @return array
     */
    public function selectSect($codegeo = null, $codeEpci = null)
    {
        //d($codegeo);
        if (!is_null($codegeo)) {
            $code = array();
                foreach ($codegeo as $value) {                    
                    $code[] = '"' . $value . '"';
                }
                $secteurs = implode(',', $code);           
            $where = ' codegeo IN (' .$secteurs. ')';
        } elseif (!is_null($codeEpci)) {
            $code = array();
            foreach ($codeEpci['code'] as $value) {
                $code[] = '"' . $value . '"';
            }
            $epci = implode(',', $code);
            $where = ' entiteSup IN (' . $epci . ')';
        } else {
            $where = ' 1 = 1';
        }
        $sql = "SELECT * FROM ts_geo_secteur WHERE " . $where;
        $query = $this->db->query($sql);
        $result = $query->getResultArray();
        if (count($result) == 1 && !is_null($codegeo)) {
            return($result[0]);
        } else {
            return($result);
        }
    }

    /**
     * recherche des communes
     * @param array $perimetre
     * @return array
     */
    public function selectComm($perimetre = null)
    {
        switch ($perimetre['geo']) {
            case 'commune':
                $code = array();
                foreach ($perimetre['code'] as $value) {
                    $code[] = '"' . $value . '"';
                }
                $communes = implode(',', $code);
                $where = ' WHERE codegeo IN (' . $communes . ')';
                break;
            case 'secteur':
                $code = array();
                foreach ($perimetre['code'] as $value) {
                    $vals = str_replace(',', '","', $value);
                    $code[] = '"' . $vals . '"';
                }
                $secteurs = implode(',', $code);
                $where = ' WHERE codegeo IN (' . $secteurs . ')';
                break;
            case 'epci':
                $code = array();
                foreach ($perimetre['code'] as $value) {
                    $code[] = '"' . $value . '"';
                }
                $epci = implode(',', $code);
                $where = ' WHERE commune_epci IN (' . $epci . ')';
                break;
            case 'scot':
                $where = ' WHERE codegeo IN (' . $perimetre['code'] . ')';
                break;
            case 'dept':
                $code = array();
                foreach ($perimetre['code'] as $value) {
                    $code[] = '"' . $value . '"';
                }
                $dept = implode(',', $code);
                $where = ' WHERE commune_dep IN (' . $dept . ')';
                break;
            default:
                $where = '';
                break;
        }
        $sql = 'SELECT * FROM ts_geo_commune' . $where;
        $query = $this->db->query($sql);
        $result = $query->getResultArray();
        return($result);
    }

    /**
     * le périmètre d'étude est l'epci, recherche des communes des epci
     * @param array $perimetre
     * @return array
     */
    public function selectEpciEtude($perimetre = null)
    {
        switch ($perimetre['geo']) {
            case 'commune':
                $code = array();
                foreach ($perimetre['code'] as $value) {
                    $code[] = '"' . $value . '"';
                }
                $communes = implode(',', $code);
                $where = ' WHERE codegeo IN (' . $communes . ')';
                $table = 'ts_geo_commune';
                break;
            case 'secteur':
                $where = ' WHERE codegeo IN (' . $perimetre['code'] . ')';
                $table = 'ts_geo_commune';
                break;
            case 'epci':
                $code = array();
                foreach ($perimetre['code'] as $value) {
                    $code[] = '"' . $value . '"';
                }
                $epci = implode(',', $code);
                $where = ' WHERE code_epci IN (' . $epci . ')';
                $table = 'ts_geo_epci';
                break;
            case 'scot':
                $where = ' WHERE codegeo IN (' . $perimetre['code'] . ')';
                break;
            case 'dept':
                $dept = implode(",", $perimetre['code']);
                $where = ' WHERE commune_dep IN (' . $dept . ')';
                break;
            default:
                $where = '';
                break;
        }
        $sql = 'SELECT * FROM ' . $table . $where;
        $query = $this->db->query($sql);
        $result = $query->getResultArray();
        //on recherche les code régions et département de l'epci
        $pQuery1 = $this->db->prepare(function ($db) {
            $sql1 = 'SELECT * FROM ts_geo_commune WHERE commune_epci = ? LIMIT 1';
            return (new Query($db))->setQuery($sql1);
        });
        foreach ($result as $key => $value) {
            $communeEpci = $value['code_epci'];
            $ret = $pQuery1->execute($communeEpci);
            $commune = $ret->getResultArray();
            $result[$key]['commune_reg'] = $commune[0]['commune_reg'];
            $result[$key]['commune_dep'] = $commune[0]['commune_dep'];
        }
        //on recherche les codes insee des communes de l'epci
        $pQuery2 = $this->db->prepare(function ($db) {
            $sql2 = 'SELECT codegeo FROM ts_geo_commune WHERE commune_epci = ?';
            return (new Query($db))->setQuery($sql2);
        });
        foreach ($result as $key => $value) {
            $communeEpci = $value['code_epci'];
            $ret2 = $pQuery2->execute($communeEpci);
            $tcommunes = $ret2->getResultArray();
            $result[$key]['commune_code_geo'] = '';
            foreach ($tcommunes as $val) {
                $result[$key]['commune_code_geo'] .= $val['codegeo'] . ',';
            }
            $result[$key]['commune_code_geo'] = rtrim($result[$key]['commune_code_geo'], ',');
        }
        return($result);
    }

    public function selectIris($commune = null)
    {
        if (is_array($commune) && !empty($commune)) {
            $communes = implode(',', $commune);
            $sql = "SELECT iris, libiris FROM ts_geo_iris WHERE com LIKE'" . $communes . "'";
            $query = $this->db->query($sql);
            $result = $query->getResultArray();
            return($result);
        } else {
            return array();
        }
    }

    /**
     *
     * @return array la liste des codes insee deja importe dans la table secteur
     */
    public function selectExist()
    {
        $sql = "SELECT * FROM ts_geo_secteur";
        $query = $this->db->query($sql);
        $tListGeo = $query->getResultArray();
        return($tListGeo);
    }

    /**
     *
     * @param array $data
     */
    public function insertData($table, $data)
    {
        $bindData = array($data[0], $data[1], $data[2], $data[3]);
        //$query = $this->db->query("INSERT INTO " . $table . " (code, libel, codegeo, entiteSup) VALUES (?, ?, ?, ?)", $bindData, true);
        if (!$this->db->query("INSERT INTO " . $table . " (code, libel, codegeo, entiteSup) VALUES (?, ?, ?, ?)", $bindData, true)) {
            $error = $this->db->error(); // Has keys 'code' and 'message'
            return '<span class="text-danger font-weight-bold">Erreur : ' . $error['message'] . '</span>';
        } else {
            return $data[1] . ' : ' . $data[2] . ' --> importés';
        }
    }
}
