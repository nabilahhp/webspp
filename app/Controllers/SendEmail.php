<?php

namespace App\Controllers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use CodeIgniter\Controller;

class SendEmail extends Controller
{
    // Fungsi untuk mengirim email reset password
    public function sendResetPasswordEmail($email, $token)
    {
        require_once APPPATH . '../vendor/autoload.php';

        $mail = new PHPMailer(true);

        try {
            // Set pengaturan SMTP untuk Gmail
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  // SMTP server Gmail
            $mail->SMTPAuth = true;
            $mail->Username = 'e31220197@student.polije.ac.id';  // Ganti dengan email Anda
            $mail->Password = 'zbzrpskalixhkjvp';  // Kata sandi aplikasi Gmail Anda (gunakan kata sandi aplikasi jika 2FA aktif)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Gunakan enkripsi TLS
            $mail->SMTPDebug = 2;
            $mail->Port = 587;  // Port untuk TLS

            // Set pengirim dan penerima
            $mail->setFrom('putrinabilah2003@gmail.com', 'SMAMUGAPAY');  // Ganti dengan email dan nama pengirim Anda
            $mail->addAddress($email, 'SMAMUGAPAY');  // Ganti dengan email penerima yang sesuai

            // Isi email
            $mail->isHTML(true);  // Set format email ke HTML
            $mail->Subject = 'Reset Password';
            $mail->Body    = 'Klik link berikut untuk mereset password Anda: <a href="' . base_url('signin/resetPassword/' . $token) . '">Reset Password</a>';
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Kirim email
            if ($mail->send()) {
                log_message('info', "Email berhasil dikirim ke $email");
            } else {
                log_message('error', "Gagal mengirim email: " . $mail->ErrorInfo);
            }
        } catch (Exception $e) {
            log_message('error', "Email gagal dikirim. Kesalahan: {$mail->ErrorInfo}");
        }
    }
}
