<?php

namespace App\Controllers\Admin;

use App\Models\Siswa_model;
use App\Models\Kelas_model;
use App\Models\Tahun_model;
use CodeIgniter\Controller;

class Siswa extends BaseController
{
    // Fungsi untuk membuat slug otomatis
    public function createSlug($name)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name))); // Mengubah nama menjadi slug
        return $slug;
    }

    // Halaman utama untuk menampilkan data siswa
    public function index()
    {
        $m_siswa = new Siswa_model();
        $pager = service('pager');
        $keywords = $this->request->getVar('keywords');

        if (isset($keywords)) {
            $totalnya = $m_siswa->total_cari($keywords);
            $title = 'Hasil pencarian: ' . $keywords . ' - ' . $totalnya->total . ' ditemukan';
            $page = (int) ($this->request->getGet('page') ?? 1);
            $perPage = 10;
            $total = $totalnya->total;
            $pager_links = $pager->makeLinks($page, $perPage, $total, 'bootstrap_pagination');
            $page = ($this->request->getGet('page')) ? ($this->request->getGet('page') - 1) * $perPage : 0;
            $siswa = $m_siswa->paginasi_cari($keywords, $perPage, $page);
        } else {
            $totalnya = $m_siswa->total();
            $title = 'Data Master Siswa (' . $totalnya->total . ')';
            $page = (int) ($this->request->getGet('page') ?? 1);
            $perPage = 10;
            $total = $totalnya->total;
            $pager_links = $pager->makeLinks($page, $perPage, $total, 'bootstrap_pagination');
            $page = ($this->request->getGet('page')) ? ($this->request->getGet('page') - 1) * $perPage : 0;
            $siswa = $m_siswa->paginasi($perPage, $page);
        }

        $data = [
            'title' => $title,
            'siswa' => $siswa,
            'pagination' => $pager_links,
            'content' => 'admin/siswa/index'
        ];

        echo view('admin/layout/wrapper', $data);
    }

    // Fungsi untuk tambah data siswa
    public function tambah()
    {
        $m_siswa = new Siswa_model();
        $siswa = $m_siswa->last_id();
        $urutan = ($siswa) ? $siswa->id_siswa + 1 : 1;

        if ($this->request->getMethod() === 'post' && $this->validate([
            'nama_siswa' => 'required',
        ])) {
            $hashedPassword = password_hash($this->request->getPost('nis'), PASSWORD_DEFAULT);
            // Membuat slug otomatis
            $slug_siswa = $this->createSlug($this->request->getPost('nama_siswa'));

            // Menangkap data jenis pembiayaan
            $jenis_pembiayaan = $this->request->getPost('jenis_pembiayaan');

            // Menyiapkan data untuk disimpan ke database
            $data = [
                'nama_siswa' => $this->request->getPost('nama_siswa'),
                'slug_siswa' => $slug_siswa, // Menambahkan slug_siswa
                'nis' => $this->request->getPost('nis'),
                'password' => $hashedPassword,
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'kategori' => $this->request->getPost('kategori'),
                'telepon' => $this->request->getPost('telepon'),
                'email' => $this->request->getPost('email'),
                'status_siswa' => $this->request->getPost('status_siswa'),
                'id_tahun' => $this->request->getPost('id_tahun'),
                'id_kelas' => $this->request->getPost('id_kelas'),
                'nama_ayah' => $this->request->getPost('nama_ayah'),
                'telepon_ayah' => $this->request->getPost('telepon_ayah'),
                'nama_ibu' => $this->request->getPost('nama_ibu'),
                'telepon_ibu' => $this->request->getPost('telepon_ibu'),
            ];

            // Menyimpan data ke database
            if ($m_siswa->insert($data)) {
                $this->session->setFlashdata('sukses', 'Data siswa telah ditambah');
            } else {
                $this->session->setFlashdata('error', 'Terjadi kesalahan, data tidak bisa ditambahkan');
            }

            return redirect()->to(base_url('admin/siswa'));
        } else {
            $data = [
                'title' => 'Tambah Data Siswa',
                'siswa' => $siswa,
                'urutan' => $urutan,
                'content' => 'admin/siswa/tambah'
            ];
            echo view('admin/layout/wrapper', $data);
        }
    }


    // Fungsi untuk edit data siswa
    public function edit($id_siswa)
    {
        $m_siswa = new Siswa_model();
        $siswa = $m_siswa->detail($id_siswa);

        if ($this->request->getMethod() === 'post' && $this->validate([
            'nama_siswa' => 'required',
            'gambar' => [
                'ext_in[gambar,jpg,jpeg,gif,png,svg]',
                'max_size[gambar,4096]',
            ],
        ])) {
            // Penanganan unggah gambar (update)
            $gambar = $this->request->getFile('gambar');
            if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
                $namabaru = $gambar->getRandomName();
                $gambar->move(WRITEPATH . 'uploads', $namabaru); // Simpan file di folder "uploads"
            } else {
                // Jika tidak ada gambar baru yang diunggah, gunakan gambar lama
                $namabaru = $siswa->gambar; // Ambil gambar lama jika tidak ada gambar baru
            }

            // Membuat slug otomatis jika slug kosong
            $slug_siswa = $siswa->slug_siswa;
            if (empty($slug_siswa)) {
                $slug_siswa = $this->createSlug($this->request->getPost('nama_siswa')); // Jika slug kosong, buat slug
            }

            // Menangkap data jenis pembiayaan
            $jenis_pembiayaan = $this->request->getPost('jenis_pembiayaan');

            // Menyimpan perubahan data
            $data = [
                'nama_siswa' => $this->request->getPost('nama_siswa'),
                'slug_siswa' => $slug_siswa, // Masukkan slug_siswa
                'nis' => $this->request->getPost('nis'),
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'telepon' => $this->request->getPost('telepon'),
                'email' => $this->request->getPost('email'),
                'gambar' => $namabaru, // Simpan nama gambar
                'status_siswa' => $this->request->getPost('status_siswa'),
                'id_tahun' => $this->request->getPost('id_tahun'),
                'id_kelas' => $this->request->getPost('id_kelas'),
                'nama_ayah' => $this->request->getPost('nama_ayah'),
                'telepon_ayah' => $this->request->getPost('telepon_ayah'),
                'nama_ibu' => $this->request->getPost('nama_ibu'),
                'telepon_ibu' => $this->request->getPost('telepon_ibu'),
                'kategori' => $jenis_pembiayaan,  // Menyimpan jenis pembiayaan
            ];

            // Menyimpan data ke database
            if ($m_siswa->update($id_siswa, $data)) {
                $this->session->setFlashdata('sukses', 'Data telah disimpan');
            } else {
                $this->session->setFlashdata('error', 'Terjadi kesalahan, data tidak bisa disimpan');
            }

            return redirect()->to(base_url('admin/siswa'));
        } else {
            // Kirim data ke view untuk menampilkan form edit dengan data yang sudah ada
            $data = [
                'title' => 'Edit Siswa: ' . $siswa->nama_siswa,
                'siswa' => $siswa,
                'content' => 'admin/siswa/edit'
            ];
            echo view('admin/layout/wrapper', $data);
        }
    }
}
