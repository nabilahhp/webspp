<?php

namespace App\Models;

use CodeIgniter\Model;

class User_model extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id_user';
    protected $allowedFields    = [
        'nama',
        'email',
        'username',
        'password',
        'akses_level',
        'kode_rahasia',
        'gambar',
        'keterangan',
        'ip_address',
        'tanggal_post',
        'tanggal'
    ];
    protected $returnType       = 'array';

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    // Insert data dan return ID terakhir
    public function insertGetId($data)
    {
        $this->insert($data);
        return $this->insertID();
    }

    // Login (gunakan password_hash untuk pembanding ke depan)
    public function login($username, $password)
    {
        $builder = $this->db->table('users');
        $builder->select('users.*, staff.nama AS nama_staff, staff.jabatan, staff.id_staff');
        $builder->join('staff', 'staff.id_user = users.id_user', 'LEFT'); // ✅ bukan id_staff
        $builder->where('users.username', $username);
        $builder->where('users.password', sha1($password)); // ❗ sementara masih pakai sha1
        $query = $builder->get();
        return $query->getRow(); // NULL jika gagal
    }


    // Listing semua user
    public function listing()
    {
        return $this->db->table('users')
            ->select('users.*, staff.nama AS nama_staff, staff.jabatan')
            ->join('staff', 'staff.id_user = users.id_user', 'LEFT') // ✅
            ->orderBy('users.id_user', 'DESC')
            ->get()
            ->getResult();
    }

    // Total user
    public function total()
    {
        return $this->db->table('users')
            ->select('COUNT(*) AS total')
            ->get()
            ->getRow();
    }

    // Ambil detail user berdasarkan ID
    public function detail($id_user)
    {
        return $this->db->table('users')
            ->select('users.*, staff.nama AS nama_staff, staff.jabatan, staff.id_staff')
            ->join('staff', 'staff.id_user = users.id_user', 'LEFT') // ✅
            ->where('users.id_user', $id_user)
            ->get()
            ->getRow();
    }

    // Cek berdasarkan kode rahasia
    public function kode_rahasia($kode_rahasia)
    {
        return $this->db->table('users')
            ->select('users.*, staff.nama AS nama_staff, staff.jabatan')
            ->join('staff', 'staff.id_user = users.id_user', 'LEFT') // ✅
            ->where('users.kode_rahasia', $kode_rahasia)
            ->get()
            ->getRow();
    }

    // Cek berdasarkan email
    public function check($email)
    {
        return $this->db->table('users')
            ->select('users.*, staff.nama AS nama_staff, staff.jabatan')
            ->join('staff', 'staff.id_user = users.id_user', 'LEFT') // ✅
            ->where('users.email', $email)
            ->get()
            ->getRow();
    }

    // Edit user
    public function edit($data)
    {
        return $this->db->table('users')
            ->where('id_user', $data['id_user'])
            ->update($data);
    }

    // Tambah user
    public function tambah($data)
    {
        return $this->db->table('users')->insert($data);
    }

    // Log aktivitas user
    public function user_log($data)
    {
        return $this->db->table('user_logs')->insert($data);
    }
}
