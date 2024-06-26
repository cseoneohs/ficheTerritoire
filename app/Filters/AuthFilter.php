<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use IonAuth\Libraries\IonAuth;

/**
 * Filtre appliqué avant toutes les requêtes sauf :
 * 'except' => ['auth/login', 'auth/forgot_password', 'auth/reset_password/*', 'caleohs/*']*
 * @author christian
 */
class AuthFilter implements FilterInterface
{

    public function before(RequestInterface $request, $arguments = null)
    {
        $this->ionAuth = new IonAuth();
        if (!($this->ionAuth->loggedIn())) {
            session()->set('redirect_url', current_url());
            return redirect()->to('auth/login');
        }
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }

}
