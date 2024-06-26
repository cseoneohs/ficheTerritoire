<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Exporte le contenu d'une table pour un département et une année
 */
class OutilModel extends Model
{

    /**
     * la table a exporter
     * @var string
     */
    private $dataSource = 'fd_logemt';

    //private $dataSourceTest = 'fd_logemt69';

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * export un jeu de données dans un fichier csv
     * @param int $annee
     * @param string $dept
     * @return array
     */
    public function getData($annee, $dept)
    {
        $table = $this->dataSource . '_' . $annee;
        $deptWhere = $dept . '%%%';
        $alea = round(microtime(true));
        $path = ROOTPATH . "/writable/download/" . $dept . "__" . $alea . ".csv";
        $pwd = getenv('database.default.password');
        $user = getenv('database.default.username');
        $sql = "SELECT * FROM " . $table . " WHERE commune LIKE '" . $deptWhere . "'"; //echo $sql;exit;
        $format = ' | sed \'s/\t/","/g;s/^/"/;s/$/"/;s/\n//g\' ';
        if (strstr(site_url(), 'local')) {
            $cmd = "mysql -h 192.168.1.20  --database=outil_eohs_observatoires -u " . $user . " --password=" . $pwd . " -e \"" . $sql . "\"" . $format . "  > " . $path;
        } else {
            $cmd = "mysql -h 95.128.74.184  --database=outil_eohs_observatoires -u " . $user . " --password=" . $pwd . " -e \"" . $sql . "\"" . $format . "  > " . $path;
        }
        exec($cmd);
        return array('path' => $path, 'file' => $dept . "__" . $alea);
    }
}
