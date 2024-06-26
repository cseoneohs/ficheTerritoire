<?php

namespace App\Controllers;

use App\Libraries\ExportToExcelMulti;

/**
 * Classe Fiche, affichage d'une fiche avec une ou plusieurs sources de données
 */
class Fiche extends BaseController
{

    /**
     * tableau contenant le périmètre
     * @var array
     */
    private $perimetre = array();

    /**
     * tableau contenant le jeu de données
     * @var array
     */
    private $dataSet = array();

    /**
     * tableau contenant les titres des tableaux et leurs IDs pemettant de créer des ancres html dans la page
     * @var array
     */
    protected $tCroisement = array();

    /**
     * tableau contenant les sous rubriques de la fiche
     * @var array
     */
    protected $tSsRubrique = array();

    /**
     * le type de fiche : synthèse ou détail
     * @var string
     */
    protected $ficheType = null;

    /**
     * Force à choisir un périmètre
     * @param string $territoireEtude
     */
    public function start($territoireEtude)
    {
        $_SESSION['territoireEtude'] = $territoireEtude;
        if (!isset($_SESSION['fiche'])) {
            return redirect()->to('/perimetre');
        }
        unset($_SESSION['perimetre']);
        unset($_SESSION['fiche']);
        unset($_SESSION['perimetre_outil']);
        return redirect()->to('/perimetre');
    }

    /**
     * Méthode principale, affiche une fiche
     */
    public function index()
    {
        $this->ficheType = $_SESSION['fiche'];
        echo view('template_modules/header.php');
        if ($this->ficheType == 'synthese') {
            $this->processFicheSynthese();
        } else {
            $this->processFicheDetail();
        }
        echo view('template_modules/footer');
    }

    /**
     * Recherche des données
     * @param string $ficheType (Détail ou Synthèse)
     */
    private function getData($ficheType = 'synthese')
    {
        if ($_SESSION['perimetre']['source']['insee_histo_pop']) {
            $this->dataSet['insee_histo_pop'] = $this->InseeHistoPop->process();
        }
        if ($_SESSION['perimetre']['source']['fd_logemt'] && $ficheType == 'synthese') {
            $this->dataSet['fd_logemt'] = $this->InseeLogemt->process();
        }
        if ($_SESSION['perimetre']['source']['sitadel_commence']) {
            $this->dataSet['sitadel_commence'] = $this->Sitadel->process('sitadel_commence');
        }
        if ($_SESSION['perimetre']['source']['sitadel_commence_neuf_ancien']) {
            $this->dataSet['sitadel_commence_neuf_ancien'] = $this->SitadelCommenceOrdinaireModel->process('sitadel_commence_neuf_ancien');
        }
        if ($_SESSION['perimetre']['source']['sitadel_commence_utilisation']) {
            $this->dataSet['sitadel_commence_utilisation'] = $this->SitadelCommenceOrdinaireModel->process('sitadel_commence_utilisation');
        }
        if ($_SESSION['perimetre']['source']['sitadel_autorise']) {
            $this->dataSet['sitadel_autorise'] = $this->Sitadel->process('sitadel_autorise');
        }
        if ($_SESSION['perimetre']['source']['rpls']) {
            $this->dataSet['rpls'] = $this->Rpls->process();
        }
        if ($_SESSION['perimetre']['source']['sne']) {
            $this->dataSet['sne'] = $this->Sne->process();
            $var = $this->Sne->getVarLibel('sne');
            foreach ($var as $key => $value) {
                $this->dataSet['var'][$key] = $value;
            }
        }
        if ($_SESSION['perimetre']['source']['filosofi']) {
            $this->dataSet['filosofi'] = $this->Filosofi->process();
        }
        if ($_SESSION['perimetre']['source']['artificialisation']) {
            $this->dataSet['artificialisation'] = $this->Artificialisation->process();
        }
        if ($_SESSION['perimetre']['source']['lovac']) {
            $this->dataSet['lovac'] = $this->Lovac->process();
        }
    }

    /**
     * Affiche la fiche de synthèse
     */
    private function processFicheSynthese()
    {
        $this->getData();
        if ($_SESSION['perimetre']['source']['insee_histo_pop']) {
            echo view('table_modules/displayEvolPopInsee');
            echo view('table_modules/displayInseeHistoPop');
        }
        if ($_SESSION['perimetre']['source']['fd_logemt']) {
            echo view('table_modules/displayFdLogement');
        }
        if ($_SESSION['perimetre']['source']['sitadel_commence']) {
            echo view('table_modules/displaySitadel');
        }
        if ($_SESSION['perimetre']['source']['sitadel_commence_neuf_ancien']) {
            echo view('table_modules/displaySitadelOrdinaire');
        }
        if ($_SESSION['perimetre']['source']['sitadel_commence_utilisation']) {
            echo view('table_modules/displaySitadelOrdinaire');
        }
        if ($_SESSION['perimetre']['source']['sitadel_autorise']) {
            echo view('table_modules/displaySitadel');
        }
        if ($_SESSION['perimetre']['source']['rpls']) {
            echo view('table_modules/displayRpls');
        }
        if ($_SESSION['perimetre']['source']['sne']) {
            echo view('table_modules/displaySne');
        }
        if ($_SESSION['perimetre']['source']['filosofi']) {
            echo view('table_modules/displayFilosofi');
        }
        if ($_SESSION['perimetre']['source']['artificialisation']) {
            echo view('table_modules/displayArtificialisation');
        }
        if ($_SESSION['perimetre']['source']['lovac']) {
            echo view('table_modules/displayLovac');
        }
        $territoire = $this->getTerritoire();
        echo view('template_modules/sommaire_page_synthese', $dataset = array('territoire' => $territoire));
        echo view('page/ficheSynthese', $dataset = array('fiche' => $this->InseeLogemt, 'dataSet' => $this->dataSet));
    }

    /**
     * Affiche la fiche détail
     */
    private function processFicheDetail()
    {
        $this->getData('detail');
        $pageFicheDetail = 'page/ficheDetailTemp';
        $html = '';
        if ($_SESSION['perimetre']['source']['insee_histo_pop']) {
            $this->tCroisement = $this->InseeHistoPop->getSousRubrique();
            echo view('table_modules/displayEvolPopInsee');
            echo view('table_modules/displayInseeHistoPop');
            $html .= view($pageFicheDetail, $dataset = array('tCroisement' => $this->tCroisement, 'fiche' => $this->InseeLogemt, 'dataSet' => $this->dataSet, 'source' => 'insee_histo_pop'));
            $this->tSsRubrique = array_merge($this->tSsRubrique, $this->tCroisement);
        }
        if ($_SESSION['perimetre']['source']['fd_logemt']) {
            $this->tCroisement = $this->InseeLogemt->getSousRubrique();
            echo view('table_modules/displayFdLogement');
            foreach ($this->tCroisement as $value) {
                $this->dataSet['fd_logemt'] = $this->InseeLogemt->process($value);
                $html .= view($pageFicheDetail, $dataset = array('tCroisement' => $value, 'fiche' => $this->InseeLogemt, 'dataSet' => $this->dataSet, 'source' => 'fd_logemt'));
            }
            $this->tSsRubrique = array_merge($this->tSsRubrique, $this->tCroisement);
        }
        if ($_SESSION['perimetre']['source']['sitadel_commence']) {
            $this->tCroisement = $this->Sitadel->getSousRubrique();
            echo view('table_modules/displaySitadel');
            $html .= view($pageFicheDetail, $dataset = array('tCroisement' => $this->tCroisement, 'fiche' => $this->InseeLogemt, 'dataSet' => $this->dataSet, 'source' => 'sitadel_commence')); 
            foreach ($this->tCroisement as $key => $value) {                
                $this->tCroisement[$key]['var_croise_ancre'] = str_replace('nb_log_autorises', 'nb_log_commences', $value['var_croise_ancre']);
                $this->tCroisement[$key]['var_croise_lib'] = str_replace('autorisés', 'commencés', $value['var_croise_lib']);
            }
            $this->tSsRubrique = array_merge($this->tSsRubrique, $this->tCroisement);
        }            
        if ($_SESSION['perimetre']['source']['sitadel_commence_neuf_ancien']) {
            $this->tCroisement = $this->SitadelCommenceOrdinaireModel->getSousRubrique();
            echo view('table_modules/displaySitadelOrdinaire');
            $html .= view($pageFicheDetail, $dataset = array('tCroisement' => $this->tCroisement, 'fiche' => $this->InseeLogemt, 'dataSet' => $this->dataSet, 'source' => 'sitadel_commence_neuf_ancien'));
            $this->tSsRubrique = array_merge($this->tSsRubrique, $this->tCroisement);
        }
        if ($_SESSION['perimetre']['source']['sitadel_commence_utilisation']) {
            $this->tCroisement = $this->SitadelCommenceOrdinaireModel->getSousRubrique();
            echo view('table_modules/displaySitadelOrdinaire');
            $html .= view($pageFicheDetail, $dataset = array('tCroisement' => $this->tCroisement, 'fiche' => $this->InseeLogemt, 'dataSet' => $this->dataSet, 'source' => 'sitadel_commence_utilisation'));
            $this->tSsRubrique = array_merge($this->tSsRubrique, $this->tCroisement);
        }
        if ($_SESSION['perimetre']['source']['sitadel_autorise']) {
            $this->tCroisement = $this->Sitadel->getSousRubrique();
            echo view('table_modules/displaySitadel');
            $html .= view($pageFicheDetail, $dataset = array('tCroisement' => $this->tCroisement, 'fiche' => $this->InseeLogemt, 'dataSet' => $this->dataSet, 'source' => 'sitadel_autorise'));
            $this->tSsRubrique = array_merge($this->tSsRubrique, $this->tCroisement);
        }
        if ($_SESSION['perimetre']['source']['rpls']) {
            $carto['dataCarto'] = $this->setCarto() ? $this->CartoModel->getData() : null;
            $this->tCroisement = $this->Rpls->getSousRubrique();
            echo view('table_modules/displayRpls', $carto);
            $html .= view($pageFicheDetail, $dataset = array('tCroisement' => $this->tCroisement, 'fiche' => $this->InseeLogemt, 'dataSet' => $this->dataSet, 'source' => 'rpls'));
            $this->tSsRubrique = array_merge($this->tSsRubrique, $this->tCroisement);
        }
        if ($_SESSION['perimetre']['source']['sne']) {
            $this->tCroisement = $this->Sne->getSousRubrique();
            echo view('table_modules/displaySne');
            $html .= view($pageFicheDetail, $dataset = array('tCroisement' => $this->tCroisement, 'fiche' => $this->InseeLogemt, 'dataSet' => $this->dataSet, 'source' => 'sne'));
            $this->tSsRubrique = array_merge($this->tSsRubrique, $this->tCroisement);
        }
        if ($_SESSION['perimetre']['source']['filosofi']) {
            $carto['dataCarto'] = $this->setCarto() ? $this->CartoModel->getData() : null;
            $this->tCroisement = $this->Filosofi->getSousRubrique();
            echo view('table_modules/displayFilosofi', $carto);
            $html .= view($pageFicheDetail, $dataset = array('tCroisement' => $this->tCroisement, 'fiche' => $this->InseeLogemt, 'dataSet' => $this->dataSet, 'source' => 'filosofi'));
            $this->tSsRubrique = array_merge($this->tSsRubrique, $this->tCroisement);
        }
        if ($_SESSION['perimetre']['source']['artificialisation']) {
            $this->tCroisement = $this->Artificialisation->getSousRubrique();
            include(APPPATH . 'Views/table_modules/displayArtificialisation.php');
            $html .= view($pageFicheDetail, $dataset = array('tCroisement' => $this->tCroisement, 'fiche' => $this->InseeLogemt, 'dataSet' => $this->dataSet, 'source' => 'artificialisation'));
            $this->tSsRubrique = array_merge($this->tSsRubrique, $this->tCroisement);
        }
        if ($_SESSION['perimetre']['source']['lovac']) {
            $this->tCroisement = $this->Lovac->getSousRubrique();
            include(APPPATH . 'Views/table_modules/displayLovac.php');
            $html .= view($pageFicheDetail, $dataset = array('tCroisement' => $this->tCroisement, 'fiche' => $this->InseeLogemt, 'dataSet' => $this->dataSet, 'source' => 'lovac'));
            $this->tSsRubrique = array_merge($this->tSsRubrique, $this->tCroisement);
        }
        echo view('template_modules/sommaire_page', $dataset = array('tSsRubrique' => $this->tSsRubrique));
        echo view('page/ficheDetail', $dataSet = array('html' => $html));
    }

    /**
     * Méthode pour télé-charger une fiche au format excel tel que affichée à l'écran
     * @param string $file
     */
    public function download($file)
    {
        $response = \Config\Services::response();
        $filename = filter_var($file, FILTER_VALIDATE_INT);
        $path = ROOTPATH . '/writable/download/' . $filename . '.xlsx';
        $data = file_get_contents($path);
        $name = $this->ficheType == 'synthese' ? 'fiche_synthese.xlsx' : 'fiche_detail.xlsx';
        return $response->download($name, $data, true);
    }

    /**
     * Méthode pour exporter la fiche au format xlsx
     */
    public function export()
    {
        $this->ficheType = $_SESSION['fiche'];
        echo view('template_modules/header');
        if ($this->ficheType == 'synthese') {
            $title = "fiche synthèse";
            $this->getData();
            $territoire = $this->getTerritoire();
            $dataset['territoire'] = $territoire;
            $dataset['fiche'] = $this->InseeLogemt;
            $dataset['data'] = $this->dataSet;
        } else {
            $title = "fiche détail";
            $this->getData('detail');
            if ($_SESSION['perimetre']['source']['fd_logemt']) {
                $this->tCroisement = $this->InseeLogemt->getSousRubrique();
                foreach ($this->tCroisement as $value) {
                    $this->dataSet['fd_logemt'][$value['var_croise_lib']] = $this->InseeLogemt->process($value);
                }
            }
        }
        $exportToExcel = new ExportToExcelMulti();
        $file = $exportToExcel->export($this->dataSet, $this->InseeLogemt->perimetre, $title);
        $response = \Config\Services::response();
        $path = ROOTPATH . '/writable/download/' . $file . '.xlsx';
        $content = file_get_contents($path);
        $name = 'fiche_territoire.xlsx';
        return $response->download($name, $content, true);
    }

    /**
     * creation d'un tableau contenant les territoires etudies
     * @return array
     */
    private function getTerritoire()
    {
        $territoire = array();
        foreach ($this->InseeLogemt->perimetre['codeEtude'] as $code) {
            $territoire[] = $code;
        }

        if (isset($this->InseeLogemt->perimetre['perimComp']) && !empty($this->InseeLogemt->perimetre['perimComp'])) {
            foreach ($this->InseeLogemt->perimetre['perimComp'] as $value) {
                if ($value != 'secteur') {
                    $territoire[] = $value;
                }
            }
        }
        return $territoire;
    }

    /**
     * la carte est affichée si seulement une source de données est sélectionnée
     * @return boolean
     */
    private function setCarto()
    {
        $select = 0;
        foreach ($_SESSION['perimetre']['source'] as $value) {
            $select += !($value) ? 0 : 1;
        }
        return $select == 1 ? true : false;
    }
}
