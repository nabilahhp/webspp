<?php

namespace App\Controllers\Siswa;

use CodeIgniter\Controller;
use App\Models\Tagihan_model;
use App\Models\Riwayat_pembayaran_model;
use App\Models\Siswa_model;
use App\Models\Order_model;

class Payment extends Controller
{
    private $clientId = 'BRN-0277-1747654294964';
    private $sharedKey = 'SK-1VFsbIhOpxbtxls43FFw';
    private $sandbox = false;

    public function create()
    {
        // Terima data dari frontend
        $input = json_decode(file_get_contents('php://input'), true);

        $invoiceNumber = $input['invoice_number'];
        $amount = $input['amount'] ?? 0;
        $idSiswa = $input['id_siswa'];
        $idTagihan = $input['id_tagihan'];


        // Cek apakah sudah ada order dengan invoice_number yang sama
        $orderModel = new Order_model();
        $existingOrder = $orderModel->where('invoice_number', $invoiceNumber)->first();

        if ($existingOrder) {
            // Jika order sudah ada, cek status pembayaran
            if ($existingOrder['status'] === 'Lunas') {
                // Jika status sudah Lunas, beri respons bahwa pembayaran sudah berhasil
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Order dengan invoice number ini sudah Lunas dan tidak perlu diproses lagi.'
                ]);
            } else {
                // Jika order sudah ada dan status belum Lunas, lanjutkan ke pembayaran Doku
                // Proses pembayaran Doku...
                $requestId = uniqid();
                $timestamp = gmdate("Y-m-d\TH:i:s\Z");
                $path = '/checkout/v1/payment';

                $body = [
                    'order' => [
                        'amount' => $amount,
                        'invoice_number' => $invoiceNumber
                    ],
                    'payment' => [
                        'payment_due_date' => 60
                    ]
                ];

                $jsonBody = json_encode($body, JSON_UNESCAPED_SLASHES);
                $digest = base64_encode(hash('sha256', $jsonBody, true));

                $rawSignature = "Client-Id:{$this->clientId}\n"
                    . "Request-Id:$requestId\n"
                    . "Request-Timestamp:$timestamp\n"
                    . "Request-Target:$path\n"
                    . "Digest:$digest";

                $signature = base64_encode(hash_hmac('sha256', $rawSignature, $this->sharedKey, true));

                $headers = [
                    "Content-Type: application/json",
                    "Client-Id: {$this->clientId}",
                    "Request-Id: $requestId",
                    "Request-Timestamp: $timestamp",
                    "Signature: HMACSHA256=$signature",
                    "Digest: SHA-256=$digest"
                ];

                $url = $this->sandbox
                    ? "https://api-sandbox.doku.com$path"
                    : "https://api.doku.com$path";

                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_HTTPHEADER => $headers,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $jsonBody,
                    CURLOPT_RETURNTRANSFER => true
                ]);

                $response = curl_exec($curl);
                curl_close($curl);

                $result = json_decode($response, true);
                log_message('info', 'Response from Doku: ' . json_encode($result));

                // Jika berhasil mendapatkan payment URL dari Doku
                if (isset($result['response']['payment']['url'])) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'paymentUrl' => $result['response']['payment']['url']
                    ]);
                }

                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal mendapatkan payment URL',
                    'response' => $result
                ]);
            }
        } else {
            // Jika order tidak ada, simpan order baru dan lanjutkan ke pembayaran Doku
            $orderData = [
                'id_siswa' => $idSiswa,
                'id_tagihan' => $idTagihan,
                'status' => 'Menunggu Pembayaran', // Status awal
                'invoice_number' => $invoiceNumber,
                'jumlah_bayar' => $amount,
                'metode_pembayaran' => 'Doku',
                'response_code' => '',
                'response_message' => '',
                'response_data' => '',
                'tanggal_bayar' => null, // Belum ada tanggal bayar
                'created_at' => date('Y-m-d H:i:s'),
            ];

            // Simpan ke model dan dapatkan order_id yang auto increment
            $orderId = $orderModel->insert($orderData);  // $orderId adalah order_id yang auto increment

            if (!$orderId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal membuat order'
                ]);
            }

            // Lanjutkan ke Doku API untuk membuat pembayaran
            $requestId = uniqid();
            $timestamp = gmdate("Y-m-d\TH:i:s\Z");
            $path = '/checkout/v1/payment';

            $body = [
                'order' => [
                    'amount' => $amount,
                    'invoice_number' => $invoiceNumber
                ],
                'payment' => [
                    'payment_due_date' => 60
                ]
            ];

            $jsonBody = json_encode($body, JSON_UNESCAPED_SLASHES);
            $digest = base64_encode(hash('sha256', $jsonBody, true));

            $rawSignature = "Client-Id:{$this->clientId}\n"
                . "Request-Id:$requestId\n"
                . "Request-Timestamp:$timestamp\n"
                . "Request-Target:$path\n"
                . "Digest:$digest";

            $signature = base64_encode(hash_hmac('sha256', $rawSignature, $this->sharedKey, true));

            $headers = [
                "Content-Type: application/json",
                "Client-Id: {$this->clientId}",
                "Request-Id: $requestId",
                "Request-Timestamp: $timestamp",
                "Signature: HMACSHA256=$signature",
                "Digest: SHA-256=$digest"
            ];

            $url = $this->sandbox
                ? "https://api-sandbox.doku.com$path"
                : "https://api.doku.com$path";

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $jsonBody,
                CURLOPT_RETURNTRANSFER => true
            ]);

            $response = curl_exec($curl);
            curl_close($curl);

            $result = json_decode($response, true);

            // Jika berhasil mendapatkan payment URL dari Doku
            if (isset($result['response']['payment']['url'])) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'paymentUrl' => $result['response']['payment']['url']
                ]);
            }

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mendapatkan payment URL',
                'response' => $result
            ]);
        }
    }



    public function paymentNotification()
    {
        // Terima data yang dikirim Doku
        $input = json_decode(file_get_contents('php://input'), true);

        // Log untuk memverifikasi data yang diterima
        log_message('info', 'Payment Notification Data: ' . json_encode($input));

        // Pastikan status pembayaran berhasil (status_code 'SUCCESS' dalam response dari Doku)
        if (!isset($input['transaction']['status']) || $input['transaction']['status'] !== 'SUCCESS') {
            log_message('error', 'Payment failed: ' . json_encode($input));
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Payment failed.'
            ]);
        }

        // Mendapatkan data penting dari response Doku
        $invoiceNumber = $input['order']['invoice_number'];  // Ambil invoice_number dari notifikasi
        log_message('info', 'Received invoice_number: ' . $invoiceNumber); // Log invoice_number yang diterima

        $paymentStatus = $input['transaction']['status'];  // Status pembayaran
        $paymentDate = date('Y-m-d H:i:s');  // Waktu pembayaran yang akan disimpan
        $amount = $input['order']['amount'];  // Jumlah pembayaran yang diterima

        // Ambil nama channel dari body Doku
        $channelName = $input['channel']['name'] ?? 'Unknown Channel';  // Ambil nama channel (default ke 'Unknown Channel' jika tidak ada)

        // Verifikasi order berdasarkan invoice_number yang dikirim Doku
        $orderModel = new Order_model();
        $order = $orderModel->where('invoice_number', $invoiceNumber)->first();  // Cari order berdasarkan invoice_number

        if (!$order) {
            // Jika order tidak ditemukan, kirimkan error response
            log_message('error', 'Order not found for invoice_number: ' . $invoiceNumber);
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Order not found.'
            ]);
        }

        // Log jika order ditemukan
        log_message('info', 'Order found: ' . json_encode($order));

        // Periksa apakah status order sudah 'Lunas', jika sudah, beri respon duplikat
        if ($order['status'] === 'Lunas') {
            log_message('info', 'Duplicate request for invoice_number: ' . $invoiceNumber);
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Duplicate request. Payment already processed.'
            ]);
        }

        // Update status pembayaran di tabel orders dan ganti metode_pembayaran dengan channel.name dari Doku
        $updateOrderStatus = $orderModel->update($order['id_order'], [
            'status' => ($paymentStatus === 'SUCCESS') ? 'Lunas' : 'Gagal',
            'tanggal_bayar' => ($paymentStatus === 'SUCCESS') ? $paymentDate : null,
            'response_code' => $paymentStatus,  // Menyimpan status pembayaran (misal "SUCCESS")
            'response_message' => "Payment processed successfully",  // Pesan response yang relevan
            'response_data' => json_encode($input),  // Menyimpan seluruh data respons dari Doku
            'metode_pembayaran' => $channelName  // Update metode pembayaran dengan nama channel
        ]);

        if (!$updateOrderStatus) {
            log_message('error', 'Failed to update order status for order_id: ' . $order['id_order']);
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update order status.'
            ]);
        } else {
            log_message('info', 'Order status updated successfully for order_id: ' . $order['id_order']);
        }

        // **Update Tagihan Status**
        // Perbarui status tagihan menjadi 'Lunas' jika pembayaran berhasil
        if ($paymentStatus === 'SUCCESS') {
            $tagihanModel = new Tagihan_model();
            $updateTagihan = $tagihanModel->update($order['id_tagihan'], [
                'status' => 'Lunas',
                'tanggal_bayar' => $paymentDate  // Update tanggal bayar
            ]);

            if (!$updateTagihan) {
                log_message('error', 'Failed to update tagihan status for tagihan_id: ' . $order['id_tagihan']);
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to update tagihan status.'
                ]);
            } else {
                log_message('info', 'Tagihan status updated successfully for tagihan_id: ' . $order['id_tagihan']);
            }
        }

        // **Insert Riwayat Pembayaran**
        // Ambil ID siswa terkait dengan order
        $siswaModel = new Siswa_model();
        $siswa = $siswaModel->find($order['id_siswa']);  // Ambil data siswa terkait dengan order

        if ($siswa) {
            // Menambahkan data riwayat pembayaran
            $dataRiwayatPembayaran = [
                'id_siswa' => $siswa['id_siswa'],
                'id_tagihan' => $order['id_tagihan'],
                'tanggal_bayar' => $paymentDate,
                'jumlah_bayar' => $amount,
                'id_order' => $order['id_order'],
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Insert riwayat pembayaran ke tabel
            $riwayatPembayaranModel = new Riwayat_pembayaran_model();
            $riwayatPembayaranModel->insert($dataRiwayatPembayaran);

            log_message('info', 'Riwayat pembayaran berhasil ditambahkan untuk siswa ID: ' . $siswa['id_siswa']);
        } else {
            log_message('error', 'Siswa tidak ditemukan untuk order_id: ' . $order['id_order']);
        }

        // Kirimkan response sukses ke Doku
        log_message('info', 'Payment notification processed successfully for invoice_number: ' . $invoiceNumber);
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Payment notification received and processed successfully.'
        ]);
    }
}
