<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Libraries\CleanDir;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{

    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;
    protected $session;

    /**
     * IonAuth
     * @var object
     */
    protected $ionAuth;

    /**
     * Les informations de l'utilisateur connecté
     * @var object
     */
    protected $user;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['url', 'form', 'classer_territoire'];

    /**
     *
     * @var array
     */
    public $data = [];

    /**
     * Class Model fd_logemt_
     * @var object
     */
    protected $InseeLogemt;

    /**
     * Class Model data_cc_serie_histo_insee
     * @var object
     */
    protected $InseeHistoPop;

    /**
     * Class Model data_sitadel_autorise && data_sitadel_commence
     * @var object
     */
    protected $Sitadel;

    /**
     * Class Model data_sitadel_commence_neuf_ancien_ordinaire && data_sitadel_commence_utilisation_ordinaire
     * @var object
     */
    protected $SitadelCommenceOrdinaireModel;

    /**
     * Class Model data_rpls
     * @var object
     */
    protected $Rpls;

    /**
     * Class Model data_sne
     * @var object
     */
    protected $Sne;

    /**
     * Class Model data_filosofi
     * @var object
     */
    protected $Filosofi;

    /**
     * Class Model data_obs_artif_conso_com_
     * @var object
     */
    protected $Artificialisation;

    /**
     * Class Model data_lovac
     * @var object
     */
    protected $Lovac;

    /**
     * Class Model
     * @var object
     */
    protected $CartoModel;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        //--------------------------------------------------------------------
        // Preload any models, libraries, etc, here.
        //--------------------------------------------------------------------
        // E.g.:
        $this->session = \Config\Services::session();
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
        $this->user = $this->ionAuth->user()->row();
        $this->user->isAdmin = $this->ionAuth->isAdmin();
        $this->data['user'] = (array) $this->user;
        $this->InseeLogemt = new \App\Models\InseeLogemtModel();
        $this->InseeHistoPop = new \App\Models\InseeHistoPopModel();
        $this->Sitadel = new \App\Models\SitadelModel();
        $this->SitadelCommenceOrdinaireModel = new \App\Models\SitadelCommenceOrdinaireModel();
        $this->Rpls = new \App\Models\RplsModel();
        $this->Sne = new \App\Models\SneModel();
        $this->Filosofi = new \App\Models\FilosofiModel();
        $this->Artificialisation = new \App\Models\ArtificialisationModel();
        $this->Lovac = new \App\Models\LovacModel();
        $this->CartoModel = new \App\Models\CartoModel();
        CleanDir::cleanDir(WRITEPATH . '/download', 'xlsx', 3600);
        CleanDir::cleanDir(WRITEPATH . '/tmp', 'html', 3600);
    }
}
