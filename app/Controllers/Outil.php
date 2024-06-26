<?php

namespace App\Controllers;

use CodeIgniter\Controller;

ini_set('max_execution_time', MAX_EXECUTION_TIME);
ini_set('memory_limit', MEMORY_LIMIT);

/**
 * Outil de découpe de fichiers à partir de fd_logemt
 */
class Outil extends Controller {

    private $perimetre = array();
    public $objPHPExcel = null;
    protected $outilModel = null;

    /**
     * constructeur de la classe DataFiche
     * @param object $dbh
     */
    public function __construct() {
        
        $this->session = \Config\Services::session();
        helper('url');
        $_SESSION['territoireEtude'] = '';
        if (!isset($_SESSION['perimetre_outil']['annee']) || !isset($_SESSION['perimetre_outil']['code'])) {
            return redirect()->to('/perimetre/outil');
        }        
    }

    public function start() {
        unset($_SESSION['perimetre_outil']);
        return redirect()->to('/perimetre/outil');
    }

    /**
     *
     */
    public function index() {
        $this->outilModel = new \App\Models\OutilModel();
        $this->perimetre['annee'] = $_SESSION['perimetre_outil']['annee'];
        $this->perimetre['dept'] = $_SESSION['perimetre_outil']['code'];
        $retour = $this->outilModel->getdata($this->perimetre['annee'], $this->perimetre['dept']);
        $dir = dirname($retour['path']);
        App\Libraries\CleanDir::cleanDir($dir, 'csv', 3600);
        $response = \Config\Services::response();       
        $path = ROOTPATH . '/writable/download/' . $retour['file'] . '.csv';
        $content = file_get_contents($path);
        $name = $retour['file'] . '.csv';
        return $response->download($name, $content, true);        
    }

}
