<?php 
namespace App\Controllers\Staff_keuangan;

use CodeIgniter\Controller;

class Dasbor extends BaseController
{
	public function index()
	{
		
		$data = [   'title'     => 'Dasbor Staff Keuangan',
					'content'	=> 'staff_keuangan/dasbor/index'
                ];
        return view('staff_keuangan/layout/wrapper',$data);
	}
}