<?php 
namespace App\Models;

use CodeIgniter\Model;

class Konfigurasi_model extends Model
{
    protected $table = 'konfigurasi';
    protected $primaryKey = 'id_konfigurasi';
    
    // Constructor
    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }
    
    // Listing - Mendapatkan data konfigurasi email dari database
    public function listing()
    {
        // Mengakses tabel 'konfigurasi'
        $builder = $this->db->table('konfigurasi');
        
        // Mengambil semua data dari tabel
        $builder->select('*');
        $query = $builder->get();

        // Mengambil satu baris hasil query
        return $query->getRow(); // Pastikan hanya ada satu baris konfigurasi
    }

    // Edit - Untuk mengupdate data konfigurasi
    public function edit($data)
    {
        $builder = $this->db->table('konfigurasi');
        
        // Menentukan kondisi berdasarkan id_konfigurasi
        $builder->where('id_konfigurasi', $data['id_konfigurasi']);
        
        // Update data konfigurasi
        $builder->update($data);
    }
}