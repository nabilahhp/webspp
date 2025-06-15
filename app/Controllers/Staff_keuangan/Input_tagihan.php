<?php


namespace App\Controllers\Staff_keuangan;

use App\Models\Input_tagihan_model;
use App\Models\Tagihan_model;
use App\Models\Siswa_model;
use App\Models\User_model;
use App\Models\Kelas_model;
use App\Models\Riwayat_pembayaran_model;
use App\Models\Tahun_model;
use App\Controllers\BaseController;

class Input_tagihan extends BaseController
{
    protected $tagihanModel;
    protected $tagihanSiswaModel;
    protected $siswaModel;
    protected $userModel;
    protected $kelasModel;
    protected $tahunModel;
    protected $riwayatModel;
    protected $logger;

    public function __construct()
    {
        $this->tagihanModel = new Input_tagihan_model();
        $this->tagihanSiswaModel = new Tagihan_model();
        $this->siswaModel = new Siswa_model();
        $this->userModel = new User_model();
        $this->kelasModel = new Kelas_model();
        $this->tahunModel = new Tahun_model();
        $this->riwayatModel = new Riwayat_pembayaran_model();
        $this->logger = \Config\Services::logger();
    }

    public function index()
    {
        $pager = service('pager');
        $request = service('request');
        $keywords = $request->getGet('keywords');

        $perPage = 10;
        $page = (int) ($request->getGet('page') ?? 1);
        $offset = ($page - 1) * $perPage;

        if ($keywords) {
            $total = $this->tagihanModel->like('detail', $keywords)->countAllResults();
            $tagihan = $this->tagihanModel
                ->select('input_tagihan.*, users.username as nama_staff')
                ->like('detail', $keywords)
                ->orderBy('input_tagihan.created_at', 'DESC')
                ->findAll($perPage, $offset);
            $title = "Hasil pencarian: '$keywords' - $total ditemukan";
        } else {
            $total = $this->tagihanModel->countAllResults();
            $tagihan = $this->tagihanModel->getAllWithUser($perPage, $offset);
            $title = "Daftar Tagihan ($total)";
        }

        $i = 1 + $offset;
        $pager_links = $pager->makeLinks($page, $perPage, $total, 'bootstrap_pagination');

        // Ambil data kelas dan tahun untuk dropdown
        $kelasList = $this->kelasModel->findAll();
        $tahunList = $this->tahunModel->findAll();

        $data = [
            'title' => $title,
            'tagihan' => $tagihan,
            'pagination' => $pager_links,
            'i' => $i,
            'kelasList' => $kelasList,
            'tahunList' => $tahunList,
            'content' => 'staff_keuangan/input_tagihan/index',
        ];

        return view('staff_keuangan/layout/wrapper', $data);
    }


    public function tambah()
    {
        $this->logger->debug('Memulai proses tambah tagihan');

        if ($this->request->getMethod() === 'post') {
            // Ambil ID staff yang login dari session
            $id_staff = session()->get('id_user');
            $this->logger->debug('ID staff yang login: ' . $id_staff);

            // Ambil data dari form
            $bulan_tagihan_input = $this->request->getPost('bulan_tagihan');
            $jumlah = $this->request->getPost('jumlah');
            $detail = $this->request->getPost('detail');
            $id_kelas = $this->request->getPost('id_kelas');
            $id_tahun = $this->request->getPost('id_tahun');

            // Validasi sederhana
            if (empty($bulan_tagihan_input) || empty($jumlah) || empty($detail) || empty($id_kelas) || empty($id_tahun)) {
                $this->logger->error('Validasi gagal: ada data kosong');
                return redirect()->back()->withInput()->with('error', 'Semua field harus diisi.');
            }

            // Konversi "2025-05" jadi "Mei 2025"
            $bulan_tagihan_full = $bulan_tagihan_input . '-01';
            $timestamp = strtotime($bulan_tagihan_full);

            $bulan = [
                1 => 'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember'
            ];

            $bulan_num = (int)date('m', $timestamp);
            $tahun = date('Y', $timestamp);
            $bulan_tagihan_nama = $bulan[$bulan_num] . ' ' . $tahun;

            $this->logger->debug('Data yang diterima:', [
                'bulan_tagihan' => $bulan_tagihan_nama,
                'jumlah' => $jumlah,
                'detail' => $detail,
                'id_kelas' => $id_kelas,
                'id_tahun' => $id_tahun,
            ]);

            // Siapkan data untuk insert input tagihan
            $dataInputTagihan = [
                'id_staff' => $id_staff,
                'bulan_tagihan' => $bulan_tagihan_nama,
                'jumlah' => $jumlah,
                'detail' => $detail,
                'id_kelas' => $id_kelas,
                'id_tahun' => $id_tahun,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            try {
                // Insert ke tabel input tagihan
                $this->tagihanModel->insert($dataInputTagihan);
                $insertId = $this->tagihanModel->getInsertID();
                $this->logger->debug('Tagihan input berhasil disimpan dengan ID: ' . $insertId);

                // Ambil semua siswa yang sesuai kelas dan tahun untuk generate tagihan siswa
                $siswaAll = $this->siswaModel
                    ->where('id_kelas', $id_kelas)
                    ->where('id_tahun', $id_tahun)
                    ->findAll();

                $this->logger->debug('Jumlah siswa ditemukan: ' . count($siswaAll));

                foreach ($siswaAll as $siswa) {
                    // Tentukan status berdasarkan kategori
                    $kategori = strtolower(trim($siswa['kategori']));
                    $this->logger->debug('Kategori siswa: ' . $kategori . ' | Nama: ' . $siswa['nama_siswa']);
                    $status = ($kategori === 'beasiswa') ? 'Lunas' : 'Belum Bayar';

                    $dataTagihanSiswa = [
                        'id_siswa' => $siswa['id_siswa'],
                        'id_input_tagihan' => $insertId,
                        'status' => $status,
                        'tanggal_bayar' => ($status === 'Lunas') ? date('Y-m-d') : null,
                        'created_at' => date('Y-m-d H:i:s'),
                    ];

                    $idTagihanSiswa = $this->tagihanSiswaModel->insert($dataTagihanSiswa);

                    // Jika beasiswa, insert riwayat pembayaran dengan jumlah_bayar 0
                    if ($kategori === 'beasiswa') {
                        $dataRiwayatPembayaran = [
                            'id_siswa' => $siswa['id_siswa'],
                            'id_tagihan' => $idTagihanSiswa,
                            'tanggal_bayar' => date('Y-m-d'),
                            'jumlah_bayar' => 0,
                            'id_order' => null,
                            'created_at' => date('Y-m-d H:i:s'),
                        ];
                        $this->riwayatModel->insert($dataRiwayatPembayaran);
                    }
                }

                $this->logger->debug('Tagihan siswa berhasil digenerate ke semua siswa pada kelas dan tahun tersebut');

                return redirect()->to(base_url('staff_keuangan/input_tagihan'))->with('success', 'Tagihan berhasil dibuat dan didistribusikan ke semua siswa.');
                $this->session->setFlashdata('sukses', 'Data Tagihan telah dibuat');
            } catch (\Exception $e) {
                $this->logger->error('Gagal menyimpan tagihan: ' . $e->getMessage());
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data tagihan.');
            }
        }

        // Jika bukan post, tampilkan form tambah tagihan (bisa redirect atau load view form)
    }



    // Edit tagihan
    public function edit($id)
    {
        $tagihan = $this->tagihanModel->find($id);
        if (!$tagihan) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Tagihan dengan ID $id tidak ditemukan");
        }

        $inputTagihanList = $this->tagihanModel->findAll();
        $users = $this->userModel->findAll();

        if ($this->request->getMethod() === 'post' && $this->validate([
            'id_input_tagihan' => 'required|integer',
            'status_tagihan' => 'required',
        ])) {
            $data = [
                'id_input_tagihan' => $this->request->getPost('id_input_tagihan'),
                'status_tagihan' => $this->request->getPost('status_tagihan'),
                'catatan' => $this->request->getPost('catatan'),
            ];

            if ($this->tagihanModel->update($id, $data)) {
                session()->setFlashdata('sukses', 'Tagihan berhasil diupdate.');
                return redirect()->to(base_url('admin/input_tagihan'));
            } else {
                session()->setFlashdata('error', 'Gagal mengupdate tagihan.');
            }
        }

        $data = [
            'title' => 'Edit Tagihan',
            'tagihan' => $tagihan,
            'input_tagihan_list' => $inputTagihanList,
            'users' => $users,
            'content' => 'admin/siswa/edit',
        ];
        echo view('admin/layout/wrapper', $data);
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
