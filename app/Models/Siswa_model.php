<?php

namespace App\Models;

use CodeIgniter\Model;

class Siswa_model extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }
    protected $useTimestamps = false;

    protected $table = 'siswa';
    protected $primaryKey = 'id_siswa';
    protected $returnType = 'array';
    protected $allowedFields = [
        'id_user',
        'id_tahun',
        'id_kelas',
        'kode_siswa',
        'slug_siswa',
        'nis',
        'nama_siswa',
        'telepon',
        'email',
        'kategori',
        'password',
        'password_reset_token',
        'password_expired',
        'jenis_kelamin',
        'isi',
        'nama_ayah',
        'nama_ibu',
        'telepon_ayah',
        'telepon_ibu',
        'kelompok',
        'gambar',
        'status_siswa',
        'tanggal_baca',
        'tanggal_post',
        'tanggal'
    ];


    public function getSiswaByNIS($nis)
    {
        // Mengambil data siswa berdasarkan NIS, kembalikan sebagai array
        return $this->where('nis', $nis)->first();  // Menggunakan first() untuk mendapatkan satu baris data
    }

    // Fungsi untuk mencari siswa berdasarkan email
    public function getSiswaByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    public function getByIdAkun($id_akun)
    {
        return $this->where('id_akun', $id_akun)->first();
    }


    // listing
    public function listing()
    {
        $builder = $this->db->table('siswa');
        $builder->select('siswa.*, kelas.nama_kelas, tahun.nama_tahun');
        $builder->join('kelas', 'kelas.id_kelas = siswa.id_kelas', 'LEFT');
        $builder->join('tahun', 'tahun.id_tahun = siswa.id_tahun', 'LEFT');
        $builder->orderBy('siswa.id_siswa', 'DESC');
        $query = $builder->get();
        return $query->getResult();
    }

    // status_siswa
    public function status_siswa($status_siswa)
    {
        $builder = $this->db->table('siswa');
        $builder->select('siswa.*, kelas.nama_kelas, tahun.nama_tahun');
        $builder->join('kelas', 'kelas.id_kelas = siswa.id_kelas', 'LEFT');
        $builder->join('tahun', 'tahun.id_tahun = siswa.id_tahun', 'LEFT');
        $builder->where('status_siswa', $status_siswa);
        $builder->orderBy('siswa.id_siswa', 'DESC');
        $query = $builder->get();
        return $query->getResult();
    }

    // paginasi
    public function paginasi($limit, $start)
    {
        $builder = $this->db->table('siswa');
        $builder->select('siswa.*,  kelas.nama_kelas, tahun.nama_tahun');
        $builder->join('kelas', 'kelas.id_kelas = siswa.id_kelas', 'LEFT');
        $builder->join('tahun', 'tahun.id_tahun = siswa.id_tahun', 'LEFT');
        $builder->limit($limit, $start);
        $builder->orderBy('siswa.id_siswa', 'DESC');
        $query = $builder->get();
        return $query->getResult();
    }

    // paginasi cari
    public function paginasi_cari($keywords, $limit, $start)
    {
        $builder = $this->db->table('siswa');
        $builder->select('siswa.*, kelas.nama_kelas, tahun.nama_tahun');
        $builder->join('kelas', 'kelas.id_kelas = siswa.id_kelas', 'LEFT');
        $builder->join('tahun', 'tahun.id_tahun = siswa.id_tahun', 'LEFT');
        $builder->like('nama_siswa', $keywords, 'BOTH');
        $builder->orLike('email', $keywords, 'BOTH');
        $builder->orLike('nama_ayah', $keywords, 'BOTH');
        $builder->orLike('nama_ibu', $keywords, 'BOTH');
        $builder->orLike('nama_wali', $keywords, 'BOTH');
        $builder->orLike('alamat', $keywords, 'BOTH');
        $builder->orLike('telepon', $keywords, 'BOTH');
        $builder->limit($limit, $start);
        $builder->orderBy('siswa.id_siswa', 'DESC');
        $query = $builder->get();
        return $query->getResult();
    }

    // total cari
    public function total_cari($keywords)
    {
        $builder = $this->db->table('siswa');
        $builder->select('COUNT(*) AS total');
        $builder->like('nama_siswa', $keywords, 'BOTH');
        $builder->orLike('email', $keywords, 'BOTH');
        $builder->orLike('nama_ayah', $keywords, 'BOTH');
        $builder->orLike('nama_ibu', $keywords, 'BOTH');
        $builder->orLike('nama_wali', $keywords, 'BOTH');
        $builder->orLike('alamat', $keywords, 'BOTH');
        $builder->orLike('telepon', $keywords, 'BOTH');
        $query = $builder->get();
        return $query->getRow();
    }

    // total
    public function total()
    {
        $builder = $this->db->table('siswa');
        $builder->select('COUNT(*) AS total');
        $builder->orderBy('siswa.id_siswa', 'DESC');
        $query = $builder->get();
        return $query->getRow();
    }

    // last id
    public function last_id()
    {
        $builder = $this->db->table('siswa');
        $builder->orderBy('siswa.id_siswa', 'DESC');
        $query = $builder->get();
        return $query->getRow();
    }

    // detail
    public function detail($id_siswa)
    {
        $builder = $this->db->table('siswa');
        $builder->select('siswa.*, kelas.nama_kelas, tahun.nama_tahun');
        $builder->join('kelas', 'kelas.id_kelas = siswa.id_kelas', 'LEFT');
        $builder->join('tahun', 'tahun.id_tahun = siswa.id_tahun', 'LEFT');
        $builder->where('id_siswa', $id_siswa);
        $builder->orderBy('siswa.id_siswa', 'DESC');
        $query = $builder->get();
        return $query->getRow();
    }

    // read by slug
    public function read($slug_siswa)
    {
        $builder = $this->db->table('siswa');
        $builder->where('slug_siswa', $slug_siswa);
        $builder->orderBy('siswa.id_siswa', 'DESC');
        $query = $builder->get();
        return $query->getRow();
    }

    // edit
    public function edit($data)
    {
        $builder = $this->db->table('siswa');
        $builder->where('id_siswa', $data['id_siswa']);
        $builder->update($data);
    }

    // tambah
    public function tambah($data)
    {
        $builder = $this->db->table('siswa');
        $builder->insert($data);
    }
}
