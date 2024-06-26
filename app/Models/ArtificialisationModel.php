<?php

namespace App\Models;

use App\Models\FicheModel;

class ArtificialisationModel extends FicheModel
{

    public $perimetre = null;
    private $andGeo = '';
    private $dataSource = 'data_obs_artif_conso_com';
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
        $this->geoEtude = $this->perimetre['codeEtude'];
        $this->geoData = ($_SESSION['territoireEtude'] == 'commune') ? $this->perimetre['codeEtude'] : $this->perimetre['codeGeo'];
        $data = array();
        $this->annee = $this->perimetre['anneeArtificialisation'];
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
        $fiche = ($this->ficheType == 'detail') ? 'artificialisation_detail' : 'artificialisation_synthese';
        $data = array();
        $from = " FROM " . $this->dataSource . " WHERE 1 " . $this->andGeo;
        //flux d'artificialisation 2009-2023 en Ha
        $sql1 = 'SELECT SUM(naf09art23/10000) as "Nombre d\'Ha artificialisé total", '
                . 'SUM(art09act23/10000) as "Nombre d\'Ha artificialisé pour l\'activité", '
                . '((SUM(art09act23/10000)/SUM(naf09art23/10000))*100) as "% d\'Ha artificialisé pour l\'activité", '
                . 'SUM(art09hab23/10000) as "Nombre d\'Ha artificialisé pour l\'habitat", '
                . '(SUM(art09hab23)/SUM(naf09art23)*100) as "% d\'Ha artificialisé pour l\'habitat", '
                . 'SUM(art09mix23/10000) as "Nombre d\'Ha artificialisé mixte", '
                . '(SUM(art09mix23)/SUM(naf09art23)*100) as "% d\'Ha artificialisé mixte", '
                . 'SUM(art09inc23/10000) as "Nombre d\'Ha artificialisé inconnu", '
                . '(SUM(art09inc23)/SUM(naf09art23)*100) as "% d\'Ha artificialisé inconnu" ' . $from;
        //Artificialisation à usage d'habitation en Ha - flux annuels
        $sql2 = 'SELECT SUM(art09hab10/10000) as "Nombre d\'Ha artificialisé pour l\'habitat de 2009 à 2010", '
                . 'SUM(art10hab11/10000) as "Nombre d\'Ha artificialisé pour l\'habitat de 2010 à 2011", '
                . 'SUM(art11hab12/10000) as "Nombre d\'Ha artificialisé pour l\'habitat de 2011 à 2012", '
                . 'SUM(art12hab13/10000) as "Nombre d\'Ha artificialisé pour l\'habitat de 2012 à 2013", '
                . 'SUM(art13hab14/10000) as "Nombre d\'Ha artificialisé pour l\'habitat de 2013 à 2014", '
                . 'SUM(art14hab15/10000) as "Nombre d\'Ha artificialisé pour l\'habitat de 2014 à 2015", '
                . 'SUM(art15hab16/10000) as "Nombre d\'Ha artificialisé pour l\'habitat de 2015 à 2016", '
                . 'SUM(art16hab17/10000) as "Nombre d\'Ha artificialisé pour l\'habitat de 2016 à 2017", '
                . 'SUM(art17hab18/10000) as "Nombre d\'Ha artificialisé pour l\'habitat de 2017 à 2018", '
                . 'SUM(art18hab19/10000) as "Nombre d\'Ha artificialisé pour l\'habitat de 2018 à 2019", '
                . 'SUM(art19hab20/10000) as "Nombre d\'Ha artificialisé pour l\'habitat de 2019 à 2020", '
                . 'SUM(art20hab21/10000) as "Nombre d\'Ha artificialisé pour l\'habitat de 2020 à 2021", '
                . 'SUM(art21hab22/10000) as "Nombre d\'Ha artificialisé pour l\'habitat de 2021 à 2022", '
                . 'SUM(art22hab23/10000) as "Nombre d\'Ha artificialisé pour l\'habitat de 2022 à 2023" ' . $from;
        //Ha artificialisé pour l’habitat
        $sql3 = 'SELECT SUM(pop1420) as "Gain de population de 2014 à 2020",
                 SUM(men1420) as "Gain de ménages de 2014 à 2020",
                 (((SUM(art14hab15) + SUM(art15hab16) + SUM(art16hab17) + SUM(art17hab18) + SUM(art18hab19) + SUM(art19hab20))/SUM(pop1420))/10000) as "Nombre d\'ha artificialisé pour l\'habitat de 2014 à 2020 par habitant supplémentaire",
                 SUM(men1420)/((SUM(art14hab15) + SUM(art15hab16) + SUM(art16hab17) + SUM(art17hab18) + SUM(art18hab19)+ SUM(art19hab20))/10000) as "Nombre de ménages par ha artificialisé à destination de l\'habitat de 2014 à 2020",
                 ((SUM(art09hab10/10000) + SUM(art10hab11/10000) + SUM(art11hab12/10000) + SUM(art12hab13/10000) + SUM(art13hab14/10000) + SUM(art14hab15/10000) + SUM(art15hab16/10000) + SUM(art16hab17/10000) + SUM(art17hab18/10000) + SUM(art18hab19/10000) + SUM(art19hab20/10000) + SUM(art20hab21/10000) + SUM(art21hab22/10000) + SUM(art22hab23/10000))/(SUM(surfcom2023/10000))*100) as "Part d\'ha artificialisé pour l\'habitat sur la surface de la commune",
                 (SUM(surfcom2023)/10000) AS "Surface du territoire"
' . $from;

        $query1 = $this->db->query($sql1);
        $result1 = $query1->getResultArray();
        $data[$fiche][$this->annee]['Flux d\'artificialisation 2009-1/1/' . $this->annee . ' en Ha'][$geo] = $result1[0];
        if (empty($this->tRubrique[0])) {
            $this->tRubrique[0]['var_croise_lib'] = 'Flux d\'artificialisation 2009-' . $this->annee . ' en Ha';
            $this->tRubrique[0]['var_croise_ancre'] = 'flux_artificialisation';
        }

        $query2 = $this->db->query($sql2);
        $result2 = $query2->getResultArray();
        $data[$fiche][$this->annee]['Artificialisation à usage d\'habitation en Ha, flux annuels'][$geo] = $result2[0];
        if (empty($this->tRubrique[1])) {
            $this->tRubrique[1]['var_croise_lib'] = 'Artificialisation à usage d\'habitation en Ha, flux annuels';
            $this->tRubrique[1]['var_croise_ancre'] = 'artificialisation_usage_habitatioon';
        }

        $query3 = $this->db->query($sql3);
        $result3 = $query3->getResultArray();
        $data[$fiche][$this->annee]['Ha artificialisé pour l\'habitat'][$geo] = $result3[0];
        if (empty($this->tRubrique[2])) {
            $this->tRubrique[2]['var_croise_lib'] = 'Ha artificialisé pour l\'habitat';
            $this->tRubrique[2]['var_croise_ancre'] = 'artificialisation_habitat';
        }
        return $data;
    }
}
