<?php

namespace App\Models;

use CodeIgniter\Model;

class Riwayat_pembayaran_model extends Model
{
    protected $table = 'riwayat_pembayaran';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_siswa', 'id_tagihan', 'tanggal_bayar', 'jumlah_bayar', 'id_order', 'created_at'];
    protected $useTimestamps = false; // karena kamu manual atur created_at

    public function getRiwayatLengkap()
    {
        return $this->select('riwayat_pembayaran.*, siswa.nama_siswa, input_tagihan.bulan_tagihan')
            ->join('siswa', 'siswa.id_siswa = riwayat_pembayaran.id_siswa')
            ->join('input_tagihan', 'input_tagihan.id = riwayat_pembayaran.id_input_tagihan')
            ->orderBy('riwayat_pembayaran.tanggal_bayar', 'DESC')
            ->findAll();
    }
    public function getRiwayatBySiswa($id_siswa)
    {
        return $this->select('riwayat_pembayaran.*, input_tagihan.bulan_tagihan, orders.invoice_number, orders.metode_pembayaran, orders.response_message, orders.status, orders.jumlah_bayar')
            ->join('tagihan', 'riwayat_pembayaran.id_tagihan = tagihan.id')
            ->join('input_tagihan', 'tagihan.id_input_tagihan = input_tagihan.id')
            ->join('orders', 'orders.id_order = riwayat_pembayaran.id_order', 'left') // Join dengan tabel orders untuk mengambil invoice_number
            ->where('riwayat_pembayaran.id_siswa', $id_siswa)
            ->orderBy('riwayat_pembayaran.tanggal_bayar', 'DESC')
            ->findAll();
    }
}
