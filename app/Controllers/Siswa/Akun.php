<?php

namespace App\Controllers\Siswa;

use CodeIgniter\Controller;

use App\Models\Siswa_model;

class Akun extends BaseController
{
	public function index()
	{
		// Ambil id_siswa dari session
		$id_siswa = session()->get('id_siswa');

		// Jika session id_siswa tidak ada, redirect ke login
		if (!$id_siswa) {
			return redirect()->to(base_url('signin'))->with('error', 'Silakan login terlebih dahulu');
		}

		// Ambil data akun siswa dari database
		$m_siswa = new Siswa_model();
		$akun = $m_siswa->detail($id_siswa);

		// Jika data akun tidak ditemukan
		if (!$akun) {
			return redirect()->to(base_url('signin'))->with('error', 'Data akun tidak ditemukan');
		}

		// Validasi form untuk update akun
		if ($this->request->getMethod() === 'post' && $this->validate([
			'nama_siswa' => 'required',
			'email' => 'required|valid_email',
			'telepon' => 'required',
			'password' => 'permit_empty|min_length[6]|max_length[32]',
		])) {
			$data = [
				'id_siswa'      => $id_siswa,
				'status_siswa'  => $akun->status_siswa,
				'nama_siswa'    => $this->request->getVar('nama_siswa'),
				'email'         => $this->request->getVar('email'),
				'username'      => $this->request->getVar('email'),
				'telepon'       => $this->request->getVar('telepon'),
				'password'      => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT),
				'nama_ayah'     => $this->request->getVar('nama_ayah') ?: $akun->nama_ayah,
				'telepon_ayah'  => $this->request->getVar('telepon_ayah') ?: $akun->telepon_ayah,
				'nama_ibu'      => $this->request->getVar('nama_ibu') ?: $akun->nama_ibu,
				'telepon_ibu'   => $this->request->getVar('telepon_ibu') ?: $akun->telepon_ibu,
				'tanggal_update' => date('Y-m-d H:i:s'),
			];

			$m_siswa->update($id_siswa, $data);
			return redirect()->to(base_url('siswa/akun'))->with('sukses', 'Akun berhasil diperbarui');
		}


		// Menampilkan halaman dengan data akun siswa
		$data = [
			'title'       => 'Data Akun Siswa',
			'description' => 'Halaman untuk melihat dan memperbarui data akun siswa',
			'keywords'    => 'Akun Siswa',
			'siswa'       => $akun,  // Menyertakan data siswa
			'content'     => 'siswa/akun/index'
		];

		return view('siswa/layout/wrapper', $data);  // Menampilkan halaman dengan layout
	}
}
