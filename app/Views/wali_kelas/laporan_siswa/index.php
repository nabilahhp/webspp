<?php $request = service('request'); ?>

<h4 class="mb-4"><?= esc($title) ?></h4>

<?= form_open(base_url('wali_kelas/laporan_tagihan/proses'), ['id' => 'form-hapus-data']) ?>
<input type="hidden" name="pengalihan" value="<?= current_url() ?>">

<div class="table-responsive mailbox-messages">
  <table id="example11" class="display table table-bordered table-sm" cellspacing="0" width="100%">
    <thead>
      <tr class="bg-light text-center">
        <th width="5%">No</th>
        <th width="20%">Nama Siswa</th>
        <th width="15%">Tagihan Bulan</th>
        <th width="15%">Nominal</th>
        <th width="15%">Status</th>
        <th width="20%">Tanggal Bayar</th>
        <th>Kelas</th>
        <th>Tahun Ajaran</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($tagihan)) : ?>
        <?php $no = $i;
        foreach ($tagihan as $row) : ?>
          <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td><?= esc($row['nama_siswa'] ?? '-') ?></td>
            <td class="text-center"><?= esc($row['bulan_tagihan']) ?></td>
            <td class="text-end">Rp<?= number_format($row['jumlah'], 0, ',', '.') ?></td>
            <td class="text-center">
              <?php if (strtolower($row['status']) == 'telat') : ?>
                <span class="badge bg-danger">Telat</span>
              <?php elseif (strtolower($row['status']) == 'belum') : ?>
                <span class="badge bg-warning text-dark">Belum Bayar</span>
              <?php else : ?>
                <span class="badge bg-secondary"><?= esc(ucwords($row['status'])) ?></span>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <?= $row['tanggal_bayar'] ? date('d-m-Y H:i', strtotime($row['tanggal_bayar'])) : '-' ?>
            </td>
            <td class="text-center"><?= esc($row['nama_kelas'] ?? '-') ?></td>
            <td class="text-center"><?= esc($row['tahun_ajaran'] ?? '-') ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else : ?>
        <tr>
          <td colspan="8" class="text-center">Data tagihan belum tersedia.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?= form_close(); ?>

<div class="clearfix">
  <hr>
</div>
<div class="text-end"><?= $pagination ?? '' ?></div>