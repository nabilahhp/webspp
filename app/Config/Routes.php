<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setAutoRoute(true);
$routes->get('/', 'Signin::index');
$routes->get('admin/akun_pendaftar/delete/(:num)', 'Admin\Akun_pendaftar::delete/$1');
$routes->get('admin/akun_pendaftar/edit/(:num)', 'Admin\AkunPendaftarController::edit/$1');
$routes->get('/signin', 'Signin::index');  // Rute untuk halaman login
$routes->post('/signin', 'Signin::index'); // Rute untuk mengirimkan form login
$routes->get('/dasbor', 'Siswa\Dasbor::index');  // Rute untuk halaman dasbor siswa
$routes->get('/siswa/riwayat', 'Siswa\Riwayat::index');


// Rute untuk halaman akun siswa, menggunakan controller Akun di folder Siswa
$routes->get('/akun', 'Siswa\Akun::index');  // Menampilkan halaman akun siswa
$routes->post('/akun/update', 'Siswa\Akun::update');  // Update akun siswa
$routes->post('siswa/payment/create', 'Siswa\Payment::create');
$routes->get('siswa/payment/create', 'Siswa\Payment::create');

$routes->get('signin/resetPassword/(:any)', 'Signin::resetPassword/$1');
$routes->post('signin/sendResetPasswordEmail', 'Signin::sendResetPasswordEmail');


$routes->post('staff_keuangan/input_tagihan/tambah', 'Staff_keuangan\Input_tagihan::tambah');
$routes->get('admin/dasbor', 'Admin\Dasbor::index');
$routes->get('staff_keuangan/riwayat_tagihan/bukti/(:num)', 'Staff_keuangan\Riwayat_tagihan::bukti/$1');

$routes->get('checker', 'Checker::index');
$routes->post('checker/check', 'Checker::check');
$routes->get('staff_keuangan/pengingat', 'Staff_keuangan\Pengingat::kirimPengingatTagihan');

$routes->post('payment/notification', 'Siswa\Payment::paymentNotification');
