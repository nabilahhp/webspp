<?php

namespace App\Controllers\Staff_keuangan;

use CodeIgniter\Controller;
use App\Models\Tagihan_model;
use App\Models\Siswa_model;

class Pengingat extends Controller
{
    protected $tagihanModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->tagihanModel = new Tagihan_model();
        $this->siswaModel = new Siswa_model();
    }

    public function kirimPengingatTagihan($id_siswa)
    {
        $token = 'XJxJLfoFBfYSZiZ26RuT'; // Ganti dengan token asli Anda

        $tagihanList = $this->tagihanModel->getTagihanAndStatus($id_siswa);

        foreach ($tagihanList as $tagihan) {
            if (empty($tagihan['last_notified'])) {
                $tanggal_dibuat = strtotime($tagihan['created_at']);
                $hari_ini = time();
                $selisih_hari = floor(($hari_ini - $tanggal_dibuat) / (60 * 60 * 24));

                $pesan = $this->generateMessage($tagihan['status'], $tagihan);

                // Kirim hanya jika pesan tidak kosong
                if (!empty($pesan)) {
                    $siswa = $this->siswaModel->find($tagihan['id_siswa']);
                    $nomor_ayah = $this->formatNomorTelepon($siswa['telepon_ayah']);
                    $nomor_ibu = $this->formatNomorTelepon($siswa['telepon_ibu']);

                    if (!empty($nomor_ayah)) {
                        $this->sendMessage($nomor_ayah, $pesan, $token);
                    }

                    if (!empty($nomor_ibu)) {
                        $this->sendMessage($nomor_ibu, $pesan, $token);
                    }

                    // Tandai bahwa sudah pernah dikirim pengingat
                    $this->tagihanModel->update($tagihan['id'], [
                        'last_notified' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }

        return 'Pengingat tagihan telah dikirim!';
    }

    private function formatNomorTelepon($nomor)
    {
        if (substr($nomor, 0, 2) == '08') {
            return '+62' . substr($nomor, 1);
        }
        return $nomor;
    }

    private function generateMessage($status, $tagihan)
    {
        $bulanTagihan = $tagihan['bulan_tagihan'];
        $jumlahTagihan = number_format($tagihan['jumlah'], 0, ',', '.');

        switch ($status) {
            case 'Belum Bayar':
                return "Pemberitahuan: Tagihan bulan **$bulanTagihan** Anda sebesar **Rp$jumlahTagihan** belum dibayar.\nSegera lakukan pembayaran.";
            case 'Tertunggak':
                return "Pemberitahuan: Tagihan bulan **$bulanTagihan** Anda sebesar **Rp$jumlahTagihan** sudah lebih dari 10 hari dan kini berstatus **Tertunggak**. Segera lakukan pembayaran.";
            case 'Telat Bayar':
                return "Pemberitahuan: Tagihan bulan **$bulanTagihan** Anda sebesar **Rp$jumlahTagihan** sudah lebih dari 13 hari dan kini berstatus **Telat Bayar**.\n\n**Jika Anda tidak dapat membayar, harap segera konsultasikan dengan wali kelas.**";
            default:
                return null;
        }
    }

    private function sendMessage($target, $message, $token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $token,
            ),
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            echo curl_error($curl);
        }

        curl_close($curl);
        echo $response;
    }
}
