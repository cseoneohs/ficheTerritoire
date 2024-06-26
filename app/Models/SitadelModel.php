<?php

namespace App\Models;

use App\Models\FicheModel;
use CodeIgniter\Database\Query;

class SitadelModel extends FicheModel
{

    public $perimetre = null;
    protected static $andGeo;
    private $dataSource;
    private $libRub = null;

    /**
     * le nombre d'année affiché
     * @var int
     */
    private $nbAnnee = 12;

    /**
     * le nombre d'année pris en compte pour la synthèse
     * @var int
     */
    private $nbAnneeSynthese = 6;
    private $anneePopInsee = null;

    /**
     * tableau contenant les annees a requeter
     * @var array
     */
    private $tAnnee = array();

    /**
     * liste des annees a requeter
     * @var string
     */
    private $listAnnee = '';

    /**
     * un tableau contenant les codes INSEE à étudier
     * @var array
     */
    private $geoEtude = [];
    private $geoData;
    private $dataVar;
    private static $select;

    /**
     * requête dans les tables temporaires et construction du jeu de données
     * @param string $where
     * @return array
     */
    public function process($contexte)
    {
        $this->tRubrique = array();
        $this->dataSource = 'data_sitadel';
        $this->dataVar = ($contexte == 'sitadel_commence') ? 'log_com' : 'log_aut';
        $this->libRub = ($contexte == 'sitadel_commence') ? 'commencés' : 'autorisés';
        $this->tAnnee = $this->getAnnee($contexte);
        $this->anneePopInsee = $this->tAnnee[0]['annee'] - $this->nbAnneeSynthese + 1;
        $annees = array();
        $anneeLimit = 0;
        foreach ($this->tAnnee as $value) {
            $annees[] = '"' . $value['annee'] . '"';
            $anneeLimit++;
            if ($anneeLimit === $this->nbAnneeSynthese) {
                break;
            }
        }
        $this->listAnnee = implode(',', $annees);
        $this->geoEtude = $this->perimetre['codeEtude'];
        $this->geoData = ($_SESSION['territoireEtude'] == 'commune') ? $this->perimetre['codeEtude'] : $this->perimetre['codeGeo'];
        reset($this->geoEtude);
        $data = array();
        //pour chaque commune etudie
        foreach ($this->geoEtude as $key => $value) {
            $insee = str_replace(',', "','", $this->geoData[$key]);
            self::$andGeo = " AND code_insee IN ('" . $insee . "') ";
            $data = $this->arrayMergeRecursiveMy($data, $this->getData($value));
        }
        //ce a quoi on compare
        if (isset($this->perimetre['perimComp']) && is_array($this->perimetre['perimComp'])) {
            //pour chaque territoire auquel on eut comparer
            foreach ($this->perimetre['perimComp'] as $value) {
                self::$andGeo = $this->getGeoComp($value);
                $data = $this->arrayMergeRecursiveMy($data, $this->getData($value));
            }
        }
        return $data;
    }

    private function getData($geo)
    {
        $data = array();
        self::$select = "SELECT
    SUM(CASE WHEN type_lgt LIKE 'Individuel pur' THEN " . $this->dataVar . " END) AS 'individuels purs',
    SUM(CASE WHEN type_lgt LIKE 'Individuel groupé' THEN " . $this->dataVar . " END) AS 'individuels groupés',
    SUM(CASE WHEN (type_lgt LIKE 'Individuel pur' OR type_lgt LIKE 'Individuel groupé') THEN " . $this->dataVar . " END) AS 'individuels (nb)',
    SUM(CASE WHEN (type_lgt LIKE 'Individuel pur' OR type_lgt LIKE 'Individuel groupé') THEN " . $this->dataVar . " END) * 100 / SUM(CASE WHEN (type_lgt LIKE 'Individuel pur' OR type_lgt LIKE 'Individuel groupé' OR type_lgt LIKE 'Collectif') THEN " . $this->dataVar . " END) AS 'individuels (%)',
     SUM(CASE WHEN type_lgt LIKE 'Collectif' THEN " . $this->dataVar . " END) AS 'collectifs (nb)',
     SUM(CASE WHEN type_lgt LIKE 'Collectif' THEN " . $this->dataVar . " END) *100 / SUM(CASE WHEN (type_lgt LIKE 'Individuel pur' OR type_lgt LIKE 'Individuel groupé' OR type_lgt LIKE 'Collectif') THEN " . $this->dataVar . " END) AS 'collectifs (%)',
     SUM(CASE WHEN (type_lgt LIKE 'Individuel pur' OR type_lgt LIKE 'Individuel groupé' OR type_lgt LIKE 'Collectif') THEN " . $this->dataVar . " END) AS 'Total ordinaires',
     SUM(CASE WHEN type_lgt LIKE 'Résidence' THEN " . $this->dataVar . " END) AS 'résidence',
     SUM(CASE WHEN (type_lgt LIKE 'Individuel pur' OR type_lgt LIKE 'Individuel groupé' OR type_lgt LIKE 'Collectif' OR type_lgt LIKE 'Résidence') THEN " . $this->dataVar . " END) as 'Nb total'";
        $sql0 = self::$select . ",
                SUM(CASE WHEN (type_lgt LIKE 'Individuel pur' OR type_lgt LIKE 'Individuel groupé' OR type_lgt LIKE 'Collectif') THEN " . $this->dataVar . " END) / " . $this->nbAnneeSynthese . " as 'par an',
                (SELECT SUM(p_annee_pop) FROM data_cc_serie_histo_insee WHERE annee = " . $this->anneePopInsee . self::$andGeo . ") as 'Pop " . $this->anneePopInsee . "',
                (SUM(CASE WHEN (type_lgt LIKE 'Individuel pur' OR type_lgt LIKE 'Individuel groupé' OR type_lgt LIKE 'Collectif') THEN " . $this->dataVar . " END) / " . $this->nbAnneeSynthese . ")/(SELECT SUM(p_annee_pop) FROM data_cc_serie_histo_insee WHERE annee = " . $this->anneePopInsee . self::$andGeo . ")*1000 as 'Indice de construction'
                FROM " . $this->dataSource;
        $sql0 .= " WHERE annee IN(" . $this->listAnnee . ")" . self::$andGeo;
        $query0 = $this->db->query($sql0);
        $result0 = $query0->getRowArray();
        $data['sitadel_synthese'][$this->tAnnee[5]['annee'] . ' - ' . $this->tAnnee[0]['annee']][$geo] = $result0;
        if ($this->ficheType == 'detail') {
            $i = 0;
            $pQuery = $this->db->prepare(static function ($db) {
                $sql2 = self::$select . " FROM data_sitadel WHERE annee = ?" . self::$andGeo;
                return (new Query($db))->setQuery($sql2);
            });
            foreach ($this->tAnnee as $val) {
                $result = $pQuery->execute($val['annee']);
                $result2 = $result->getRowArray();
                $data['sitadel_detail'][$val['annee']][$geo] = $result2;
                if (empty($this->tRubrique[$i])) {
                    $this->tRubrique[$i]['var_croise_lib'] = "Nombre de logements " . $this->libRub . " en date réelle en " . $val['annee'];                    
                    $this->tRubrique[$i]['var_croise_ancre'] = 'nb_log_' . iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $this->libRub) . $i;
                }
                $i++;
            }
        }
        return $data;
    }

    /**
     * Fixe les années à étudiées
     * @return array
     */
    private function getAnnee($contexte)
    {

        $perimAnnee = $contexte == 'sitadel_commence' ? $this->perimetre['anneeSitadel'] : $this->perimetre['anneeSitadelAutorise'];
        $sql = "SELECT DISTINCT(annee) FROM " . $this->dataSource . " WHERE annee <= " . $perimAnnee . " order by annee DESC limit " . $this->nbAnnee;
        $query = $this->db->query($sql);
        $result = $query->getResultArray();
        return($result);
    }
}
