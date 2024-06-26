<?php

namespace App\Controllers;

/**
 * Controller par défaut
 */
class Start extends BaseController
{

    /**
     * Page appelé par défaut
     */
    public function index()
    {
        if (isset($_SESSION['perimetre'])) {
            unset($_SESSION['perimetre']);
        }
        if (isset($_SESSION['fiche'])) {
            unset($_SESSION['fiche']);
        }
        if (isset($_SESSION['perimetre'])) {
            unset($_SESSION['perimetre']);
        }        
        echo view('template_modules/header.php', $this->data);
        echo view('page/start', $this->data);
        echo view('template_modules/footer.php');
    }
}
