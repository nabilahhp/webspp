<!-- Bootstrap JS (wajib ada agar modal bisa jalan) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
<link rel="stylesheet" href="https://printjs-4de6.kxcdn.com/print.min.css">


<h4 class="mb-4">Riwayat Pembayaran Siswa</h4>

<style>
    .filter-container {
        max-width: 400px;
        margin-bottom: 10px;
        padding: 10px 15px;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-family: Arial, sans-serif;
        font-size: 14px;
    }

    .filter-form {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        flex: 1 1 140px;
    }

    .filter-group label {
        font-weight: 600;
        margin-bottom: 4px;
        color: #222;
    }

    select {
        padding: 5px 8px;
        font-size: 13px;
        border: 1px solid #ccc;
        border-radius: 4px;
        outline-offset: 2px;
    }

    button.filter-button {
        padding: 6px 15px;
        font-size: 14px;
        background-color: #007bff;
        border: none;
        color: white;
        border-radius: 4px;
        cursor: pointer;
        flex-shrink: 0;
    }

    button.filter-button:hover {
        background-color: #0056b3;
    }
</style>

<div class="filter-container">
    <form action="<?= base_url('staff_keuangan/riwayat_tagihan/rekap') ?>" method="get" class="filter-form">
        <div class="filter-group">
            <label for="id_kelas">Kelas:</label>
            <select name="id_kelas" id="id_kelas">
                <option value="">Semua</option>
                <?php foreach ($kelasList as $kelas): ?>
                    <option value="<?= $kelas['id_kelas'] ?>" <?= (isset($filter_kelas) && $filter_kelas == $kelas['id_kelas']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($kelas['nama_kelas']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <label for="id_tahun">Tahun Ajaran:</label>
            <select name="id_tahun" id="id_tahun">
                <option value="">Semua</option>
                <?php foreach ($tahunList as $tahun): ?>
                    <option value="<?= $tahun['id_tahun'] ?>" <?= (isset($filter_tahun) && $filter_tahun == $tahun['id_tahun']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tahun['nama_tahun']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="filter-button">Filter & PDF</button>
    </form>
</div>


<div class="table-responsive">
    <table class="table table-bordered table-sm">
        <thead class="bg-light text-center">
            <tr>
                <th width="5%">No</th>
                <th>Nama Siswa</th>
                <th>Bulan Tagihan</th>
                <th>Jumlah Bayar</th>
                <th>Tanggal Bayar</th>
                <th>Bukti Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($riwayat)) : ?>
                <?php $i = 1;
                foreach ($riwayat as $row) : ?>
                    <tr>
                        <td class="text-center"><?= $i++ ?></td>
                        <td><?= esc($row['nama_siswa']) ?></td>
                        <td class="text-center"><?= esc($row['bulan_tagihan']) ?></td>
                        <td class="<?= ($row['jumlah_bayar'] == 0) ? 'text-center' : 'text-end' ?>">
                            <?= ($row['jumlah_bayar'] == 0) ? '-' : 'Rp ' . number_format($row['jumlah_bayar'], 0, ',', '.') ?>
                        </td>

                        <td class="text-center"><?= date('d-m-Y H:i', strtotime($row['tanggal_bayar'])) ?></td>
                        <td class="text-center">
                            <?php if (strtolower($row['kategori']) == 'beasiswa') : ?>
                                <span>Beasiswa</span>
                            <?php else : ?>
                                <?php if (!empty($row['id_order'])) : ?>
                                    <button
                                        class="btn btn-info btn-sm btn-lihat-bukti"
                                        data-order-id="<?= esc($row['id_order']) ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#buktiModal">
                                        Lihat Bukti
                                    </button>
                                <?php else : ?>
                                    <span>-</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="6" class="text-center">Belum ada riwayat pembayaran.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Bukti -->
<div class="modal fade" id="buktiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div id="loading-spinner" class="text-center my-3" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                </div>

                <div id="bukti-content" style="display: none;">
                    <table class="table table-sm table-bordered">
                        <tbody>
                            <tr>
                                <th>ID Order</th>
                                <td id="bukti-id_order"></td>
                            </tr>
                            <tr>
                                <th>Bulan Tagihan</th>
                                <td id="bukti-bulan_tagihan"></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td id="bukti-status"></td>
                            </tr>
                            <tr>
                                <th>Jumlah Bayar</th>
                                <td id="bukti-jumlah"></td>
                            </tr>
                            <tr>
                                <th>Metode</th>
                                <td id="bukti-metode"></td>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <td id="bukti-tanggal"></td>
                            </tr>
                            <tr>
                                <th>Transaksi ID</th>
                                <td id="bukti-transaksi"></td>
                            </tr>
                            <tr>
                                <th>Response Message</th>
                                <td id="bukti-response"></td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Tombol Cetak PDF -->
                    <div class="text-end mt-3">
                        <button type="button" class="btn btn-danger btn-sm"
                            onclick="printJS({ 
                printable: 'bukti-content', 
                type: 'html', 
                header: 'Bukti Pembayaran', 
                css: ['https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'] 
            })">
                            Cetak PDF
                        </button>
                    </div>
                </div>


                <div id="bukti-error" class="alert alert-danger d-none" role="alert">
                    Gagal mengambil data bukti pembayaran.
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.btn-lihat-bukti');

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                const idOrder = button.getAttribute('data-order-id');

                // Show spinner & hide others
                document.getElementById('loading-spinner').style.display = 'block';
                document.getElementById('bukti-content').style.display = 'none';
                document.getElementById('bukti-error').classList.add('d-none');

                fetch(`<?= base_url('staff_keuangan/riwayat_tagihan/bukti/') ?>${idOrder}`)
                    .then(res => {
                        if (!res.ok) throw new Error('Tidak ditemukan');
                        return res.json();
                    })
                    .then(data => {
                        document.getElementById('bukti-id_order').textContent = data.id_order || '-';
                        document.getElementById('bukti-bulan_tagihan').textContent = data.bulan_tagihan || '-';
                        document.getElementById('bukti-status').textContent = data.status || '-';
                        document.getElementById('bukti-jumlah').textContent = 'Rp ' + parseInt(data.jumlah_bayar).toLocaleString('id-ID');
                        document.getElementById('bukti-metode').textContent = data.metode_pembayaran || '-';
                        document.getElementById('bukti-tanggal').textContent = data.tanggal_bayar || '-';
                        document.getElementById('bukti-transaksi').textContent = data.response_data?.transaction_id || '-';
                        document.getElementById('bukti-response').textContent = data.response_message || '-';

                        // Show table, hide spinner
                        document.getElementById('loading-spinner').style.display = 'none';
                        document.getElementById('bukti-content').style.display = 'block';
                    })

                    .catch(error => {
                        document.getElementById('loading-spinner').style.display = 'none';
                        document.getElementById('bukti-error').classList.remove('d-none');
                        console.error(error);
                    });
            });
        });
    });
</script>