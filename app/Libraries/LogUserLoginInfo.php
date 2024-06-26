<?php

namespace App\Libraries;

use App\Models\SystemModel;

/**
 * Enregistrement des connexions des utilisateurs
 * @author christian
 */
class LogUserLoginInfo
{

    protected $systemModel = null;

    public function __construct()
    {
        $this->systemModel = new SystemModel();
    }

    public function logInfo($param)
    {
        $this->systemModel->logUserLoginInfo($param);
    }

}
