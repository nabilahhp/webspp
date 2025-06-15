<?php

namespace App\Libraries;

use App\Models\User_model;
use App\Models\Client_model;
use App\Models\Siswa_model;
use App\Models\Akun_model;

class Simple_login
{
	// check login
	public function login($username, $password, $pengalihan)
	{
		$this->session = \Config\Services::session();
		$m_user = new User_model();
		$user = $m_user->login($username, $password);

		if ($user) {
			// Cari id_staff berdasarkan id_user dari staff
			$m_staff = new \App\Models\Staff_model();
			$staff = $m_staff->where('id_user', $user->id_user)->first();

			$this->session->set('username', $username);
			$this->session->set('id_user', $user->id_user);
			$this->session->set('id_staff', $user->id_staff);
			$this->session->set('nama', $user->nama);
			$this->session->set('akses_level', $user->akses_level);

			if (!empty($pengalihan)) {
				return redirect()->to($pengalihan);
			} else {
				$level = strtolower($user->akses_level);
				\log_message('debug', 'Akses level: ' . $level);

				if ($level === 'admin') {
					return redirect()->to(base_url('admin/dasbor'));
				} elseif ($level === 'keuangan') {
					return redirect()->to(base_url('staff_keuangan/dasbor'));
				} elseif ($level === 'walikelas') {
					return redirect()->to(base_url('wali_kelas/dasbor'));
				} else {
					return redirect()->to(base_url('login'));
				}
			}
		} else {
			$this->session->setFlashdata('warning', 'Username atau password salah');
			return redirect()->to(base_url('login'));
		}
	}




	// check login
	public function login_siswa($username, $password)
	{
		$this->session  = \Config\Services::session();
		$uri            = service('uri');
		$m_siswa        = new Siswa_model();
		$m_akun         = new Akun_model();

		// Cari user berdasarkan username
		$user = $m_akun->login($username); // Cari data user berdasarkan username

		// Jika user ditemukan, lakukan pengecekan password
		if ($user) {
			// Verifikasi password menggunakan password_verify
			if (password_verify($password, $user->password)) {  // Gunakan password_verify untuk memeriksa password
				// Jika password cocok, set session
				$this->session->set('username_siswa', $username);
				$this->session->set('id_akun', $user->id_akun);
				$this->session->set('nama_siswa', $user->nama);
				$this->session->set('jenis_akun', $user->jenis_akun);
				$this->session->set('nis', $user->nis);
				$this->session->set('nisn', $user->nisn);
				session_write_close(); // ✅ wajib sebelum redirect
				return redirect()->to(base_url('siswa/dasbor')); // Redirect ke dasbor
			} else {
				// Jika password tidak cocok
				$this->session->setFlashdata('warning', 'Username atau password salah');
				return redirect()->to(base_url('signin'));
			}
		} else {
			// Jika username tidak ditemukan
			$this->session->setFlashdata('warning', 'Username atau password salah');
			return redirect()->to(base_url('signin'));
		}
	}


	// check login
	public function checklogin_siswa()
	{
		$this->session  = \Config\Services::session();
		if ($this->session->get('username_siswa') == '') {
			$pengalihan = str_replace('index.php/', '', current_url());
			$this->session->set('pengalihan', $pengalihan);
			$this->session->setFlashdata('warning', 'Anda belum login');
			header("Location: " . base_url('signin')) . '?redirect=' . $pengalihan;
			exit;
		}
	}

	// check login
	public function login_client($username, $password)
	{
		$this->session  = \Config\Services::session();
		$uri            = service('uri');
		$m_client 		= new Client_model();
		$user 			= $m_client->login($username, $password);
		if ($user) {
			// Jika username password benar
			$this->session->set('username_client', $username);
			$this->session->set('id_client', $user->id_client);
			$this->session->set('nama_client', $user->nama);
			$this->session->set('akses_level', 'Client');
			header("Location: client/dasbor");
			exit;
		} else {
			// jika username password salah
			$this->session->setFlashdata('warning', 'Username atau password salah');
			return redirect()->to(base_url('signin'));
		}
	}

	// check login
	public function checklogin()
	{
		$this->session  = \Config\Services::session();
		if ($this->session->get('username') == '') {
			$pengalihan = str_replace('index.php/', '', current_url());
			$this->session->set('pengalihan', $pengalihan);
			$this->session->setFlashdata('warning', 'Anda belum login');
			header("Location: " . base_url('login')) . '?redirect=' . $pengalihan;
			exit;
		}
	}

	// check login
	public function checklogin_client()
	{
		$this->session  = \Config\Services::session();
		if ($this->session->get('username_client') == '') {
			$pengalihan = str_replace('index.php/', '', current_url());
			$this->session->set('pengalihan', $pengalihan);
			$this->session->setFlashdata('warning', 'Anda belum login');
			header("Location: " . base_url('signin')) . '?redirect=' . $pengalihan;
			exit;
		}
	}

	// check logout
	public function logout()
	{
		$this->session  = \Config\Services::session();
		$this->session->remove('username');
		$this->session->remove('id_user');
		$this->session->remove('akses_level');
		$this->session->remove('nama');
		$this->session->setFlashdata('sukses', 'Anda berhasil logout');
		header("Location: " . base_url('login?logout=sukses'));
		exit;
	}

	// logout_siswa
	public function logout_siswa()
	{
		$this->session  = \Config\Services::session();
		$this->session->remove('username_siswa');
		$this->session->remove('id_akun');
		$this->session->remove('jenis_akun');
		$this->session->remove('nama_siswa');
		$this->session->remove('nis');
		$this->session->remove('nisn');
		$this->session->setFlashdata('sukses', 'Anda berhasil logout');
		header("Location: " . base_url('signin?logout=sukses'));
		exit;
	}
}
