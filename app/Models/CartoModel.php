<?php
namespace App\Models;

/**
 * les données spatiales ont été exportées dans la BDD à partir de qgis avec l'extension MySQL Importer Import spatial and table data into MySQL/MariaDB
 *
 * @author christian
 *
 */



use App\Models\FicheModel;

/**
 * Description of FicheModel
 *
 * @author christian
 */
class CartoModel extends FicheModel
{
    public $perimetre = null;
    protected $tRubrique = array();

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        parent::__construct();
        /**
         * DEBUG ONLY
         */
        if ((defined('ENVIRONMENT') && ENVIRONMENT == 'development') || defined('DEBUG')) {
            //$this->dataSource = $this->dataSourceTest;
        }
        //var_dump($this->perimetre);exit;
    }
    /**
     * retourne un tableau d'objets de type geojson contenant les coordonnées des polygones des communes
     * NB : les communes des territoires d'outre mer ne sont pas positionnées là où elles sont réellement mais à l'ouest de la Corse
     * @return array
     *
     */
    public function getData()
    {
        if ($_SESSION['territoireEtude'] != 'commune') {
            return null;
        }
        $this->geoData = $this->perimetre['codeEtude'];
        $geo = implode(",", $this->geoData);
        $sql = "SELECT CONCAT('\"type\": \"Feature\",\"geometry\":', ST_AsGeoJSON(ST_GeomFromWKB(SHAPE)),',\"properties\": {\"code_insee\": \"',codgeo,'\",\"lib_commune\": \"',libgeo,'\"}') as geojson FROM geo_com2020_geojson WHERE codgeo IN ($geo)";
        //$sql = "SELECT CONCAT('\"type\": \"Feature\",\"geometry\":', ST_AsGeoJSON(ST_GeomFromWKB(SHAPE)), ',\"type\": \"Point\", \"coordinates\": [', xcl4326,',', ycl4326, ',]', ',\"properties\": {\"code_insee\": \"',codgeo,'\",\"lib_commune\": \"',libgeo,'\"}') as geojson FROM geo_com2020_geojson WHERE codgeo IN ($geo)";
        //echo $sql;
        $query1 = $this->db->query($sql);
        $result1 = $query1->getResult();
        return $result1;
    }
}
