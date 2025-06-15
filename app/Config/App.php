<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class App extends BaseConfig
{
    // Hapus deklarasi properti di bawah ini jika sudah ada di constructor
    // public string $baseURL;

    public string $baseURL;  // Pastikan URL ini benar

    public function __construct()
    {
        // Cek apakah .env memiliki pengaturan baseURL
        $envBaseURL = getenv('app.baseURL');
        if ($envBaseURL !== false) {
            $this->baseURL = rtrim($envBaseURL, '/') . '/';  // Gunakan baseURL dari .env jika ada
        } else {
            // Jika tidak ada di .env, tentukan baseURL berdasarkan protokol dan hostname
            $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
            $base_url .= $_SERVER['HTTP_HOST'] . '/';
            $this->baseURL = rtrim($base_url, '/') . '/';  // Pastikan diakhiri dengan '/'
        }
    }

    /**
     * Base Site URL (biasanya menggunakan URL dasar aplikasi dengan trailing slash)
     */

    public array $allowedHostnames = [];

    public string $indexPage = '';

    public string $uriProtocol = 'REQUEST_URI';

    public string $defaultLocale = 'en';

    public bool $negotiateLocale = false;

    public array $supportedLocales = ['en'];

    public string $appTimezone = 'UTC';

    public string $charset = 'UTF-8';

    public bool $forceGlobalSecureRequests = false;

    public array $proxyIPs = [];

    public bool $CSPEnabled = false;
}
