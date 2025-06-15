<?php

namespace App\Controllers;

use App\Models\User_model;
use App\Models\Konfigurasi_model;

class Login extends BaseController
{
    // login
    public function index()
    {
        if ($this->request->getMethod() === 'post' && $this->validate([
            'username' => 'required|min_length[3]',
            'password' => 'required|min_length[3]',
        ])) {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');
            $pengalihan = $this->request->getPost('pengalihan');

            // PENTING: return hasil login, supaya redirect berjalan
            return $this->simple_login->login($username, $password, $pengalihan);
        }

        $m_site = new Konfigurasi_model();
        $site = $m_site->listing();

        $data = [
            'title' => 'Login Karyawan',
            'site' => $site,
            'content' => 'login/index'
        ];

        return view('login/wrapper', $data);
    }


    // coba
    public function coba()
    {
        $username   = 'andoyo';
        $password   = 'andoyo';
        $pengalihan = '';
        $this->simple_login->login($username, $password, $pengalihan);
    }

    // lupa
    public function lupa()
    {
        $m_site = new Konfigurasi_model();
        $m_user = new User_model();
        $site = $m_site->listing();  // Mengambil data konfigurasi dari model

        // Jika permintaan menggunakan metode POST
        if ($this->request->getMethod() === 'post' && $this->validate(['email' => 'required|valid_email'])) {
            $email = $this->request->getPost('email');  // Mendapatkan email dari form
            $check = $m_user->check($email);  // Memeriksa apakah email ada di database

            // Jika email ditemukan di database
            if ($check) {
                // Generate kode reset password yang lebih aman
                $kode_rahasia = bin2hex(random_bytes(32));  // Membuat kode rahasia sepanjang 64 karakter

                // Data yang akan disimpan di database
                $data = [
                    'id_user'      => $check->id_user,
                    'kode_rahasia' => $kode_rahasia,
                    'ip_address'   => $_SERVER['REMOTE_ADDR'], // Menyimpan IP address untuk audit
                ];

                // Update data di database dengan kode reset
                $m_user->edit($data);

                // Membuat email untuk dikirimkan kepada pengguna
                $subject = 'Reset Password - ' . $site->namaweb;
                $message = '
            <p>Hai ' . $check->nama_staff . ',</p>
            <p>Anda telah meminta untuk mereset password akun Anda.</p>
            <p>Untuk mereset password, klik link berikut:</p>
            <p><a href="' . base_url('login/reset/' . $kode_rahasia) . '">' . base_url('login/reset/' . $kode_rahasia) . '</a></p>
            <p>Link ini hanya berlaku selama 24 jam.</p>
            <p>Jika Anda tidak merasa meminta reset password, abaikan email ini.</p>
            <hr>
            <p>' . $site->namaweb . '</p>
            ';

                // Memanggil layanan email dari CodeIgniter
                $emailService = \Config\Services::email();  // Menggunakan layanan email dari Config/Email.php

                // Tentukan pengirim dengan alamat email yang benar
                $emailService->setFrom('e31220197@student.polije.ac.id', 'nabilah');  // Alamat email dan nama pengirim yang diinginkan
                $emailService->setTo($email);  // Alamat email penerima
                $emailService->setSubject($subject);  // Subjek email
                $emailService->setMessage($message);  // Isi pesan email

                // Mengirimkan email
                if ($emailService->send()) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Link reset password telah dikirimkan ke email Anda.'
                    ]);
                } else {
                    // Menampilkan error jika email gagal dikirim
                    $error = $emailService->printDebugger(['headers']);
                    log_message('error', 'Email sending failed: ' . $error);

                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal mengirim email reset password. Silakan coba lagi atau hubungi administrator.'
                    ]);
                }
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Email tidak ditemukan atau tidak terdaftar.'
                ]);
            }
        }

        // Jika metode request bukan POST, tampilkan form lupa password
        $data = [
            'title'   => 'Lupa Password',
            'site'    => $site,
            'content' => 'login/lupa'  // Halaman tampilan untuk form lupa password
        ];
        return view('login/wrapper', $data);
    }



    // reset
    public function reset($kode_rahasia = '')
    {
        $m_site = new Konfigurasi_model();
        $m_user = new User_model();
        $site = $m_site->listing();
        $user = $m_user->kode_rahasia($kode_rahasia);

        if ($kode_rahasia == '') { //! validate empty token
            $this->session->setFlashdata('warning', 'Token tidak valid atau kosong.');
            return redirect()->to(base_url('login'));
        }

        if ($user == null) {
            $this->session->setFlashdata('warning', 'Token tidak valid atau masa berlaku token sudah habis.');
            return redirect()->to(base_url('login'));
        }

        // Start validasi
        if ($this->request->getMethod() === 'post' && $this->validate([
            'password'            => 'required|min_length[6]',
            'password_konfirmasi' => 'required|matches[password]'
        ])) {
            $data = [
                'id_user'      => $user->id_user,
                'password'     => sha1($this->request->getPost('password')),
                'kode_rahasia' => ''
            ];
            $m_user->edit($data);
            // masuk database
            $this->session->setFlashdata('sukses', 'Password telah diupdate. Silakan login dengan password baru Anda.');
            return redirect()->to(base_url('login'));
        } else {
            $data = [
                'title'         => 'Reset Password',
                'site'          => $site,
                'user'          => $user,
                'kode_rahasia'  => $kode_rahasia,
                'content'       => 'login/reset'
            ];
            return view('login/wrapper', $data);
        }
    }

    //logout
    public function logout()
    {
        $this->session->destroy();
        return redirect()->to(base_url('login?logout=sukses'));
    }
}
