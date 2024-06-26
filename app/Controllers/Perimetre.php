<?php

namespace App\Controllers;

/**
 * Définition du périmètre
 *
 */
use CodeIgniter\Controller;
use App\Models\PerimetreModel;

/**
 * Classe définissant le périmètre de travail
 */
class Perimetre extends BaseController
{
    protected $PerimetreModel = null;
    protected $dataperim = array();

    public function __construct()
    {       
        $this->PerimetreModel = new PerimetreModel();
        $this->dataperim['annee'] = $this->PerimetreModel->selectAnneeInsee();
        $this->dataperim['anneeInseeHistoPop'] = $this->PerimetreModel->selectAnneeData('data_cc_serie_histo_insee');
        $this->dataperim['anneeSitadel'] = $this->PerimetreModel->selectAnneeData('data_sitadel');
        unset($this->dataperim['anneeSitadel'][0]);
        $this->dataperim['anneeSitadelNeufAncien'] = $this->PerimetreModel->selectAnneeData('data_sitadel_commence_neuf_ancien_ordinaire');
        $this->dataperim['anneeSitadelUtilisation'] = $this->PerimetreModel->selectAnneeData('data_sitadel_commence_utilisation_ordinaire');
        $this->dataperim['anneeSitadelAutorise'] = $this->PerimetreModel->selectAnneeData('data_sitadel');
        $this->dataperim['anneeRpls'] = $this->PerimetreModel->selectAnneeData('data_rpls');
        $this->dataperim['anneeSne'] = $this->PerimetreModel->selectAnneeData('data_sne');
        $this->dataperim['anneeFilosofi'] = $this->PerimetreModel->selectAnneeData('data_filosofi');
        $this->dataperim['anneeArtificialisation'] = $this->PerimetreModel->selectAnneeArtificialisation();
        $this->dataperim['anneeLovac'] = $this->PerimetreModel->selectAnneeData('data_lovac');
        $this->dataperim['dept'] = $this->PerimetreModel->selectDept();
        $this->dataperim['varFdLogement'] = $this->PerimetreModel->selectVarFdLogement();
    }

    public function index()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'perimEtuDep' => ['label' => 'Département', 'rules' => 'required'],
            'perimEtuEpci' => ['label' => 'EPCI', 'rules' => 'required_without[perimEtuScot]'],
            'chkFiche' => ['label' => 'Fiche à produire', 'rules' => 'required']
        ]);

        $valid = ($this->request->getPost('fd_logemt')) || ($this->request->getPost('sitadel_commence') || ($this->request->getPost('sitadel_commence_neuf_ancien')) || ($this->request->getPost('sitadel_commence_utilisation')) || ($this->request->getPost('insee_histo_pop')) || ($this->request->getPost('rpls')) || ($this->request->getPost('sitadel_autorise')) || ($this->request->getPost('sne')) || ($this->request->getPost('filosofi')) || ($this->request->getPost('artificialisation') || ($this->request->getPost('lovac'))));
        if ($this->request->getMethod() === 'get') {
            echo view('template_modules/header.php', $this->data);
            echo view('page/perimetre', $this->dataperim);
            echo view('template_modules/footer.php');
        } elseif (!$valid) {
            $this->dataperim['validation'] = ['Merci de choisir la source de données'];
            echo view('template_modules/header.php', $this->data);
            echo view('page/perimetre', $this->dataperim);
            echo view('template_modules/footer.php');
        } elseif (!$validation->withRequest($this->request)->run()) {
            $this->dataperim['validation'] = $validation->listErrors();
            echo view('template_modules/header.php', $this->data);
            echo view('page/perimetre', $this->dataperim);
            echo view('template_modules/footer.php');
        } else {
            $this->setPerimetre();
        }
    }

    public function deptSubmit()
    {
        unset($_SESSION['secteurLibel']);
        $dept = $this->request->getPost('dept');
        $this->dataperim['scot'] = $this->PerimetreModel->selectScot(null, $dept);
        $this->dataperim['epci'] = $this->PerimetreModel->selectEpci(null, $dept);
        $this->dataperim['commune'] = $this->PerimetreModel->selectComm(array('geo' => 'dept', 'code' => $dept));
        echo json_encode($this->dataperim);
    }

    public function epciSubmit()
    {
        $epci = $this->request->getPost('epci');
        $this->dataperim['secteur'] = $this->PerimetreModel->selectSect(null, array('code' => $epci));
        $this->dataperim['commune'] = $this->PerimetreModel->selectComm(array('geo' => 'epci', 'code' => $epci));
        echo json_encode($this->dataperim);
    }

    public function scotSubmit()
    {
        $scot = $this->request->getPost('scot');
        $this->dataperim['commune'] = $this->PerimetreModel->selectComm(array('geo' => 'scot', 'code' => $scot));
        echo json_encode($this->dataperim);
    }

    public function secteurSubmit()
    {
        $secteur = $this->request->getPost('secteur');
        $_SESSION['secteurLibel'] = $secteur;
        $this->dataperim['commune'] = $this->PerimetreModel->selectComm(array('geo' => 'secteur', 'code' => $secteur));
        echo json_encode($this->dataperim);
    }

    public function communeSubmit()
    {
        $commune = $this->request->getPost('commune');
        $this->dataperim['iris'] = $this->PerimetreModel->selectIris($commune);
        echo json_encode($this->dataperim);
    }

    private function setPerimetre($dest = 'fiche')
    {
        $perimetre = array();

        $perimetre['source']['fd_logemt'] = ($this->request->getPost('fd_logemt')) ? true : false;
        $perimetre['annee'] = $this->request->getPost('perimAnnee');
        $perimetre['source']['sitadel_commence'] = ($this->request->getPost('sitadel_commence')) ? true : false;
        $perimetre['anneeSitadel'] = $this->request->getPost('perimAnneeSitadel');
        $perimetre['source']['sitadel_commence_neuf_ancien'] = ($this->request->getPost('sitadel_commence_neuf_ancien')) ? true : false;
        $perimetre['anneeSitadelNeufAncien'] = $this->request->getPost('perimAnneeSitadelNeufAncien');
        $perimetre['source']['sitadel_commence_utilisation'] = ($this->request->getPost('sitadel_commence_utilisation')) ? true : false;
        $perimetre['anneeSitadelUtilisation'] = $this->request->getPost('perimAnneeSitadelUtilisation');
        $perimetre['source']['sitadel_autorise'] = ($this->request->getPost('sitadel_autorise')) ? true : false;
        $perimetre['anneeSitadelAutorise'] = $this->request->getPost('perimAnneeSitadelAutorise');
        $perimetre['source']['insee_histo_pop'] = ($this->request->getPost('insee_histo_pop')) ? true : false;
        $perimetre['anneeInseeHistoPop'] = $this->request->getPost('perimAnneeInseeHistoPop');
        $perimetre['source']['rpls'] = ($this->request->getPost('rpls')) ? true : false;
        $perimetre['anneeRpls'] = $this->request->getPost('perimAnneeRpls');
        $perimetre['source']['sne'] = ($this->request->getPost('sne')) ? true : false;
        $perimetre['anneeSne'] = $this->request->getPost('perimAnneeSne');
        $perimetre['source']['filosofi'] = ($this->request->getPost('filosofi')) ? true : false;
        $perimetre['anneeFilosofi'] = $this->request->getPost('perimAnneeFilosofi');
        $perimetre['source']['artificialisation'] = ($this->request->getPost('artificialisation')) ? true : false;
        $perimetre['anneeArtificialisation'] = $this->request->getPost('perimAnneeArtificialisation');
        $perimetre['source']['lovac'] = ($this->request->getPost('lovac')) ? true : false;
        $perimetre['anneeLovac'] = $this->request->getPost('perimAnneeLovac');

        if ($this->request->getPost('perimEtuIris')) {
            $perimetre['geo'] = 'iris';
            $perimetre['code'] = $this->request->getPost('perimEtuIris');
        } elseif ($this->request->getPost('perimEtuCom')) {
            $perimetre['geo'] = 'commune';
            $perimetre['code'] = $this->request->getPost('perimEtuCom');
        } elseif ($this->request->getPost('perimEtuSect')) {
            $perimetre['geo'] = 'secteur';
            $perimetre['code'] = $this->request->getPost('perimEtuSect');
        } elseif ($this->request->getPost('perimEtuEpci')) {
            $perimetre['geo'] = 'epci';
            $perimetre['code'] = $this->request->getPost('perimEtuEpci');
        } elseif ($this->request->getPost('perimEtuScot')) {
            $perimetre['geo'] = 'scot';
            $perimetre['code'] = $this->request->getPost('perimEtuScot');
        } elseif ($this->request->getPost('perimEtuDep')) {
            $perimetre['geo'] = 'departement';
            $perimetre['code'] = $this->request->getPost('perimEtuDep');
        } else {
            return null;
        }
        $dataTemp = ($_SESSION['territoireEtude'] == 'commune') ? $this->PerimetreModel->selectComm($perimetre) : $this->PerimetreModel->selectEpciEtude($perimetre);
        foreach ($dataTemp as $key => $value) {
            if ($_SESSION['territoireEtude'] == 'commune') {
                $perimetre['codeEtude'][$key] = $value['codegeo'];
                $perimetre['libEtude'][$key] = $value['libgeo'];
                $perimetre['labelEtude'][$value['libgeo']] = $value['codegeo'];
                if (isset($_SESSION['secteurLibel'])) {
                    $perimetre['secteur'][$value['codegeo']] = $this->PerimetreModel->selectSect($_SESSION['secteurLibel']);
                }
                $perimetre['epci'][$key] = $value['commune_epci'];
                $perimetre['scot'][$value['codegeo']] = $this->PerimetreModel->selectScot($value['codegeo']);
            } elseif ($_SESSION['territoireEtude'] == 'epci') {
                $perimetre['codeEtude'][$key] = $value['code_epci'];
                $perimetre['libEtude'][$key] = $value['lib_epci'];
                $perimetre['labelEtude'][$value['lib_epci']] = $value['code_epci'];
                $perimetre['codeGeo'][$key] = $value['commune_code_geo'];
            }

            $perimetre['departement'][$key] = $value['commune_dep'];
            $perimetre['region'][$key] = $value['commune_reg'];
        }
        // var_dump($perimetre);exit;
        $perimetreComp = $this->request->getPost('chkTerritoire');
        if (is_array($perimetreComp)) {
            if (in_array('secteur', $perimetreComp)) {
                d($perimetre);
                $perimetre['secteurLib'] = $this->PerimetreModel->selectSect($_SESSION['secteurLibel']);
            }
            if (in_array('epci', $perimetreComp)) {
                $perimetre['epciLib'] = $this->PerimetreModel->selectEpci($perimetre['epci']);
            }
            if (in_array('departement', $perimetreComp)) {
                $perimetre['deptLib'] = $this->PerimetreModel->selectDept($perimetre['departement']);
                foreach (array_keys($perimetre['deptLib']) as $code) {
                    $perimetreComp[] = 'departement'.$code;
                }
                unset($perimetreComp[array_search('departement', $perimetreComp)]);
            }
        }
        $fiche = $this->request->getPost('chkFiche');
        if (($fiche[0] == 'detail') && $perimetre['source']['fd_logemt']) {
            $perimetre['varFdLogement'] = $this->request->getPost('var_fd_logemt');
        } else {
            $perimetre['varFdLogement'] = null;
        }

        $_SESSION['perimetre'] = $perimetre;
        $_SESSION['perimetreComp'] = $perimetreComp;
        $_SESSION['fiche'] = $fiche[0];
        header('Location: /' . $dest);
        exit();
    }

    /**
     * Affichage du choix du périmètre pour la partie Outil
     */
    public function outil()
    {
        $validation = \Config\Services::validation();
        $validation->setRule('perimEtuDep', 'Département', 'required');
        if (!$validation->withRequest($this->request)->run()) {
            $this->dataperim['contexte'] = 'outil';
            $this->dataperim['validation'] = $validation->listErrors();
            echo view('template_modules/header.php', $this->data);
            echo view('page/perimetre_outil', $this->dataperim);
            echo view('template_modules/footer');
        } else {
            $this->setPerimetreOutil('outil');
        }
    }

    /**
     * Le choix du périmètre pour la partie Outil a été effectué
     * @param string $dest la page vers laquelle on est renvoyé
     */
    private function setPerimetreOutil($dest = 'outil')
    {
        $perimetreOutil = array();
        unset($_SESSION['perimetre_outil']);
        $perimetreOutil['annee'] = $this->request->getPost('perimAnnee');
        $perimetreOutil['code'] = $this->request->getPost('perimEtuDep');
        foreach ($perimetreOutil as $key => $value) {
            $_SESSION['perimetre_outil'][$key] = $value;
        }
        header('Location: /' . $dest);
        exit();
    }

    /**
     * le formulaire d'import de fichier csv a importer a ete poste
     * @return string on retourne à la page précédente avec un message
     */
    public function importGeoFile()
    {
        if (isset($_FILES) && count($_FILES) > 0) {
            $selectGeo = filter_input(INPUT_POST, 'selectGeo', FILTER_SANITIZE_STRING);
            $tFiles = explode('.', $_FILES['importFileGeo']['name']);
            $table = $tFiles[0];
            if ($selectGeo == 'secteur') {
                $codeAlreadyExisting = $this->PerimetreModel->selectExist();
            }
            $data = $this->readFile($_FILES);
            $codeExist = ($selectGeo == 'secteur') ? $this->testExist($data, $codeAlreadyExisting) : false;
            if (!$codeExist) {
                $reponse = '';
                foreach ($data as $value) {
                    $reponse .= $this->PerimetreModel->insertData($table, $value);
                }
            } else {
                $reponse = '<span class="text-danger font-weight-bold">Fichier non importé : ' . $codeExist . '<br>';
            }
            return redirect()->back()->with('reponseImport', $reponse);
        }
    }

    /**
     *
     * @param array $data le tableau des donnees que l'on souhaite importer
     * @return boolean
     */
    private function testExist($data, $codeAlreadyExisting)
    {
        $exist = false;
        $existLibel = in_array($data[1][1], array_column($codeAlreadyExisting, 'libel'));
        $listCode = array_column($codeAlreadyExisting, 'codegeo');
        foreach ($listCode as $key => $values) {
            if ($values == $data[1][2]) {
                $exist = "Secteur : " . $values . " déjà présent dans la BDD avec l'intitulé : " . $codeAlreadyExisting[$key]['libel'];
                return $exist;
            }
        }
        if ($existLibel) {
            $exist = "Secteur " . $codeAlreadyExisting[0]['libel'] . " déjà présent dans la BDD";
            return $exist;
        }
        /*         * if ($existCodeGeo) {
          d($existCodeGeo);d($data[1][2]);d(array_column($codeAlreadyExisting, 'codegeo'));exit;
          $exist = "Un secteur identique existe déjà : " .$codeAlreadyExisting[0]['libel'];
          return $exist;
          } */
        return $exist;
    }

    /**
     *
     * @param array $file
     * @return array
     */
    private function readFile($file)
    {
        $data = array();
        $fp = fopen($file['importFileGeo']['tmp_name'], 'r');
        $row = 1;
        $ind = 0;
        $lib = '';
        while ($getdata = fgetcsv($fp, 0, ';')) {
            if ($row > 1) {
                $newlib = $getdata[0];
                if (($newlib != $lib)) {
                    $ind++;
                }
                $data[$ind][1] = $getdata[0];
                $data[$ind][2] = empty($data[$ind][2]) ? '' . $getdata[1] . ',' : $data[$ind][2] . $getdata[1] . ',';
                $data[$ind][3] = $getdata[2];
                $lib = $newlib;
            }
            $row++;
        }
        fclose($fp);
        foreach ($data as $key => $value) {
            $data[$key][0] = '';
            $data[$key][2] = rtrim($data[$key][2], ',');
        }
        return $data;
    }
}
