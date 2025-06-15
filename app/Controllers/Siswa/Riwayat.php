<?php

namespace App\Controllers\Siswa;

use App\Models\Riwayat_pembayaran_model;

class Riwayat extends BaseController
{
    public function index()
    {
        $id_siswa = session()->get('id_siswa'); // Ambil dari session login siswa

        $m_riwayat = new Riwayat_pembayaran_model();
        $riwayat = $m_riwayat->getRiwayatBySiswa($id_siswa);

        $data = [
            'title' => 'Riwayat Pembayaran',
			'description'   => 'Riwayat ',
            'riwayat' => $riwayat,
            'content' => 'siswa/riwayat/index'
        ];

        return view('siswa/layout/wrapper',$data);
    }
}
