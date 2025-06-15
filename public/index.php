<?php
// Cek versi PHP
$minPhpVersion = '7.4';
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    exit("PHP version must be {$minPhpVersion} or higher. Current: " . PHP_VERSION);
}

// Path front controller
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Pastikan current directory sesuai
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

// Load konfigurasi paths
require FCPATH . '../app/Config/Paths.php';

$paths = new Config\Paths();

// Load bootstrap framework
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

// Load environment variables
require_once SYSTEMPATH . 'Config/DotEnv.php';
(new CodeIgniter\Config\DotEnv(ROOTPATH))->load();

if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', env('CI_ENVIRONMENT', 'production'));
}

// Jalankan aplikasi
$app = Config\Services::codeigniter();
$app->initialize();
$app->setContext(is_cli() ? 'php-cli' : 'web');
$app->run();

exit(EXIT_SUCCESS);
