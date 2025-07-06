<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Config\Services;
use App\Models\Tagihan_model;
use App\Models\Siswa_model;

class KirimPengingat extends BaseCommand
{
    protected $group       = 'Tagihan';
    protected $name        = 'tagihan:kirim-pengingat';
    protected $description = 'Kirim pengingat tagihan ke semua siswa yang belum diberi notifikasi dan update status otomatis';

    public function run(array $params)
    {
        $logger = Services::logger();
        $tagihanModel = new Tagihan_model();
        $siswaModel   = new Siswa_model();

        $token = 'XJxJLfoFBfYSZiZ26RuT'; // Gunakan .env jika sudah

        CLI::write('📍 Memulai pengiriman pengingat tagihan...', 'yellow');
        $logger->info('📍 Memulai pengiriman pengingat tagihan...');

        // Update status tagihan otomatis
        $tagihanModel->updateStatusOtomatis();

        // Ambil semua siswa
        $siswaList = $siswaModel->findAll();

        foreach ($siswaList as $siswa) {
            $tagihanList = $tagihanModel->getTagihanAndStatus($siswa['id_siswa']);

            foreach ($tagihanList as $tagihan) {
                
                $lastNotified = $tagihan['last_notified'];
                $nowMonth = date('Y-m');
                $notifiedMonth = $lastNotified ? date('Y-m', strtotime($lastNotified)) : null;

                $shouldNotify = false;

                if ($tagihan['status'] === 'Telat Bayar') {
                    $shouldNotify = empty($lastNotified) || $notifiedMonth < $nowMonth;
                } else {
                    $shouldNotify = empty($lastNotified);
                }

                if ($shouldNotify) {
                    $pesan = $this->generateMessage($tagihan['status'], $tagihan);

                    if ($pesan) {
                        $nomor_ayah = $this->formatNomor($siswa['telepon_ayah']);
                        $nomor_ibu  = $this->formatNomor($siswa['telepon_ibu']);
                        $nomor_wali = $this->formatNomor($siswa['telepon_wali']);

                        if ($nomor_ayah) {
                            $this->sendMessage($nomor_ayah, $pesan, $token);
                        }
                        if ($nomor_ibu) {
                            $this->sendMessage($nomor_ibu, $pesan, $token);
                        }
                        if ($nomor_wali) {
                            $this->sendMessage($nomor_wali, $pesan, $token);
                        }

                        // Update waktu pengingat terakhir
                        $tagihanModel->update($tagihan['id'], [
                            'last_notified' => date('Y-m-d H:i:s')
                        ]);

                        $logger->info("Pesan pengingat untuk siswa {$siswa['nama_siswa']} (ID: {$siswa['id_siswa']}) telah dikirim.");
                    }
                }
                CLI::write("DEBUG - ID Tagihan: {$tagihan['id']}", 'light_cyan');
                CLI::write("Status: {$tagihan['status']}", 'light_cyan');
                CLI::write("Last Notified: " . ($lastNotified ?? 'NULL'), 'light_cyan');
                CLI::write("Notified Month: " . ($notifiedMonth ?? 'NULL'), 'light_cyan');
                CLI::write("Now Month: $nowMonth", 'light_cyan');
                CLI::write("Should Notify? " . ($shouldNotify ? '✅ YA' : '❌ TIDAK'), 'light_cyan');
            }
        }

        CLI::write('✅ Semua pengingat berhasil dikirim!', 'green');
        $logger->info('✅ Semua pengingat berhasil dikirim!');
    }

    private function generateMessage($status, $tagihan)
    {
        $nama   = $tagihan['nama_siswa'];
        $nis    = $tagihan['nis'];
        $bulan  = $tagihan['bulan_tagihan'];
        $jumlah = number_format($tagihan['jumlah'], 0, ',', '.');
        $link   = 'smamugapay.my.id';

        $pesanPembuka = "Pemberitahuan Kepada Orang Tua atau Wali Siswa *$nama* dengan NIS *$nis*: ";
        $pesanPenutup = "\n\nPesan dikirim otomatis oleh Sistem Pembayaran SPP SMA Muhammadiyah 3 Sidoarjo.\n\nSegera lakukan pembayaran melalui link berikut,\n$link\n\n! Abaikan pesan ini jika sudah membayar !";

        switch ($status) {
            case 'Belum Bayar':
                return $pesanPembuka . "Untuk segera membayar Tagihan SPP bulan *$bulan* sebesar *Rp$jumlah*. Mohon segera membayar." . $pesanPenutup;

            case 'Tertunggak':
                return $pesanPembuka . "Tagihan SPP bulan *$bulan* sebesar *Rp$jumlah* sudah tertunggak lebih dari 10 hari. Mohon segera membayar sebelum status tagihan menjadi Telat Bayar." . $pesanPenutup;

            case 'Telat Bayar':
                return $pesanPembuka . "Tagihan SPP bulan *$bulan* sebesar *Rp$jumlah* sudah mengalami keterlambatan lebih dari 13 hari. Mohon segera menghubungi wali kelas sebelum melakukan pembayaran." . $pesanPenutup;

            default:
                return null;
        }
    }


    private function formatNomor($nomor)
    {
        $nomor = preg_replace('/[^0-9]/', '', $nomor);
        if (substr($nomor, 0, 1) === '0') {
            return '+62' . substr($nomor, 1);
        } elseif (substr($nomor, 0, 2) === '62') {
            return '+' . $nomor;
        }
        return null;
    }

    private function sendMessage($target, $message, $token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => array(
                'target' => $target,
                'message' => $message,
                'countryCode' => '62'
            ),
            CURLOPT_HTTPHEADER => array(
                "Authorization: $token"
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        // Log hasil pengiriman pesan
        Services::logger()->info("Pesan dikirim ke $target dengan respon: $response");
        CLI::write("ðŸ“¨ $target => $response", 'blue');
    }
}
