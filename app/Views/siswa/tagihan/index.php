<?php
// Menonaktifkan X-Frame-Options agar halaman bisa ditampilkan di dalam iframe
header("X-Frame-Options: ALLOWALL");

// Kode PHP lainnya bisa di sini

?>
<script src="https://jokul.doku.com/jokul-checkout-js/v1/jokul-checkout-1.0.0.js"></script>
<div class="table-responsive mailbox-messages">
    <table class="table table-bordered table-sm">
        <thead>
            <tr class="text-center bg-light">
                <th>No</th>
                <th>Bulan Tagihan</th>
                <th>Nominal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($tagihan as $row) : ?>
                <tr class="text-center">
                    <td><?= $no++ ?></td>
                    <td><?= esc($row['bulan_tagihan']) ?></td>
                    <td>Rp<?= number_format((float)($row['jumlah'] ?? 0), 0, ',', '.') ?></td>
                    <td>
                        <?php if ($row['status'] == 'Lunas') : ?>
                            <span class="badge bg-success">Lunas</span>
                        <?php elseif ($row['status'] == 'Telat Bayar') : ?>
                            <span class="badge bg-danger">Telat Bayar - Hubungi Wali Kelas</span>
                        <?php elseif ($row['status'] == 'Tertunggak') : ?>
                            <button
                                type="button"
                                class="btn btn-danger btn-sm btn-bayar"
                                data-id="<?= $row['id'] ?>"
                                data-amount="<?= (int)$row['jumlah'] ?>"
                                data-invoice="INV<?= $row['id'] ?>"
                                data-id-siswa="<?= $id_siswa ?>"> <!-- Menambahkan data-id-siswa -->
                                Tertunggak - Bayar Sekarang
                            </button>
                        <?php else : ?>
                            <button
                                type="button"
                                class="btn btn-warning btn-sm btn-bayar"
                                data-id="<?= $row['id'] ?>"
                                data-amount="<?= (int)$row['jumlah'] ?>"
                                data-invoice="INV<?= $row['id'] ?>"
                                data-id-siswa="<?= $id_siswa ?>"> <!-- Menambahkan data-id-siswa -->
                                Belum Terbayar - Bayar Sekarang
                            </button>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                            data-bs-target="#detailModal<?= $row['id'] ?>">
                            Detail
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Section (di luar table) -->
<?php foreach ($tagihan as $row) : ?>
    <div class="modal fade" id="detailModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="detailModalLabel<?= $row['id'] ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel<?= $row['id'] ?>">Detail Tagihan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start">
                    <?= nl2br(htmlspecialchars($row['detail'] ?? 'Tidak ada detail')) ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".btn-bayar").forEach(function(button) {
            button.addEventListener("click", function() {
                const invoiceNumber = this.getAttribute("data-invoice");
                const amount = this.getAttribute("data-amount");
                const idSiswa = this.getAttribute("data-id-siswa"); // Ambil id_siswa dari data-id-siswa
                const idTagihan = this.getAttribute("data-id"); // Ambil id_tagihan dari data-id

                fetch("<?= base_url('siswa/payment/create') ?>", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            invoice_number: invoiceNumber,
                            amount: parseInt(amount),
                            id_siswa: idSiswa, // Kirim id_siswa ke backend
                            id_tagihan: idTagihan // Kirim id_tagihan ke backend
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            loadJokulCheckout(data.paymentUrl);
                        } else {
                            alert("Gagal membuat pembayaran: " + data.message);
                            console.error(data.response);
                        }
                    })
                    .catch(error => {
                        alert("Terjadi kesalahan saat membuat pembayaran.");
                        console.error("Error:", error);
                    });
            });
        });
    });
</script>

<!-- Bootstrap 5 CSS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>