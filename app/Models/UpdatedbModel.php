<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Query;

/**
 * Description of Update_db
 *
 * @author christian
 */
class UpdatedbModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Recherche la liste des tables métiers contenant les données
     * @return array
     */
    public function get_table()
    {
        $sql = 'SELECT `ts_table_data_libel` FROM `ts_table_data` WHERE 1';
        $query = $this->db->query($sql);
        $result = $query->getResultArray();
        return $result;
    }

    /**
     * remplace un code insee par un nouveau dans une table (nouvelles communes)
     * @param string $table la table où effectuer le remplacement
     * @param string $colCommune le nom de la  colonne contenant le code INSEE
     */
    public function update_commune($table, $colCommune)
    {
        $pQuery = $this->db->prepare(function ($db) use ($table, $colCommune) {
            $sql = "UPDATE " . $table . " SET " . $colCommune . " = ? WHERE " . $colCommune . "= ?";
            return (new Query($db))->setQuery($sql);
        });
        $sql = "SELECT code_insee_nouvelle_commune, code_insee_ancienne_commune FROM ts_geo_communes_nouvelles WHERE code_insee_nouvelle_commune!=code_insee_ancienne_commune AND annnee=(SELECT MAX(`annnee`) FROM ts_geo_communes_nouvelles)";
        $query = $this->db->query($sql);
        foreach ($query->getResultArray() as $row) {
            $result = $pQuery->execute($row['code_insee_nouvelle_commune'], $row['code_insee_ancienne_commune']);
            if ($this->db->affected_rows != 0) {
                echo '&nbsp;-&nbsp;' . $row['code_insee_ancienne_commune'] . ' remplacée par ' . $row['code_insee_nouvelle_commune'] . '<br>';
            }
        }
    }
}
