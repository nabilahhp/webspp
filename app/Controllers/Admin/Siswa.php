<?php

namespace App\Controllers\Admin;


use App\Models\Siswa_model;
use App\Models\Rombel_model;
use App\Models\Kelas_model;
use App\Models\Tahun_model;



use App\Models\Siswa_rombel_model;


class Siswa extends BaseController
{
    // Fungsi untuk membuat slug otomatis
    public function createSlug($name)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name))); // Mengubah nama menjadi slug
        return $slug;
    }

    // Main page
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

    public function tambah()
    {
        $m_siswa = new Siswa_model();
        $siswa = $m_siswa->last_id();
        $urutan = ($siswa) ? $siswa->id_siswa + 1 : 1;

        if ($this->request->getMethod() === 'post' && $this->validate([
            'nama_siswa' => 'required',
            'gambar' => [
                'ext_in[gambar,jpg,jpeg,gif,png,svg]',
                'max_size[gambar,4096]',
            ],
        ])) {
            // Penanganan unggah gambar
            $gambar = $this->request->getFile('gambar');
            if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
                $namabaru = $gambar->getRandomName();
                $gambar->move(WRITEPATH . 'uploads', $namabaru); // Simpan file di folder "uploads"
            } else {
                $namabaru = ''; // Nilai default jika tidak ada gambar yang diunggah
            }
            $hashedPassword = password_hash($this->request->getPost('nis'), PASSWORD_DEFAULT);
            // Membuat slug otomatis
            $slug_siswa = $this->createSlug($this->request->getPost('nama_siswa'));

            // Menyiapkan data untuk disimpan ke database
            $data = [
                'nama_siswa' => $this->request->getPost('nama_siswa'),
                'slug_siswa' => $slug_siswa, // Menambahkan slug_siswa
                'nis' => $this->request->getPost('nis'),
                'password' => $hashedPassword,
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

    public function import()
    {
        $m_siswa = new Siswa_model();
        $m_kelas = new Kelas_model();
        $m_tahun = new Tahun_model();


        $kelas = $m_kelas->listing();
        $tahun = $m_tahun->listing();

        if ($this->request->getMethod() === 'post' && $this->validate([
            'ID_USER' => 'required',
            'id_kelas' => 'required',
            'id_tahun' => 'required',
            'file_excel' => [
                'ext_in[file_excel,xlsx,xls,csv]',
                'max_size[file_excel,4096]',
            ],
        ])) {
            $id_kelas = $this->request->getPost('id_kelas');
            $id_tahun = $this->request->getPost('id_tahun');

            $file = $this->request->getFile('file_excel');
            $filename = $file->getRandomName();
            $file->move(WRITEPATH . '../assets/upload/file/', $filename);

            $filepath = WRITEPATH . '../assets/upload/file/' . $filename;
            $ext = $file->getClientExtension();

            // Pilih reader berdasarkan ekstensi file
            if ($ext == 'csv') {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Csv');
            } elseif ($ext == 'xls') {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xls');
            } else { // xlsx
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            }

            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filepath);
            $worksheet = $spreadsheet->getActiveSheet();

            $i = 1;
            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $cells = [];
                foreach ($cellIterator as $cell) {
                    $cells[] = $cell->getValue();
                }

                if ($i > 1) { // Lewati header
                    if (empty($cells[1])) {
                        $i++;
                        continue;
                    }

                    $data = [
                        'id_tahun' => $id_tahun,
                        'id_kelas' => $id_kelas,
                        'kode_siswa' => null,
                        'slug_siswa' => null,
                        'kategori' => $cells[0],
                        'nis' => $cells[1],
                        'nama_siswa' => $cells[2],
                        'telepon' => $cells[3],
                        'email' => $cells[4],
                        'password' => password_hash($cells[1], PASSWORD_DEFAULT),
                        'password_hint' => null,
                        'jenis_kelamin' => $cells[5],
                        'isi' => $cells[6],
                        'nama_ayah' => $cells[7],
                        'nama_ibu' => $cells[8],
                        'telepon_ayah' => $cells[9],
                        'telepon_ibu' => $cells[10],
                        'kelompok' => null,
                        'gambar' => null,
                        'status_siswa' => 'Aktif',
                        'tanggal_baca' => null,
                        'tanggal_post' => date('Y-m-d H:i:s'),
                        'tanggal' => date('Y-m-d H:i:s'),
                    ];

                    $m_siswa->insert($data);
                }
                $i++;
            }

            $this->session->setFlashdata('sukses', 'Data siswa berhasil diimpor.');
            return redirect()->to(base_url('admin/siswa'));
        } else {
            $data = [
                'title' => 'Import Data Siswa',
                'kelas' => $kelas,
                'tahun' => $tahun,
                'content' => 'admin/siswa/import'
            ];
            echo view('admin/layout/wrapper', $data);
        }
    }

    // Edit
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
    public function proses()
    {
        $m_siswa = new Siswa_model();
        $request = service('request');

        $submit = $request->getPost('submit');
        $id_siswa = $request->getPost('id_siswa');

        if (empty($id_siswa)) {
            $this->session->setFlashdata('warning', 'Tidak ada siswa yang dipilih.');
            return redirect()->to(base_url('admin/siswa'));
        }

        if ($submit === 'delete') {
            // Proses hapus banyak siswa
            foreach ($id_siswa as $id) {
                $m_siswa->delete($id);
            }
            $this->session->setFlashdata('sukses', 'Beberapa data siswa berhasil dihapus.');
        } elseif ($submit === 'update') {
            // Update status siswa
            $status = $request->getPost('status_siswa');
            foreach ($id_siswa as $id) {
                $m_siswa->update($id, ['status_siswa' => $status]);
            }
            $this->session->setFlashdata('sukses', 'Status siswa berhasil diperbarui.');
        }

        return redirect()->to(base_url('admin/siswa'));
    }


    // Delete
    public function delete($id_siswa)
    {
        $m_siswa = new Siswa_model();
        $data = ['id_siswa' => $id_siswa];
        $m_siswa->delete($data);
        $this->session->setFlashdata('sukses', 'Data telah dihapus');
        return redirect()->to(base_url('admin/siswa'));
    }
}
