<?php

namespace App\Controllers;

/**
 * Controller affichage du journal des visites
 */
class Visites extends BaseController
{

    /**
     * Page appelé par défaut
     */
    public function index()
    {
        $visiteModel = new \App\Models\SystemModel();
        $this->data['visites'] = $visiteModel->getVisite();
        echo view('template_modules/header.php', $this->data);
        echo view('page/visite', $this->data);
        echo view('template_modules/footer.php');
    }

}
