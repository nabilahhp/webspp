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

    public function tambah()
    {
        $m_siswa = new Siswa_model();
        $siswa = $m_siswa->last_id();
        $urutan = ($siswa) ? $siswa->id_siswa + 1 : 1;

        // Validasi form
        $rules = [
            'nama_siswa' => 'required',
            'nis' => 'required',
            'jenis_kelamin' => 'required',
            'kategori' => 'required',
            'status_siswa' => 'required',
            'id_tahun' => 'required',
            'id_kelas' => 'required',
            'telepon' => 'required',
            'email' => 'required|valid_email',

            // Validasi untuk orang tua dan wali
            'nama_ayah' => 'permit_empty',
            'telepon_ayah' => 'permit_empty',
            'nama_ibu' => 'permit_empty',
            'telepon_ibu' => 'permit_empty',

            // Validasi untuk wali jika orang tua tidak diisi
            'nama_wali' => 'permit_empty',
            'telepon_wali' => 'permit_empty',
        ];

        // Pesan error yang lebih jelas
        $messages = [
            'required' => '{field} wajib diisi.',
            'valid_email' => 'Harap masukkan alamat email yang valid.',
            'permit_empty' => 'Kolom ini bisa dibiarkan kosong, tetapi jika orang tua tidak diisi, kolom wali harus diisi.',
            'required_without' => '{field} harus diisi jika {param} tidak diisi.',
        ];

        // Jika form disubmit dan validasi berhasil
        if ($this->request->getMethod() === 'post' && $this->validate($rules, $messages)) {
            // Proses password dan slug otomatis
            $hashedPassword = password_hash($this->request->getPost('nis'), PASSWORD_DEFAULT);
            $slug_siswa = $this->createSlug($this->request->getPost('nama_siswa'));

            // Memeriksa apakah orang tua dan wali kosong
            $nama_ayah = $this->request->getPost('nama_ayah');
            $telepon_ayah = $this->request->getPost('telepon_ayah');
            $nama_ibu = $this->request->getPost('nama_ibu');
            $telepon_ibu = $this->request->getPost('telepon_ibu');
            $nama_wali = $this->request->getPost('nama_wali');
            $telepon_wali = $this->request->getPost('telepon_wali');

            // Validasi kondisi orang tua dan wali
            if (empty($nama_ayah) && empty($nama_ibu) && empty($nama_wali)) {
                $this->session->setFlashdata('error', 'Jika nama ayah dan ibu kosong, maka nama wali wajib diisi.');
                return redirect()->back()->withInput();
            }

            if (empty($telepon_ayah) && empty($telepon_ibu) && empty($telepon_wali)) {
                $this->session->setFlashdata('error', 'Jika telepon ayah dan ibu kosong, maka telepon wali wajib diisi.');
                return redirect()->back()->withInput();
            }

            // Persiapkan data yang akan disimpan
            $data = [
                'nama_siswa' => $this->request->getPost('nama_siswa'),
                'slug_siswa' => $slug_siswa,
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
                'nama_wali' => $this->request->getPost('nama_wali'),
                'telepon_wali' => $this->request->getPost('telepon_wali'),
            ];

            // Menyimpan data siswa ke database
            if ($m_siswa->insert($data)) {
                $this->session->setFlashdata('sukses', 'Data siswa telah ditambah');
            } else {
                $this->session->setFlashdata('error', 'Terjadi kesalahan, data tidak bisa ditambahkan');
            }

            return redirect()->to(base_url('admin/siswa'));
        } else {
            // Jika form tidak valid, tampilkan kembali form dengan error
            $data = [
                'title' => 'Tambah Data Siswa',
                'siswa' => $siswa,
                'urutan' => $urutan,
                'content' => 'admin/siswa/tambah'
            ];
            echo view('admin/layout/wrapper', $data);
        }
    }

    public function edit($id_siswa)
    {
        $m_siswa = new Siswa_model();
        $siswa = $m_siswa->detail($id_siswa);

        $rules = [
            'nama_siswa' => 'required',
            'nis' => 'required',
            'jenis_kelamin' => 'required',
            'kategori' => 'required',
            'status_siswa' => 'required',
            'id_tahun' => 'required',
            'id_kelas' => 'required',
            'telepon' => 'required',
            'email' => 'required|valid_email',

            // Validasi gambar
            'gambar' => [
                'permit_empty',
                'ext_in[gambar,jpg,jpeg,gif,png,svg]',
                'max_size[gambar,4096]',
            ],

            // Validasi ortu dan wali sebagai input opsional
            'nama_ayah' => 'permit_empty',
            'telepon_ayah' => 'permit_empty',
            'nama_ibu' => 'permit_empty',
            'telepon_ibu' => 'permit_empty',
            'nama_wali' => 'permit_empty',
            'telepon_wali' => 'permit_empty',
        ];

        $messages = [
            'required' => '{field} wajib diisi.',
            'valid_email' => 'Format email tidak valid.',
            'ext_in' => 'Format gambar tidak didukung (hanya jpg, jpeg, png, gif, svg).',
            'max_size' => 'Ukuran gambar maksimal 4MB.',
        ];

        if ($this->request->getMethod() === 'post' && $this->validate($rules, $messages)) {
            // Ambil data input
            $nama_ayah = $this->request->getPost('nama_ayah');
            $telepon_ayah = $this->request->getPost('telepon_ayah');
            $nama_ibu = $this->request->getPost('nama_ibu');
            $telepon_ibu = $this->request->getPost('telepon_ibu');
            $nama_wali = $this->request->getPost('nama_wali');
            $telepon_wali = $this->request->getPost('telepon_wali');

            // Validasi logika ortu/wali
            if (empty($nama_ayah) && empty($nama_ibu) && empty($nama_wali)) {
                session()->setFlashdata('error', 'Jika nama ayah dan ibu kosong, maka nama wali wajib diisi.');
                return redirect()->back()->withInput();
            }

            if (empty($telepon_ayah) && empty($telepon_ibu) && empty($telepon_wali)) {
                session()->setFlashdata('error', 'Jika telepon ayah dan ibu kosong, maka telepon wali wajib diisi.');
                return redirect()->back()->withInput();
            }

            // Penanganan unggah gambar
            $gambar = $this->request->getFile('gambar');
            if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
                $namabaru = $gambar->getRandomName();
                $gambar->move(WRITEPATH . 'uploads', $namabaru);
            } else {
                $namabaru = $siswa->gambar;
            }

            // Slug otomatis
            $slug_siswa = $siswa->slug_siswa ?: $this->createSlug($this->request->getPost('nama_siswa'));

            // Data yang akan disimpan
            $data = [
                'nama_siswa' => $this->request->getPost('nama_siswa'),
                'slug_siswa' => $slug_siswa,
                'nis' => $this->request->getPost('nis'),
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'telepon' => $this->request->getPost('telepon'),
                'email' => $this->request->getPost('email'),
                'gambar' => $namabaru,
                'status_siswa' => $this->request->getPost('status_siswa'),
                'id_tahun' => $this->request->getPost('id_tahun'),
                'id_kelas' => $this->request->getPost('id_kelas'),
                'nama_ayah' => $nama_ayah,
                'telepon_ayah' => $telepon_ayah,
                'nama_ibu' => $nama_ibu,
                'telepon_ibu' => $telepon_ibu,
                'nama_wali' => $nama_wali,
                'telepon_wali' => $telepon_wali,
                'kategori' => $this->request->getPost('kategori'),
            ];

            if ($m_siswa->update($id_siswa, $data)) {
                session()->setFlashdata('sukses', 'Data siswa berhasil diperbarui.');
            } else {
                session()->setFlashdata('error', 'Terjadi kesalahan saat menyimpan data.');
            }

            return redirect()->to(base_url('admin/siswa'));
        } else {
            // Jika validasi gagal atau belum disubmit
            $data = [
                'title' => 'Edit Siswa: ' . $siswa->nama_siswa,
                'siswa' => $siswa,
                'content' => 'admin/siswa/edit'
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

            if ($ext == 'csv') {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Csv');
            } elseif ($ext == 'xls') {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xls');
            } else {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            }

            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filepath);
            $worksheet = $spreadsheet->getActiveSheet();

            $i = 1;
            $errors = [];
            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $cells = [];
                foreach ($cellIterator as $cell) {
                    $cells[] = $cell->getValue();
                }

                if ($i > 1) {
                    if (empty($cells[1])) { // Lewati jika NIS kosong
                        $i++;
                        continue;
                    }

                    // Ambil data ortu/wali
                    $nama_ayah = $cells[7];
                    $nama_ibu = $cells[8];
                    $nama_wali = $cells[9];
                    $telepon_wali = $cells[10];
                    $telepon_ayah = $cells[11];
                    $telepon_ibu = $cells[12];

                    // Validasi jika nama ayah & ibu kosong, maka nama wali wajib
                    if (empty($nama_ayah) && empty($nama_ibu) && empty($nama_wali)) {
                        $errors[] = "Baris ke-$i: Nama wali wajib diisi jika nama ayah dan ibu kosong.";
                    }

                    // Validasi jika telepon ayah & ibu kosong, maka telepon wali wajib
                    if (empty($telepon_ayah) && empty($telepon_ibu) && empty($telepon_wali)) {
                        $errors[] = "Baris ke-$i: Telepon wali wajib diisi jika telepon ayah dan ibu kosong.";
                    }

                    // Skip insert jika ada error
                    if (!empty($errors)) {
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
                        'nama_ayah' => $nama_ayah,
                        'nama_ibu' => $nama_ibu,
                        'nama_wali' => $nama_wali,
                        'telepon_wali' => $telepon_wali,
                        'telepon_ayah' => $telepon_ayah,
                        'telepon_ibu' => $telepon_ibu,
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

            if (!empty($errors)) {
                $this->session->setFlashdata('error', implode('<br>', $errors));
                return redirect()->back()->withInput();
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
