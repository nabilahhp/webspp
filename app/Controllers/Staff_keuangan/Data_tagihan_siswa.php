<?php

namespace App\Controllers\Staff_keuangan;

use App\Controllers\BaseController;
use App\Models\Tagihan_model;
use App\Models\Siswa_model;
use App\Models\Input_tagihan_model;

class Data_tagihan_siswa extends BaseController
{
    protected $tagihanModel;
    protected $siswaModel;
    protected $inputTagihanModel;
    protected $logger;

    public function __construct()
    {
        $this->tagihanModel = new Tagihan_model();
        $this->siswaModel = new Siswa_model();
        $this->inputTagihanModel = new Input_tagihan_model();
        $this->logger = \Config\Services::logger();
    }

    public function index()
    {
        $pager = service('pager');
        $request = service('request');
        $keywords = $request->getGet('keywords');
        $tahun_ajaran = $request->getGet('tahun_ajaran');
        $kelas = $request->getGet('kelas');

        $perPage = 10;
        $page = (int) ($request->getGet('page') ?? 1);
        $offset = ($page - 1) * $perPage;

        $builder = $this->tagihanModel
            ->select('tagihan.*, siswa.nama_siswa, input_tagihan.bulan_tagihan, input_tagihan.jumlah, kelas.nama_kelas, CONCAT(tahun.tahun_mulai, "/", tahun.tahun_selesai) AS tahun_ajaran')
            ->join('siswa', 'siswa.id_siswa = tagihan.id_siswa', 'left')
            ->join('kelas', 'kelas.id_kelas = siswa.id_kelas', 'left')
            ->join('tahun', 'tahun.id_tahun = siswa.id_tahun', 'left')
            ->join('input_tagihan', 'input_tagihan.id = tagihan.id_input_tagihan', 'left');

        // Filter berdasarkan tahun ajaran
        if ($tahun_ajaran) {
            $builder->where('CONCAT(tahun.tahun_mulai, "/", tahun.tahun_selesai)', $tahun_ajaran);
        }

        // Filter berdasarkan kelas
        if ($kelas) {
            $builder->where('kelas.nama_kelas', $kelas);
        }

        // Jika ada pencarian
        if ($keywords) {
            $builder->groupStart()
                ->like('siswa.nama', $keywords)
                ->orLike('tagihan.status', $keywords)
                ->groupEnd();

            $total = $builder->countAllResults(false);

            $tagihan = $builder
                ->orderBy('tagihan.created_at', 'DESC')
                ->findAll($perPage, $offset);

            $title = "Hasil pencarian: '$keywords' - $total ditemukan";
        } else {
            $total = $builder->countAllResults(false);

            $tagihan = $builder
                ->orderBy('tagihan.created_at', 'DESC')
                ->findAll($perPage, $offset);

            $title = "Data Tagihan Siswa ($total)";
        }

        $pager_links = $pager->makeLinks($page, $perPage, $total, 'bootstrap_pagination');

        $data = [
            'title' => $title,
            'tagihan' => $tagihan,
            'pagination' => $pager_links,
            'i' => 1 + $offset,
            'tahun_ajaran' => $this->inputTagihanModel->getTahunAjaranList(),  // Mengambil list tahun ajaran
            'kelas' => $this->inputTagihanModel->getKelasList(),  // Mengambil list kelas
            'content' => 'staff_keuangan/data_tagihan_siswa/index',
        ];

        return view('staff_keuangan/layout/wrapper', $data);
    }

    public function update_status()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        if (!$id || !$status) {
            return redirect()->back()->with('error', 'Data tidak lengkap untuk memperbarui status tagihan.');
        }

        $data = ['status' => $status];

        if ($status === 'Lunas') {
            $data['tanggal_bayar'] = date('Y-m-d H:i:s');
        } else {
            $data['tanggal_bayar'] = null;
        }

        try {
            $this->tagihanModel->update($id, $data);
            session()->setFlashdata('sukses', 'Status tagihan berhasil diperbarui.');
        } catch (\Exception $e) {
            $this->logger->error('Gagal update status tagihan: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat memperbarui status.');
        }

        return redirect()->to(base_url('staff_keuangan/data_tagihan_siswa'));
    }

    // Delete tagihan
    public function delete($id)
    {
        if ($this->tagihanModel->delete($id)) {
            session()->setFlashdata('sukses', 'Tagihan berhasil dihapus.');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus tagihan.');
        }
        return redirect()->to(base_url('staff_keuangan/input_tagihan'));
    }
}
