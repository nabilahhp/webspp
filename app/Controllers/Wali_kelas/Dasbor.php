<?php 
namespace App\Controllers\Wali_kelas;



class Dasbor extends BaseController
{
	public function index()
	{
		
		$data = [   'title'     => 'Dasbor Wali Kelas',
					'content'	=> 'wali_kelas/dasbor/index'
                ];
        return view('wali_kelas/layout/wrapper',$data);
	}
}