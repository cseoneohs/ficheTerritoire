<?php


namespace App\Models;

use App\Models\FicheModel;

ini_set('max_execution_time', MAX_EXECUTION_TIME);
ini_set('memory_limit', MEMORY_LIMIT);

class InseeLogemtModel extends FicheModel
{
    public $perimetre = null;
    //clause where supplementaire de la requete
    private $where = '';
    private $andGeo = '';
    private $dataSource = 'fd_logemt';
    //private $dataSourceTest = 'fd_logemt69';

    /**
     * tableau contenant le com de chaque variables
     * @var array
     */
    private $colToDisplay = array();

    /**
     * tableau contenant toutes les inforamtions pour chaques variables et leurs modalités
     * @var array
     */
    private $varToDisplay = array();

    /**
     * les variables concernant l'occupation (informations concernant les ménages)
     * @var array
     */
    private $tVarOccupation = array('AGEMEN8', 'ANEMR', 'DIPLM', 'ILTM', 'INPER', 'RECHM', 'TACTM', 'TPM', 'TRANSM', 'VOIT', 'INP75M');

    /**
     * le nom de la table contenant les variables, leur libellé et leurs modalités
     * @var string
     */
    private $tableVar = '';
    private $geoEtude = null;
    private $geoData = null;
    public $data = array();
    public $ficheType = null;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * creation de table mysql temporaire pour limiter le jeu de donnees a requeter
     * une table contenant les communes observees
     * une table par terrtoire de comparaison
     */
    private function setTemptable()
    {
        if (isset($this->perimetre['perimComp']) && is_array($this->perimetre['perimComp'])) {
            foreach ($this->perimetre['perimComp'] as $value) {
                $this->createTemptable($value, 'tempTableComp');
            }
        }
        $this->geoEtude = $this->perimetre['codeEtude'];
        $this->geoData = ($_SESSION['territoireEtude'] == 'commune') ? $this->perimetre['codeEtude'] : $this->perimetre['codeGeo'];
        reset($this->geoEtude);
        $this->createTemptable($this->geoEtude, 'tempTable');
    }

    /**
     * requete dans les tables temporaires et construction du jeu de donnees
     * @param string $where
     * @return array
     */
    public function process($where = null)
    {
        $this->tableVar = 'ts_var_fd_logemnt_' . $this->ficheType;
        //ce que l'on observe
        $this->colToDisplay = $this->getCol();
        $this->varToDisplay = $this->getVar();
        $this->setTemptable();

        if (!is_null($where) && ($this->ficheType == 'detail')) {
            $this->where = $this->getWhere($where);
        }
        $data = array();

        //pour chaque commune etudie
        foreach ($this->geoEtude as $key => $value) {
            $insee = str_replace(',', "','", $this->geoData[$key]);
            $this->andGeo = " AND commune IN ('" . $insee . "') ";
            $data = $this->arrayMergeRecursiveMy($data, $this->getData($value, 'tempTable'));
        }
        //ce a quoi on compare

        if (isset($this->perimetre['perimComp']) && is_array($this->perimetre['perimComp'])) {
            $this->andGeo = '';
            //pour chaque territoire auquel on eut comparer
            foreach ($this->perimetre['perimComp'] as $value) {
                //$tempTableComp = iconv("UTF-8", 'ASCII//TRANSLIT//IGNORE', $value) . '_tempTableComp';
                $tempTableComp = $value . '_tempTableComp';
                $tData = $this->getData($value, $tempTableComp);
                $data = array_merge_recursive($data, $tData);
            }
        }

        return $data;
    }

    /**
     * creation des tables temporaires pour ne pas requeter sur toute la BDD
     * @param string $geo
     * @param string $temptable
     */
    public function createTemptable($geo, $temptable)
    {
        $col = implode(',', $this->colToDisplay);
        $col .= ',ipondl, commune';
        if ($this->ficheType == 'synthese') {
            $col .= ', catl';
        }
        if ($temptable == 'tempTable') {
            if (is_array($geo) && $this->perimetre['geo'] == 'commune') {
                $code = array();
                foreach ($geo as $value) {
                    $code[] = '"' . $value . '"';
                }
                $codeEtude = implode(',', $code);
                $where = "commune IN(" . $codeEtude . ")";
            } elseif (is_array($geo) && $this->perimetre['geo'] == 'epci') {
                $code = "'";
                reset($this->perimetre['codeGeo']);
                $start = key($this->perimetre['codeGeo']);
                foreach ($this->perimetre['codeGeo'] as $key => $value) {
                    if ($start != $key) {
                        $code .= "','";
                        $start = $key;
                    }
                    $code .= str_replace(",", "','", $value);
                }
                $codeEtude = $code . "'";
                $where = "commune IN(" . $codeEtude . ")";
            } else {
                $where = "commune LIKE'" . $geo . "'";
            }
            $sql = 'CREATE TEMPORARY TABLE IF NOT EXISTS tempTable (INDEX COMMUNE (commune)) SELECT ' . $col . ' FROM ' . $this->dataSource . '_' . $this->perimetre['annee'] . ' WHERE ' . $where;
        } else {
            switch ($geo) {
                case 'commune':
                    if (count($this->perimetre["code"]) == 1) {
                        $where = "commune LIKE'" . $this->perimetre["code"][0] . "'";
                    } else {
                        $code = array();
                        foreach ($this->perimetre["code"] as $value) {
                            $code[] = '"' . $value . '"';
                        }
                        $codeEtude = implode(',', $code);
                        $where = "commune IN (" . $codeEtude . ")";
                    }
                    $sql = 'CREATE TEMPORARY TABLE IF NOT EXISTS commune_tempTableComp SELECT ' . $col . ' FROM ' . $this->dataSource . '_' . $this->perimetre['annee'] . ' WHERE ' . $where;
                    break;
                case 'france':
                    $whereComp = ' 1';
                    $sql = 'CREATE TEMPORARY TABLE IF NOT EXISTS france_tempTableComp SELECT ' . $col . ' FROM ' . $this->dataSource . '_' . $this->perimetre['annee'] . ' WHERE ' . $whereComp;
                    break;
                case 'region':
                    $region = $this->perimetre['region'][0];
                    $whereComp = 'commune IN (SELECT codegeo FROM ts_geo_commune WHERE commune_reg =' . $region . ')';
                    $sql = 'CREATE TEMPORARY TABLE IF NOT EXISTS region_tempTableComp SELECT ' . $col . ' FROM ' . $this->dataSource . '_' . $this->perimetre['annee'] . ' WHERE ' . $whereComp;
                    break;
                case strstr($geo,'departement'):
                    $dept = ltrim(strstr($geo,'departement'), 'departement');
                    $whereComp = 'commune IN (SELECT codegeo FROM ts_geo_commune WHERE commune_dep = ' . $dept . ')';
                    $sql = 'CREATE TEMPORARY TABLE IF NOT EXISTS ' .$geo.'_tempTableComp SELECT ' . $col . ' FROM ' . $this->dataSource . '_' . $this->perimetre['annee'] . ' WHERE ' . $whereComp;
                    break;
                case 'epci':
                    $epci = $this->perimetre['epci'][0];
                    $sqlCommune = "SELECT codegeo FROM ts_geo_commune WHERE commune_epci =" . $epci;
                    $query = $this->db->query($sqlCommune);
                    $result = $query->getResultArray();
                    $val = '';
                    foreach ($result as $key => $value) {
                        $val .= $value['codegeo'] . ',';
                    }
                    $params = rtrim($val, ',');
                    $whereComp = 'commune IN (' . $params . ')';
                    $sql = 'CREATE TEMPORARY TABLE IF NOT EXISTS epci_tempTableComp SELECT ' . $col . ' FROM ' . $this->dataSource . '_' . $this->perimetre['annee'] . ' WHERE ' . $whereComp;
                    break;
                case 'scot':
                    $code = $this->perimetre['scot'][$this->perimetre['codeEtude'][0]]['codegeo'];
                    $whereComp = 'commune IN (' . $code . ')';
                    $sql = 'CREATE TEMPORARY TABLE IF NOT EXISTS scot_tempTableComp SELECT ' . $col . ' FROM ' . $this->dataSource . '_' . $this->perimetre['annee'] . ' WHERE ' . $whereComp;
                    break;

                default:
                    if ($secteur = strstr($geo, '_secteur', true)) {
                        $sql0 = 'SELECT codegeo FROM ts_geo_secteur WHERE libel LIKE "' . $secteur . '"';
                        $stmt0 = $this->dbh->query($sql0);
                        $result0 = $stmt0->fetchAll(PDO::FETCH_ASSOC);
                        $stmt0 = null;
                        if (strpos($result0[0]['codegeo'], ',')) {
                            $listCommunes = str_replace(',', "','", $result0[0]['codegeo']);
                            $listCommunes = "'" . $listCommunes . "'";
                        } else {
                            $listCommunes = $result0[0]['codegeo'];
                        }
                        //$whereComp = 'commune IN (SELECT codegeo FROM ts_geo_secteur WHERE libel LIKE "'.$result0.'")';
                        //$code = $this->perimetre['secteur'][$this->perimetre['code']];
                        $whereComp = 'commune IN (' . $listCommunes . ')';
                        $sql = 'CREATE TEMPORARY TABLE IF NOT EXISTS secteur_tempTableComp SELECT ' . $col . ' FROM ' . $this->dataSource . '_' . $this->perimetre['annee'] . ' WHERE ' . $whereComp;
                    }
                    break;
            }
        }
        $query = $this->db->query($sql);
    }

    /**
     * suppression des tables temporaires
     */
    public function dropTemptable($temptable)
    {
        $sql = "DROP TEMPORARY TABLE IF EXISTS " . $temptable;
        $query = $this->db->query($sql);
    }

    /**
     * recherche des noms des variables
     * @return array
     */
    private function getCol()
    {
        $sql = 'SELECT DISTINCT var_code FROM ' . $this->tableVar . ' WHERE 1';
        $query = $this->db->query($sql);
        $result = $query->getResultArray();

        $val = null;
        foreach ($result as $value) {
            $val[] = $value['var_code'];
        }

        return($val);
    }

    /**
     * recherche de toutes les informations concenrant les variabels
     * @return array
     */
    private function getVar()
    {
        $sqlLib = 'SELECT * FROM ' . $this->tableVar . ' WHERE 1';
        $query = $this->db->query($sqlLib);
        $result = $query->getResultArray();
        return($result);
    }

    /**
     * permet de renseigner le complement de la clause WHERE de la methode getData
     * @param  string $myWhere
     * @return string
     */
    private function getWhere($myWhere)
    {
        if ($this->ficheType == 'detail') {
            $where = is_null($myWhere['var_code']) ? '1 = 1' : (strpos($myWhere['var_croise_mod_' . $this->ficheType], ',') ? $myWhere['var_code'] . ' IN (' . $myWhere['var_croise_mod_' . $this->ficheType] . ')' : $myWhere['var_code'] . '=' . $myWhere['var_croise_mod_' . $this->ficheType]);
            return $where;
        } else {
            $sql = 'SELECT var_contrainte FROM ts_var_fd_logemnt_synthese WHERE var_code LIKE "' . $myWhere . '"';
            $query = $this->db->query($sql);
            $result = $query->getResultArray();
            return $result[0]['var_contrainte'];
        }
    }

    /**
     * requetes des donnees
     * @param  string $geo
     * @param  string $tempTable
     * @return array
     */
    private function getData($geo, $tempTable)
    {
        //printf(" table temporaire : %f", xdebug_time_index());
        $data = array();
        foreach ($this->colToDisplay as $value) {
            $todo = true;
            if ($this->ficheType == 'synthese') {
                $this->where = $this->getWhere($value);
            } elseif ($this->ficheType == 'detail') {
                if (($this->where == '1 = 1' || $this->where == 'CATL=4') && in_array($value, $this->tVarOccupation)) {
                    $todo = null;
                }
                if ($this->where != '1 = 1' && $value == 'CATL') {
                    $todo = null;
                }
            }
            if (!isset($todo)) {
                continue;
            }
            foreach ($this->varToDisplay as $val) {
                if (($val['var_code']) !== $value) {
                    continue;
                }
                $sql = 'SELECT SUM(ipondl) as nbMenage FROM ' . $tempTable . ' WHERE ' . $value . ' IN(' . $val["var_modalite_code"] . ') AND ' . $this->where . $this->andGeo;
                $query = $this->db->query($sql);
                $result = $query->getResultArray();
                $data[$val['var_lib']][$val['var_modalite_lib']][$geo]['nb'] = !is_null($result[0]['nbMenage']) ? $result[0]['nbMenage'] : 0;
            }
        }
        $dataTot = $this->calculePercent($data);
        return $dataTot;
    }

    /**
     * requete sur la table ts_var_croise_fd_logement
     * @return array
     */
    public function getSousRubrique()
    {
        if (is_array($this->perimetre['varFdLogement']) && !empty($this->perimetre['varFdLogement'])) {
            $varId = implode(",", $this->perimetre['varFdLogement']);
            $sql = "SELECT * FROM ts_var_croise_fd_logement WHERE var_id IN(" . $varId . ") ORDER BY var_croise_ordre ASC";
        } else {
            $sql = "SELECT * FROM ts_var_croise_fd_logement ORDER BY var_croise_ordre ASC";
        }
        $query = $this->db->query($sql);
        $result = $query->getResultArray();
        return($result);
    }

    /**
     * rajoute une colonne de pourcentage a un tableau de valeurs
     * @param array $data ; le tableau de donnees dont on cherche l'expression en pourcent
     * @return array
     */
    private function calculePercent($data)
    {
        foreach ($data as $k1 => $var) {
            $tot = 0;
            foreach ($var as $k2 => $col) {
                foreach ($col as $k3 => $geo) {
                    $tot += $geo['nb'];
                }
            }
            foreach ($var as $k2 => $col) {
                foreach ($col as $k3 => $geo) {
                    $data[$k1][$k2][$k3]['pc'] = ($tot == 0) ? 0 : (100 * $geo['nb'] / $tot);
                }
            }
        }
        return($data);
    }
}
