<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\Tagihan_model;

class UpdateTagihanStatus extends BaseCommand
{
    protected $group       = 'Tagihan';
    protected $name        = 'tagihan:update-status';
    protected $description = 'Update status otomatis untuk tagihan berdasarkan usia';

    public function run(array $params)
     {
        $model = new Tagihan_model();
        $model->updateStatusOtomatis();

        CLI::write('✔ Status tagihan berhasil diperbarui.', 'green');
    }
}
