<?php
namespace App\Controllers\Staff_keuangan;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Libraries\Simple_login;
use App\Libraries\Website;

/**
 * Class BaseController
 */
abstract class BaseController extends Controller
{
    // Deklarasi properti
    protected $session;
    protected $db;
    protected $pager;
    protected $simple_login;
    protected $website;

    protected $helpers = ['form', 'website', 'text'];

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Panggil constructor parent
        parent::initController($request, $response, $logger);

        // Inisialisasi properti-properti
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
        $this->pager = \Config\Services::pager();
        $this->simple_login = new Simple_login();
        $this->website = new Website();
    }
}
