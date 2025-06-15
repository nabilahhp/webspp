<?php
namespace App\Controllers;

class Assets extends BaseController
{
    public function template()
    {
        // Contoh: mengirim file Excel ke browser untuk di-download
        return $this->response->download(APPPATH . 'assets/template/template-siswa.xlsx', null);
    }
}
