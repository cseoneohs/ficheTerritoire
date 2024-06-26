<?php
namespace App\Controllers;
/**
 * Contrôleur permettant de gérer le remplacement les anciens codes INSEE par les nouveaux dans des tables de données
 * cas des fusions de communes / nouvelles communes
 * ce script permet de mettre à jour les communes anciennes communes -> nouvelles communes
 * 
 */

use CodeIgniter\Controller;

/**
 * Contrôleur permettant de gérer le remplacement les anciens codes INSEE par les nouveaux
 */
class UpdateDb extends Controller {

    protected $tTables = array();
    private $tIpAllowed = array("77.159.233.74", "::1", "127.0.0.1", "192.168.111.15", "192.168.111.20", "82.64.236.152");

    /**
     * UpdateDb model
     * @var \App\Models\UpdatedbModel
     */
    protected $updataModel = null;

    /**
     *
     */
    public function __construct() {
        $this->updataModel = new \App\Models\UpdatedbModel();
        
    }

    /**
     * Si appeler sans le nom de la table et de la colonne du code INSEE agit sur les tables référencées dans ts_table_data
     * @param string $table concaténation table + __ + colonne du code_insee
     * @example http://ficheterritoire.local/UpdateDb/index/data_sne__code_insee si mise à jour SNE
     */
    public function index($table = null) {
        if (!in_array($this->request->getIPAddress(), $this->tIpAllowed)) {
            die("Accès impossible pour cette IP : ".$this->request->getIPAddress());
        }
        if (isset($table)) {
            $this->tTables = $table;
        }
        $this->process();
    }

    /**
     * Lancement du processus de mise à jour de la table concernée
     * 
     */
    public function process() {
        if (!empty($this->tTables)) {
            $var = explode('__', $this->tTables);
            echo "Table à modifier : $var[0]" . PHP_EOL;
            $this->updataModel->update_commune($var[0], $var[1]);
            echo ' => TERMINE<br>';
        } else {
            $this->tTables = $this->updataModel->get_table();
            echo '<pre>';
            //var_dump($this->tTables);exit;
            foreach ($this->tTables as $table) {
                echo "Table à modifier : {$table['ts_table_data_libel']}" . PHP_EOL;
                $colCommune = (strstr($table['ts_table_data_libel'], 'fd_logemt')) ? 'commune' : (($table['ts_table_data_libel'] == 'data_filosofi') ? 'codgeo' : 'code_insee');
                $this->updataModel->update_commune($table['ts_table_data_libel'], $colCommune);
                echo ' => TERMINE<br>';
            }
        }
    }

}
