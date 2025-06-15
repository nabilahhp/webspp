<?php

namespace App\Controllers\Siswa;

use CodeIgniter\Controller;

class Dasbor extends Controller
{
    public function index()
    {
        // Mengambil session
        $session = \Config\Services::session();

        // Jika session siswa tidak ada, arahkan ke halaman login
        if (!$session->get('username_siswa')) {
            return redirect()->to(base_url('signin'));
        }

        // Mengirimkan data ke tampilan
        $data = [
            'title' => 'Dasbor Siswa',  // Judul halaman dashboard
            'description' => 'Halaman Dasbor Siswa',  // Deskripsi halaman
            'content' => 'siswa/dasbor/index', // Konten halaman dashboard
            'website' => 'SMAMUGA'
        ];

        // Menampilkan tampilan dengan data yang diteruskan
        return view('siswa/layout/wrapper', $data);
    }
}
