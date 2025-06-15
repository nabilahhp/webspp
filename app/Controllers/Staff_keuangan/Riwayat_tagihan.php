<?php

namespace App\Controllers\Staff_keuangan;

use App\Controllers\BaseController;
use App\Models\Riwayat_pembayaran_model;
use App\Models\Siswa_model;
use App\Models\Input_tagihan_model;
use App\Models\Kelas_model;
use App\Models\Tahun_model;
use Dompdf\Dompdf;
use Dompdf\Options;

class Riwayat_tagihan extends BaseController
{
    protected $riwayatModel;
    protected $siswaModel;
    protected $inputTagihanModel;
    protected $orderModel;
    protected $kelasModel;
    protected $tahunModel;
    public function __construct()
    {
        $this->riwayatModel = new Riwayat_pembayaran_model();
        $this->siswaModel = new Siswa_model();
        $this->kelasModel = new Kelas_model();
        $this->tahunModel = new Tahun_model();
        $this->inputTagihanModel = new Input_tagihan_model();
        $this->orderModel = new \App\Models\Order_model();
        helper('format');  // di __construct atau di method rekap()

    }

    public function index()
    {
        // Ambil input filter dari request (GET atau POST)
        $id_kelas = $this->request->getGet('id_kelas');
        $id_tahun = $this->request->getGet('id_tahun');

        $builder = $this->riwayatModel
            ->select('riwayat_pembayaran.*, siswa.nama_siswa, siswa.kategori, siswa.id_kelas, siswa.id_tahun, input_tagihan.bulan_tagihan, orders.id_order')
            ->join('siswa', 'siswa.id_siswa = riwayat_pembayaran.id_siswa')
            ->join('tagihan', 'tagihan.id = riwayat_pembayaran.id_tagihan')
            ->join('input_tagihan', 'input_tagihan.id = tagihan.id_input_tagihan')
            ->join('orders', 'orders.id_tagihan = tagihan.id', 'left') // left join karena orders bisa kosong
            ->where('tagihan.status', 'Lunas')
            ->orderBy('riwayat_pembayaran.tanggal_bayar', 'DESC');

        // Filter berdasarkan kelas jika ada
        if ($id_kelas) {
            $builder->where('siswa.id_kelas', $id_kelas);
        }

        // Filter berdasarkan tahun ajaran jika ada
        if ($id_tahun) {
            $builder->where('siswa.id_tahun', $id_tahun);
        }

        $riwayat = $builder->findAll();

        // Load model kelas dan tahun untuk data dropdown filter (opsional)
        $kelasModel = new \App\Models\Kelas_model();
        $tahunModel = new \App\Models\Tahun_model();

        $data = [
            'title' => 'Riwayat Pembayaran Siswa',
            'riwayat' => $riwayat,
            'kelasList' => $kelasModel->findAll(),
            'tahunList' => $tahunModel->findAll(),
            'filter_kelas' => $id_kelas ? $kelasModel->find($id_kelas) : null,
            'filter_tahun' => $id_tahun ? $tahunModel->find($id_tahun) : null,
            'content' => 'staff_keuangan/riwayat_pembayaran/index',
        ];

        return view('staff_keuangan/layout/wrapper', $data);
    }


    public function bukti($idOrder)
    {
        // Query dengan join untuk mengambil data order + bulan_tagihan
        $order = $this->orderModel
            ->select('orders.*, input_tagihan.bulan_tagihan')
            ->join('tagihan', 'tagihan.id = orders.id_tagihan')
            ->join('input_tagihan', 'input_tagihan.id = tagihan.id_input_tagihan')
            ->where('orders.id_order', $idOrder)
            ->first();

        if (!$order) {
            log_message('error', 'Bukti pembayaran tidak ditemukan untuk id_order: ' . $idOrder);
            return $this->response->setJSON(['error' => 'Data tidak ditemukan'])->setStatusCode(404);
        }

        // Decode response_data jika ada
        if (!empty($order['response_data'])) {
            $decoded = json_decode($order['response_data'], true);
            $order['response_data'] = $decoded ?? $order['response_data'];
        }

        return $this->response->setJSON($order);
    }

    private function konversiBulanKeIndonesia($bulanInggris)
    {
        $map = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember',
        ];

        return $map[$bulanInggris] ?? $bulanInggris;
    }


    public function rekap()
    {
        helper('format');  // pastikan helper format sudah tersedia

        $siswaModel = new \App\Models\Siswa_model();
        $tagihanModel = new \App\Models\Tagihan_model();
        $riwayatModel = new \App\Models\Riwayat_pembayaran_model();

        // Ambil data kelas dan tahun dari model
        $kelasList = $this->kelasModel->findAll();
        $tahunList = $this->tahunModel->findAll();

        // Ambil filter dari request GET
        $id_kelas = $this->request->getGet('id_kelas');
        $id_tahun = $this->request->getGet('id_tahun');

        // Query siswa dengan join kelas dan tahun, dan filter jika ada
        $siswaQuery = $siswaModel
            ->select('siswa.*, tahun.nama_tahun, kelas.nama_kelas')
            ->join('tahun', 'tahun.id_tahun = siswa.id_tahun')
            ->join('kelas', 'kelas.id_kelas = siswa.id_kelas');

        if ($id_kelas) {
            $siswaQuery->where('siswa.id_kelas', $id_kelas);
        }
        if ($id_tahun) {
            $siswaQuery->where('siswa.id_tahun', $id_tahun);
        }

        $siswaList = $siswaQuery->findAll();

        $bulanList = ['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];

        $rekapData = [];

        foreach ($siswaList as $siswa) {
            // proses rekap sama seperti sebelumnya
            if (strtolower($siswa['kategori']) === 'beasiswa') {
                $bulanan = [];
                foreach ($bulanList as $bln) {
                    $bulanan[$bln] = '-';
                }

                $rekapData[] = [
                    'nis' => $siswa['nis'],
                    'nama' => $siswa['nama_siswa'],
                    'kelas' => $siswa['nama_kelas'],
                    'tahun' => $siswa['nama_tahun'],
                    'bulanan' => $bulanan,
                    'total_tagihan' => 0,
                    'total_bayar' => 0,
                    'sisa_tagihan' => 0,
                ];
                continue;
            }

            $tagihanAll = $tagihanModel
                ->select('tagihan.*, input_tagihan.bulan_tagihan, input_tagihan.jumlah as nominal_tagihan')
                ->join('input_tagihan', 'input_tagihan.id = tagihan.id_input_tagihan')
                ->where('tagihan.id_siswa', $siswa['id_siswa'])
                ->findAll();

            $riwayatAll = $riwayatModel
                ->where('id_siswa', $siswa['id_siswa'])
                ->findAll();

            $bulanan = [];
            foreach ($bulanList as $bln) {
                $bulanan[$bln] = '-';
            }

            $tagihanPerBulan = [];
            foreach ($tagihanAll as $t) {
                $bln = explode(' ', $t['bulan_tagihan'])[0];
                $tagihanPerBulan[$bln] = $t['nominal_tagihan'];
                if (!isset($bulanan[$bln])) {
                    $bulanan[$bln] = '-';
                }
            }

            $totalTagihan = array_sum($tagihanPerBulan);

            $bayarPerBulan = [];
            foreach ($riwayatAll as $r) {
                $blnBayar = nama_bulan_indo($r['tanggal_bayar']);
                if (isset($bayarPerBulan[$blnBayar])) {
                    $bayarPerBulan[$blnBayar] += $r['jumlah_bayar'];
                } else {
                    $bayarPerBulan[$blnBayar] = $r['jumlah_bayar'];
                }
            }

            foreach ($bulanList as $bln) {
                if (isset($bayarPerBulan[$bln])) {
                    $bulanan[$bln] = number_format($bayarPerBulan[$bln], 0, ',', '.');
                } else {
                    if (isset($tagihanPerBulan[$bln])) {
                        $bulanan[$bln] = number_format($tagihanPerBulan[$bln], 0, ',', '.');
                    } else {
                        $bulanan[$bln] = '-';
                    }
                }
            }

            $totalBayar = array_sum($bayarPerBulan ?? []);

            $rekapData[] = [
                'nis' => $siswa['nis'],
                'nama' => $siswa['nama_siswa'],
                'kelas' => $siswa['nama_kelas'],
                'tahun' => $siswa['nama_tahun'],
                'bulanan' => $bulanan,
                'total_tagihan' => $totalTagihan,
                'total_bayar' => $totalBayar,
                'sisa_tagihan' => $totalTagihan - $totalBayar,
            ];
        }

        // Fungsi bantu untuk dapatkan nama kelas dari ID
        function getNamaKelasById($kelasList, $id)
        {
            foreach ($kelasList as $item) {
                if ($item['id_kelas'] == $id) {
                    return $item['nama_kelas'];
                }
            }
            return null;
        }

        // Fungsi bantu untuk dapatkan nama tahun dari ID
        function getNamaTahunById($tahunList, $id)
        {
            foreach ($tahunList as $item) {
                if ($item['id_tahun'] == $id) {
                    return $item['nama_tahun'];
                }
            }
            return null;
        }

        $namaKelas = $id_kelas ? getNamaKelasById($kelasList, $id_kelas) : 'Semua Kelas';
        $namaTahun = $id_tahun ? getNamaTahunById($tahunList, $id_tahun) : 'Semua Tahun';

        $data = [
            'judul' => 'Rekap Pembayaran',
            'jenis' => 'SPP',
            'rekap' => $rekapData,
            'filter_kelas' => $id_kelas,
            'filter_tahun' => $id_tahun,
            'namaKelas' => $namaKelas,
            'namaTahun' => $namaTahun,
        ];

        $html = view('staff_keuangan/riwayat_pembayaran/rekap_pdf', $data);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Rekap_Pembayaran.pdf", ["Attachment" => false]);
        exit();
    }
}
