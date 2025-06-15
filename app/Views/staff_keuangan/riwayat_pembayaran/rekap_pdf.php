<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 4px;
        font-size: 12px;
        text-align: center;
    }

    th {
        background-color: #eee;
    }
</style>


<h2 style="text-align: center;">Rekap Pembayaran</h2>
<p>Jenis Pembayaran: <?= $jenis ?></p>
<p>Filter Kelas: <?= esc($namaKelas ?? 'Semua Kelas') ?></p>
<p>Filter Tahun: <?= esc($namaTahun ?? 'Semua Tahun') ?></p>

<table border="1" cellspacing="0" cellpadding="4" width="100%">
    <thead>
        <tr style="background: #eee;">
            <th>No</th>
            <th>NIS</th>
            <th>Nama Siswa</th>
            <?php foreach (['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'] as $bulan): ?>
                <th><?= $bulan ?></th>
            <?php endforeach; ?>
            <th>Total Dibayar</th>
            <th>Total Tagihan</th>
            <th>Sisa</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1;
        $grandTagihan = 0;
        $grandBayar = 0;
        foreach ($rekap as $r): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= $r['nis'] ?></td>
                <td><?= $r['nama'] ?></td>
                <?php foreach (['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'] as $bulan): ?>
                    <td><?= $r['bulanan'][$bulan] ?? '-' ?></td>
                <?php endforeach; ?>
                <td><?= number_format($r['total_bayar'], 0, ',', '.') ?></td>
                <td><?= number_format($r['total_tagihan'], 0, ',', '.') ?></td>
                <td><?= number_format($r['sisa_tagihan'], 0, ',', '.') ?></td>
            </tr>
            <?php $grandTagihan += $r['total_tagihan'];
            $grandBayar += $r['total_bayar']; ?>
        <?php endforeach; ?>
        <tr>
            <td colspan="15" align="right"><strong>Grand Total</strong></td>
            <td><strong><?= number_format($grandBayar, 0, ',', '.') ?></strong></td>
            <td><strong><?= number_format($grandTagihan, 0, ',', '.') ?></strong></td>
            <td><strong><?= number_format($grandTagihan - $grandBayar, 0, ',', '.') ?></strong></td>
        </tr>
    </tbody>
</table>