<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use App\Models\User_model;
use App\Models\Staff_model;
use App\Models\Kategori_staff_model;

class Akun extends BaseController
{
	public function index()
	{
		$m_user            = new User_model();
		$m_staff           = new Staff_model();
		$m_kategori_staff  = new Kategori_staff_model();
		$id_user           = $this->session->get('id_user');
		$user              = $m_user->detail($id_user);
		$kategori_staff    = $m_kategori_staff->listing();

		// Ambil staff berdasarkan id_user
		$staff = $m_staff->where('id_user', $id_user)->first();
		$id_staff = $staff['id_staff'] ?? null;

		// Jika $staff masih array, ubah ke object agar konsisten
		if (is_array($staff)) {
			$staff = (object) $staff;
		}

		if ($this->request->getMethod() === 'post' && $this->validate(['nama' => 'required'])) {

			// UPDATE USER
			if (isset($_POST['user'])) {
				if (!empty($_FILES['gambar']['name'])) {
					$avatar = $this->request->getFile('gambar');
					$nama_baru = $avatar->getRandomName();
					$avatar->move(WRITEPATH . '../assets/upload/image/', $nama_baru);

					\Config\Services::image()
						->withFile(WRITEPATH . '../assets/upload/image/' . $nama_baru)
						->fit(100, 100, 'center')
						->save(WRITEPATH . '../assets/upload/image/thumbs/' . $nama_baru);

					$data = [
						'id_user' => $id_user,
						'nama' => $this->request->getPost('nama'),
						'email' => $this->request->getPost('email'),
						'gambar' => $nama_baru
					];
				} else {
					$data = [
						'id_user' => $id_user,
						'nama' => $this->request->getPost('nama'),
						'email' => $this->request->getPost('email')
					];
				}

				$m_user->edit($data);
				$this->session->setFlashdata('sukses', 'Data telah diupdate');
				return redirect()->to(base_url('admin/akun#user'));
			}

			// UPDATE PASSWORD
			if (isset($_POST['pwd'])) {
				$password = $this->request->getPost('password');
				$konfirmasi = $this->request->getPost('konfirmasi_password');

				if (strlen($password) < 6 || strlen($password) > 32) {
					$this->session->setFlashdata('warning', 'Password minimal 6 dan maksimal 32 karakter');
					return redirect()->to(base_url('admin/akun#pwd'));
				} elseif ($password != $konfirmasi) {
					$this->session->setFlashdata('warning', 'Password tidak sama');
					return redirect()->to(base_url('admin/akun#pwd'));
				}

				$m_user->edit([
					'id_user' => $id_user,
					'password' => sha1($password)
				]);

				$this->session->setFlashdata('sukses', 'Password telah diupdate');
				return redirect()->to(base_url('admin/akun#pwd'));
			}

			// UPDATE STAFF
			if (isset($_POST['staff'])) {
				$data = []; // <<< INI WAJIB ditambahkan

				if (!empty($_FILES['gambar']['name'])) {
					$avatar = $this->request->getFile('gambar');
					$nama_baru = $avatar->getRandomName();
					$avatar->move(WRITEPATH . '../assets/upload/image/', $nama_baru);

					\Config\Services::image()
						->withFile(WRITEPATH . '../assets/upload/image/' . $nama_baru)
						->fit(100, 100, 'center')
						->save(WRITEPATH . '../assets/upload/image/thumbs/' . $nama_baru);

					$data['gambar'] = $nama_baru;
				}

				$data += [
					'id_staff' => $id_staff,
					'id_user' => $id_user,
					'id_kategori_staff' => $this->request->getPost('id_kategori_staff'),
					'urutan' => $this->request->getPost('urutan'),
					'nama' => $this->request->getPost('nama'),
					'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
					'jabatan' => $this->request->getPost('jabatan'),
					'alamat' => $this->request->getPost('alamat'),
					'telepon' => $this->request->getPost('telepon'),
					'website' => $this->request->getPost('website'),
					'email' => $this->request->getPost('email'),
					'keahlian' => $this->request->getPost('keahlian'),
					'status_staff' => $this->request->getPost('status_staff'),
					'tempat_lahir' => $this->request->getPost('tempat_lahir'),
					'tanggal_lahir' => date('Y-m-d', strtotime($this->request->getPost('tanggal_lahir')))
				];

				$m_staff->edit($data);
				$this->session->setFlashdata('sukses', 'Data telah diupdate');
				return redirect()->to(base_url('admin/akun#staff'));
			}
		}

		$data = [
			'title' => 'Profil Saya',
			'user' => $user,
			'staff' => $staff,
			'kategori_staff' => $kategori_staff,
			'content' => 'admin/akun/index'
		];
		echo view('admin/layout/wrapper', $data);
	}
}
