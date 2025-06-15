<div class="table-responsive mailbox-messages">
    <table class="table table-bordered table-sm">
        <thead>
            <tr class="text-center bg-light">
                <th>No</th>
                <th>Bulan Tagihan</th>
                <th>Jumlah Dibayar</th>
                <th>Tanggal Dibayar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            function nama_bulan_indo($tanggal)
            {
                // Format tanggal bisa 'Y-m-d' atau 'Y-m-d H:i:s', ambil bagian tanggalnya saja
                $bulan = date('m', strtotime($tanggal));

                $bulan_indo = [
                    '01' => 'Januari',
                    '02' => 'Februari',
                    '03' => 'Maret',
                    '04' => 'April',
                    '05' => 'Mei',
                    '06' => 'Juni',
                    '07' => 'Juli',
                    '08' => 'Agustus',
                    '09' => 'September',
                    '10' => 'Oktober',
                    '11' => 'November',
                    '12' => 'Desember',
                ];

                return $bulan_indo[$bulan] ?? 'Bulan tidak diketahui';
            }
            ?>

            <?php $no = 1; ?>
            <?php foreach ($riwayat as $row) : ?>
                <tr class="text-center">
                    <td><?= $no++ ?></td>
                    <td><?= isset($row['bulan_tagihan']) ? $row['bulan_tagihan'] : '-' ?></td> <!-- Menampilkan bulan_tagihan dari input_tagihan -->
                    <td>Rp<?= number_format((float)($row['jumlah_bayar'] ?? 0), 0, ',', '.') ?></td>
                    <td><?= date('d-m-Y', strtotime($row['tanggal_bayar'])) ?></td>
                    <td>
                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#paymentModal<?= $row['id'] ?>">
                            Lihat Bukti Pembayaran
                        </button>
                    </td>
                </tr>

                <!-- Modal Pop-up -->
                <div class="modal fade" id="paymentModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="paymentModalLabel<?= $row['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="paymentModalLabel<?= $row['id'] ?>">Bukti Pembayaran: <?= $row['invoice_number'] ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <h6>Detail Pembayaran</h6>
                                <p><strong>Status:</strong> <?= $row['status'] === 'Lunas' ? 'Lunas' : 'Gagal' ?></p>
                                <p><strong>Metode Pembayaran:</strong> <?= $row['metode_pembayaran'] ?></p>
                                <p><strong>Tanggal Pembayaran:</strong> <?= date('d-m-Y', strtotime($row['tanggal_bayar'])) ?></p>
                                <p><strong>Jumlah Dibayar:</strong> Rp<?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></p>

                                <!-- Informasi tambahan jika ada -->
                                <p><strong>Invoice Number:</strong> <?= $row['invoice_number'] ?></p>
                                <p><strong>Response Message:</strong> <?= htmlspecialchars($row['response_message'] ?? 'Tidak ada pesan') ?></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <!-- Print Button -->
                                <button type="button" class="btn btn-primary" onclick="printModal('paymentModal<?= $row['id'] ?>')">Print</button>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    function printModal(modalId) {
        // Ambil konten hanya dari modal-body (untuk menghindari header/footer modal yang tidak perlu)
        var modalContent = document.getElementById(modalId).querySelector('.modal-body').innerHTML;

        // Membuka jendela baru untuk mencetak
        var printWindow = window.open('', '', 'height=600,width=800');

        // Cek jika jendela tidak terbuka (untuk menangani pemblokiran popup)
        if (!printWindow) {
            alert("Pop-up blocker is enabled. Please disable it to print.");
            return;
        }

        // Tulis HTML untuk jendela cetak
        printWindow.document.write('<html><head><title>Print</title>');

        // Menambahkan CSS untuk memastikan elemen tidak ikut tercetak
        printWindow.document.write('<style>body{font-family: Arial, sans-serif;} h2 {text-align: center;} .modal-footer, .btn {display: none;}</style>');

        // Menulis konten modal yang akan dicetak
        printWindow.document.write('</head><body>');
        printWindow.document.write('<h2>Detail Pembayaran</h2>'); // Menambahkan judul untuk cetakan
        printWindow.document.write(modalContent); // Menulis konten modal
        printWindow.document.write('</body></html>');

        // Menutup dokumen setelah menulis semua konten
        printWindow.document.close();

        // Memastikan jendela siap sebelum memulai pencetakan
        printWindow.focus(); // Memberi fokus ke jendela yang baru
        printWindow.print(); // Memulai pencetakan
    }
</script>

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>