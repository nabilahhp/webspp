<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Config\Services;
use App\Models\Siswa_model;

class Tagihan_model extends Model
{
    protected $table = 'tagihan';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['id_siswa', 'id_input_tagihan', 'status', 'tanggal_bayar', 'created_at', 'last_notified'];


    public function getTagihanAndStatus($id_siswa)
    {
        // Ambil semua tagihan siswa dengan join ke tabel input_tagihan
        $builder = $this->db->table('tagihan')
            ->select([
                'tagihan.*',
                'input_tagihan.bulan_tagihan',
                'input_tagihan.jumlah',
                'siswa.nama_siswa'
            ])
            ->join('input_tagihan', 'input_tagihan.id = tagihan.id_input_tagihan', 'left')
            ->join('siswa', 'siswa.id_siswa = tagihan.id_siswa', 'left')
            ->where('tagihan.id_siswa', $id_siswa)
            ->orderBy('tagihan.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }


    public function getTagihanTelatWaliKelas($id_kelas, $id_tahun, $keywords = null)
    {
        $builder = $this->db->table('tagihan')
            ->select([
                'tagihan.*',
                'siswa.nama_siswa',
                'kelas.nama_kelas',
                'input_tagihan.bulan_tagihan',
                'input_tagihan.jumlah',
                'CONCAT(tahun.tahun_mulai, "/", tahun.tahun_selesai) AS tahun_ajaran'
            ])
            ->join('siswa', 'siswa.id_siswa = tagihan.id_siswa', 'left')
            ->join('kelas', 'kelas.id_kelas = siswa.id_kelas', 'left')
            ->join('tahun', 'tahun.id_tahun = siswa.id_tahun', 'left')
            ->join('input_tagihan', 'input_tagihan.id = tagihan.id_input_tagihan', 'left')
            ->where('siswa.id_kelas', $id_kelas)
            ->where('siswa.id_tahun', $id_tahun)
            ->where('tagihan.status !=', 'lunas')
            ->where('tagihan.tanggal_bayar IS NULL');

        if ($keywords) {
            $builder->groupStart()
                ->like('siswa.nama_siswa', $keywords)
                ->orLike('tagihan.status', $keywords)
                ->groupEnd();
        }

        return $builder->orderBy('tagihan.created_at', 'DESC')->get()->getResultArray();
    }


    public function getTagihanBySiswa($id_siswa)
    {
        return $this->db->table('tagihan')
            ->select([
                'tagihan.id',
                'input_tagihan.bulan_tagihan', // ini penting
                'input_tagihan.jumlah',
                'input_tagihan.detail',
                'tagihan.status',
                'tagihan.tanggal_bayar',
                'siswa.nama_siswa'
            ])
            ->join('input_tagihan', 'input_tagihan.id = tagihan.id_input_tagihan')
            ->join('siswa', 'siswa.id_siswa = tagihan.id_siswa')
            ->where('tagihan.id_siswa', $id_siswa)
            ->orderBy('input_tagihan.bulan_tagihan', 'ASC')
            ->get()
            ->getResultArray();
    }



    public function getDetailForPayment($id)
    {
        return $this->select('input_tagihan.jumlah, siswa.nama_siswa, siswa.email')
            ->join('input_tagihan', 'input_tagihan.id = tagihan.id_input_tagihan')
            ->join('siswa', 'siswa.id_siswa = tagihan.id_siswa')
            ->where('tagihan.id', $id)
            ->first();
    }


    private $logger;
    protected $siswaModel;

    public function __construct()
    {
        // Inisialisasi logger dengan benar menggunakan Services::logger()
        $this->logger = Services::logger();
        $this->siswaModel = new Siswa_model();
    }



    public function updateStatusOtomatis()
    {
        // Menulis log bahwa proses update dimulai
        $this->logger->info('Memulai proses update status tagihan...');

        // Ambil semua tagihan dengan status tertentu
        $builder = $this->builder(); // Menggunakan query builder untuk kondisi lebih fleksibel
        $builder->whereIn('status', ['Belum Bayar', 'Tertunggak']); // Kondisi status
        $tagihanList = $builder->get()->getResultArray();

        if (empty($tagihanList)) {
            $this->logger->info('Tidak ada tagihan yang memerlukan update status.');
            return;
        }

        foreach ($tagihanList as $tagihan) {
            // Menghitung usia tagihan (dalam hari)
            $tanggal_dibuat = strtotime($tagihan['created_at']);
            $hari_ini = time();
            $selisih_hari = floor(($hari_ini - $tanggal_dibuat) / (60 * 60 * 24));

            $status_baru = null;

            // Tentukan status baru berdasarkan usia tagihan
            if ($tagihan['status'] == 'Belum Bayar' && $selisih_hari > 10) {
                $status_baru = 'Tertunggak';
            } elseif ($tagihan['status'] == 'Tertunggak' && $selisih_hari > 13) {
                $status_baru = 'Telat Bayar';
            }

            // Update status jika ada perubahan dan set last_notified ke NULL
            if ($status_baru) {
                $this->update($tagihan['id'], [
                    'status' => $status_baru,
                    'last_notified' => null, // Kolom last_notified di-set menjadi NULL
                ]);

                // Menulis log bahwa status tagihan telah diperbarui
                $this->logger->info("Memperbarui status tagihan ID: {$tagihan['id']} menjadi {$status_baru}");
            }
        }

        // Menulis log bahwa proses update selesai
        $this->logger->info('Proses update status tagihan selesai.');
    }
}
