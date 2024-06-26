<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Description of SystemModel
 *
 * @author christian
 */
class SystemModel extends Model
{

    /**
     * Enregistrement des informations de connexion dans la table "users_login_info"
     * @param type $param
     * @return int
     */
    public function logUserLoginInfo($param)
    {
        $dateTime = new \DateTimeImmutable("now", new \DateTimeZone("Europe/Paris"));
        $date = $dateTime->format("d-m-Y");
        $time = $dateTime->format("H:i:s");
        $params = ['users_id' => $param->user_id, 'users_first_name' => $param->first_name, 'users_last_name' => $param->last_name, 'users_login_date' => $date, 'users_login_time' => $time];
        $builder = $this->db->table("auth_login_info");
        $builder->insert($params);
        $lastInsertId = $this->db->insertID();
        return $lastInsertId;
    }
    /**
     * 
     * @return array le tableau contenant les visites
     */
    public function getVisite()
    {
        $builder = $this->db->table("auth_login_info");
        $query = $builder->select('id, CONCAT(users_first_name, " ",users_last_name) AS "Nom",users_login_date')->orderBy('id', 'DESC')->get();
        $result = $query->getResultArray();
        return $result;
    }

}
