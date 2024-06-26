<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use IonAuth\Libraries\IonAuth;
use App\Libraries\LogUserLoginInfo;

/**
 * Filtre appliqué uniquement pour 
 *  ['auth/login']
 * 
 * @author christian
 */
class IsLoggedInFilter implements FilterInterface
{

    public function before(RequestInterface $request, $arguments = null)
    {
        
    }

    //--------------------------------------------------------------------
    /**
     * Après identification, enregistrement des informations dans la BDD
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param type $arguments
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $this->ionAuth = new IonAuth();
        if ($this->ionAuth->loggedIn()) {
            $user = $this->ionAuth->user()->row();
            $logger = new LogUserLoginInfo();
            $logger->logInfo($user);
        }
    }

}
