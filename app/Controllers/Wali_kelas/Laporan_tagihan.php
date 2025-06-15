<?php

namespace App\Controllers\Wali_kelas;

use App\Controllers\BaseController;
use App\Models\Tagihan_model;

// ✅ Letakkan fungsi helper di luar class
if (!function_exists('angka_bulan_indo')) {
    function angka_bulan_indo($nama_bulan)
    {
        $bulan = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        return $bulan[ucfirst(strtolower(trim($nama_bulan)))] ?? null;
    }
}

class Laporan_tagihan extends BaseController
{
    protected $tagihanModel;

    public function __construct()
    {
        $this->tagihanModel = new Tagihan_model();
    }

    public function index()
    {
        $pager = service('pager');
        $request = service('request');
        $keywords = $request->getGet('keywords');

        $perPage = 10;
        $page = (int) ($request->getGet('page') ?? 1);
        $offset = ($page - 1) * $perPage;

        $id_user = session('id_user');
        log_message('debug', 'Mulai Laporan_tagihan::index() - ID User: ' . $id_user);

        $staff = $this->db->table('staff')->where('id_user', $id_user)->get()->getRow();
        if (!$staff) {
            log_message('error', 'Data staff tidak ditemukan untuk ID user: ' . $id_user);
            return redirect()->back()->with('error', 'Data wali kelas tidak ditemukan.');
        }

        $id_kelas = $staff->id_kelas;
        $id_tahun = $staff->id_tahun;

        log_message('debug', 'ID Kelas dari wali kelas: ' . $id_kelas);
        log_message('debug', 'ID Tahun dari wali kelas: ' . $id_tahun);

        $builder = $this->tagihanModel
            ->select('tagihan.*, siswa.nama_siswa, input_tagihan.bulan_tagihan, input_tagihan.jumlah, kelas.nama_kelas, CONCAT(tahun.tahun_mulai, "/", tahun.tahun_selesai) AS tahun_ajaran')
            ->join('siswa', 'siswa.id_siswa = tagihan.id_siswa', 'left')
            ->join('kelas', 'kelas.id_kelas = siswa.id_kelas', 'left')
            ->join('tahun', 'tahun.id_tahun = siswa.id_tahun', 'left')
            ->join('input_tagihan', 'input_tagihan.id = tagihan.id_input_tagihan', 'left')
            ->where('siswa.id_kelas', $id_kelas)
            ->where('siswa.id_tahun', $id_tahun)
            ->where('tagihan.status', 'Telat Bayar');


        if ($keywords) {
            $builder->groupStart()
                ->like('siswa.nama_siswa', $keywords)
                ->orLike('tagihan.status', $keywords)
                ->groupEnd();
        }

        $allTagihan = $builder
            ->orderBy('tagihan.created_at', 'DESC')
            ->findAll();

        log_message('debug', 'Total tagihan hasil query: ' . count($allTagihan));

        $bulanSekarang = date('n');
        $filteredTagihan = $allTagihan;

        log_message('debug', 'Tagihan setelah filter bulan: ' . print_r($filteredTagihan, true));


        $total = count($filteredTagihan);
        $tagihan = array_slice($filteredTagihan, $offset, $perPage);

        $title = $keywords
            ? "Hasil pencarian tagihan telat: '$keywords' - $total ditemukan"
            : "Data Tagihan Telat Kelas {$staff->nama} ($total)";

        $pager_links = $pager->makeLinks($page, $perPage, $total, 'bootstrap_pagination');

        $data = [
            'title' => $title,
            'tagihan' => $tagihan,
            'pagination' => $pager_links,
            'i' => 1 + $offset,
            'content' => 'wali_kelas/laporan_siswa/index',
        ];

        return view('wali_kelas/layout/wrapper', $data);
    }
}
