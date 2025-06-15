<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\Tagihan_model;

class Tagihan extends BaseController
{
	protected $tagihanModel;

	public function __construct()
	{
		helper('format');
		$this->tagihanModel = new Tagihan_model();
	}

	public function index()
	{
		$id_siswa = session()->get('id_siswa');
		if (!$id_siswa) {
			return redirect()->to('/login'); // Jaga-jaga kalau session hilang
		}

		// Perbarui status tagihan berdasarkan usia
		$this->tagihanModel->updateStatusOtomatis();

		// Ambil tagihan siswa
		$tagihan = $this->tagihanModel->getTagihanBySiswa($id_siswa);

		$data = [
			'title'        => 'Tagihan SPP',
			'description'  => 'Data Tagihan SPP',
			'keywords'     => 'Data Tagihan SPP',
			'tagihan'      => $tagihan,
			'id_siswa'     => $id_siswa,
			'content'      => 'siswa/tagihan/index'
		];

		return view('siswa/layout/wrapper', $data);
	}
}
