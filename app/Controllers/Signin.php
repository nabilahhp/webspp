<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Konfigurasi_model;
use App\Models\Siswa_model; // Menggunakan hanya Siswa_model

class Signin extends BaseController
{
    public function __construct()
    {
        helper('form');
    }

    // Halaman Login
    public function index()
    {
        // Membuat session service
        $session = \Config\Services::session();

        // Menyimpan redirect jika ada
        if (isset($_GET['redirect'])) {
            $session->set('pengalihan', $_GET['redirect']);
        }

        // Memuat model untuk konfigurasi
        $m_konfigurasi = new Konfigurasi_model();
        $konfigurasi = $m_konfigurasi->listing();

        // Cek validasi form login
        if ($this->request->getMethod() === 'post' && $this->validate([
            'username' => 'required|min_length[3]',
            'password' => 'required|min_length[3]',
        ])) {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');
            return $this->login_siswa($username, $password);  // Panggil fungsi login siswa
        }

        // Data yang akan dipassing ke view
        $data = [
            'title' => 'Login Siswa',
            'description' => 'Login Siswa ' . $konfigurasi->namaweb . ', ' . $konfigurasi->tentang,
            'keywords' => 'Login Siswa ' . $konfigurasi->namaweb . ', ' . $konfigurasi->keywords,
            'session' => $session,  // Mempassing session ke view
            'content' => 'signin/index'  // Nama tampilan yang akan digunakan
        ];

        // Menampilkan tampilan
        return view('layout/wrapper', $data);  // Menampilkan layout dengan data
    }

    public function login_siswa($username, $password)
    {
        $this->session = \Config\Services::session();
        $uri = service('uri');
        $m_siswa = new Siswa_model();

        // Cari siswa berdasarkan NIS
        $siswa = $m_siswa->getSiswaByNIS($username);  // Mencari siswa berdasarkan NIS

        if ($siswa) {
            // Verifikasi password dengan password_verify
            if (password_verify($password, $siswa['password'])) {  // Gunakan password_verify untuk memverifikasi password yang di-hash
                // Login sukses, set session
                $this->session->set([
                    'username_siswa' => $username,
                    'id_siswa' => $siswa['id_siswa'],
                    'nama_siswa' => $siswa['nama_siswa'],
                    'gambar' => $siswa['gambar'],
                    'logged_in' => true
                ]);

                session_write_close();  // ✅ wajib sebelum redirect
                return redirect()->to(base_url('siswa/dasbor'));  // Redirect ke dasbor
            } else {
                // Jika password tidak cocok
                $this->session->setFlashdata('warning', 'Username atau password salah');
                return redirect()->to(base_url('signin'));
            }
        } else {
            // Jika username (NIS) tidak ditemukan
            $this->session->setFlashdata('warning', 'Username atau password salah');
            return redirect()->to(base_url('signin'));
        }
    }



    // Logout
    public function logout()
    {
        log_message('info', 'User logged out');
        session()->destroy();  // Menghancurkan session
        return redirect()->to(base_url('signin?logout=sukses'));  // Redirect ke halaman login setelah logout
    }

    // Reset password (tambahan metode reset)
    public function reset()
    {
        // Menampilkan halaman reset password
        return view('signin/reset');  // Ganti dengan tampilan yang sesuai
    }

    public function sendResetPasswordEmail()
    {
        if (!$this->validate([
            'email' => 'required|valid_email'
        ])) {
            return redirect()->back()->with('error', 'Email tidak valid');
        }

        $email = $this->request->getPost('email');
        log_message('info', "Mencari email: $email");  // Log untuk email yang dimasukkan

        $m_siswa = new Siswa_model();
        $siswa = $m_siswa->getSiswaByEmail($email);

        if ($siswa) {
            log_message('info', "Siswa ditemukan: " . print_r($siswa, true));  // Log jika siswa ditemukan

            // Generate token reset password
            $token = bin2hex(random_bytes(50));  // Membuat token acak

            // Simpan token dan masa berlaku token di database
            $m_siswa->update($siswa['id_siswa'], [
                'password_reset_token' => $token,
                'password_expired' => date('Y-m-d H:i:s', strtotime('+1 hour'))  // Token berlaku 1 jam
            ]);

            // Kirimkan email dengan link reset password
            $resetLink = base_url('signin/resetPassword/' . $token);
            $emailService = \Config\Services::email();
            $emailService->setTo($email);
            $emailService->setSubject('Reset Password');
            $emailService->setMessage('Klik link berikut untuk mereset password Anda: ' . $resetLink);

            if ($emailService->send()) {
                return redirect()->back()->with('sukses', 'Link reset password telah dikirim ke email Anda.');
            } else {
                return redirect()->back()->with('error', 'Gagal mengirim email, coba lagi.');
            }
        } else {
            log_message('error', "Email $email tidak ditemukan di database.");
            return redirect()->back()->with('error', 'Email tidak ditemukan.');
        }
    }

    public function resetPassword($token)
    {
        log_message('info', "Menerima token reset password: $token");

        $m_siswa = new Siswa_model();
        $siswa = $m_siswa->where('password_reset_token', $token)->first();  // Mengambil data siswa berdasarkan token reset

        // Log token yang ditemukan di database
        log_message('info', "Siswa dengan token ditemukan: " . print_r($siswa, true));

        if ($siswa) {
            log_message('info', "Token valid, siswa ditemukan: " . print_r($siswa, true));

            // Cek apakah token belum kedaluwarsa
            if (strtotime($siswa['password_expired']) > time()) {
                log_message('info', "Token belum kedaluwarsa.");
                return view('signin/reset_password', ['token' => $token]);  // Menampilkan halaman reset password
            } else {
                log_message('error', "Token kedaluwarsa.");
                return redirect()->to('signin')->with('error', 'Link reset password sudah kedaluwarsa.');
            }
        } else {
            log_message('error', "Token tidak ditemukan.");
            return redirect()->to('signin')->with('error', 'Link reset password tidak valid.');
        }
    }

    public function updatePassword()
    {
        // Mendapatkan token dan password baru dari form
        $token = $this->request->getPost('token');
        $newPassword = $this->request->getPost('new_password');

        // Periksa apakah password baru telah diisi
        if (empty($newPassword)) {
            return redirect()->back()->withInput()->with('error', 'Password baru harus diisi.');
        }

        // Mencari siswa berdasarkan token yang diberikan
        $m_siswa = new Siswa_model();
        $siswa = $m_siswa->where('password_reset_token', $token)->first();

        if ($siswa) {
            // Token ditemukan, lakukan hash untuk password baru
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update password dan hapus token serta masa berlaku token
            $m_siswa->update($siswa['id_siswa'], [
                'password' => $hashedPassword,
                'password_reset_token' => null,  // Hapus token reset setelah digunakan
                'password_expired' => null,      // Hapus masa berlaku token
            ]);

            // Redirect ke halaman login dengan pesan sukses
            return redirect()->to('signin')->with('sukses', 'Password Anda berhasil direset.');
        } else {
            // Token tidak ditemukan atau sudah kedaluwarsa
            return redirect()->to('signin')->with('error', 'Token tidak valid atau sudah kedaluwarsa.');
        }
    }
}
