<?php

namespace App\Models;

use CodeIgniter\Model;

class Order_model extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id_order';
    protected $allowedFields = [
        'id_order',
        'id_siswa',
        'id_tagihan',
        'status',
        'invoice_number',
        'jumlah_bayar',
        'metode_pembayaran',
        'response_code',
        'response_message',
        'response_data',
        'tanggal_bayar',
        'created_at',
        'updated_at'
    ];
}
