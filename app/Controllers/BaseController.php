<?php

namespace App\Controllers;

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
    protected $request;
    protected $helpers = ['form','website', 'text'];

    // Deklarasikan properti-properti yang digunakan di sini
    protected $session;
    protected $db;
    protected $pager;
    protected $simple_login;
    protected $website;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Panggil konstruktor parent
        parent::initController($request, $response, $logger);

        // Inisialisasi properti yang diperlukan
        $this->session          = \Config\Services::session();
        $this->db               = \Config\Database::connect();
        $this->pager            = \Config\Services::pager();
        $this->simple_login     = new Simple_login();
        $this->website          = new Website();
    }
}
