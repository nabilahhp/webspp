<?php

namespace App\Models;

use CodeIgniter\Model;

class Input_tagihan_model extends Model
{
    protected $table      = 'input_tagihan';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;


    protected $allowedFields = [
        'bulan_tagihan',
        'jumlah',
        'detail',
        'id_kelas',
        'id_tahun',
        'id_staff',       // staf yang input tagihan
        'created_at',
        'updated_at',
    ];
    public function getTagihanTelatWaliKelas($id_kelas, $id_tahun, $keywords = null)
    {
        $builder = $this->db->table('tagihan')
            ->select([
                'tagihan.*',
                'siswa.nama_siswa',
                'kelas.nama_kelas',
                'input_tagihan.bulan_tagihan',
                'input_tagihan.jumlah',  // Pastikan jumlah ada di select()
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


    // Ambil semua data input tagihan, termasuk nama staf dari tabel users (join)
    public function getAllWithUser()
    {
        return $this->select('input_tagihan.*, users.username as nama_staff')
            ->join('users', 'users.id_user = input_tagihan.id_staff', 'left')
            ->orderBy('input_tagihan.created_at', 'DESC')
            ->findAll();
    }

    // Ambil data input tagihan berdasarkan ID
    public function getById($id)
    {
        return $this->where('id', $id)->first();
    }

    // Simpan data baru
    public function insertData($data)
    {
        return $this->insert($data);
    }

    // Update data berdasarkan ID
    public function updateData($id, $data)
    {
        return $this->update($id, $data);
    }

    // Hapus data berdasarkan ID
    public function deleteData($id)
    {
        return $this->delete($id);
    }

    // Fungsi untuk mengambil daftar tahun ajaran
    public function getTahunAjaranList()
    {
        return $this->db->table('tahun')
            ->select('CONCAT(tahun_mulai, "/", tahun_selesai) AS tahun_ajaran')
            ->distinct()
            ->get()
            ->getResultArray();
    }

    // Fungsi untuk mengambil daftar kelas
    public function getKelasList()
    {
        return $this->db->table('kelas')
            ->select('nama_kelas')
            ->distinct()
            ->get()
            ->getResultArray();
    }
}
