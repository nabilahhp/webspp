<?php $request = service('request'); ?>

<h4 class="mb-4">Data Tagihan Siswa</h4>

<!-- Filter Form -->
<?= form_open(base_url('staff_keuangan/data_tagihan_siswa'), ['method' => 'get']) ?>
<div class="row mb-4">
    <div class="col-md-3">
        <label for="tahun_ajaran">Tahun Ajaran</label>
        <select name="tahun_ajaran" id="tahun_ajaran" class="form-control">
            <option value="">Pilih Tahun Ajaran</option>
            <?php foreach ($tahun_ajaran as $tahun) : ?>
                <option value="<?= esc($tahun['tahun_ajaran']) ?>" <?= ($request->getGet('tahun_ajaran') == $tahun['tahun_ajaran']) ? 'selected' : '' ?>>
                    <?= esc($tahun['tahun_ajaran']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label for="kelas">Kelas</label>
        <select name="kelas" id="kelas" class="form-control">
            <option value="">Pilih Kelas</option>
            <?php foreach ($kelas as $kls) : ?>
                <option value="<?= esc($kls['nama_kelas']) ?>" <?= ($request->getGet('kelas') == $kls['nama_kelas']) ? 'selected' : '' ?>>
                    <?= esc($kls['nama_kelas']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <label>&nbsp;</label>
        <button type="submit" class="btn btn-primary form-control">Filter</button>
    </div>
</div>
<?= form_close() ?>

<!-- Table -->
<?= form_open(base_url('staff_keuangan/data_tagihan_siswa/proses'), ['id' => 'form-hapus-data']) ?>
<input type="hidden" name="pengalihan" value="<?= current_url() ?>">

<div class="table-responsive mailbox-messages">
  <table id="example11" class="display table table-bordered table-sm" cellspacing="0" width="100%">
    <thead>
      <tr class="bg-light text-center">
        <th width="5%">
          <div class="mailbox-controls">
            <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="far fa-square"></i></button>
          </div>
        </th>
        <th width="5%">No</th>
        <th width="20%">Nama Siswa</th>
        <th width="15%">Tagihan Bulan</th>
        <th width="15%">Nominal</th>
        <th width="15%">Status</th>
        <th width="20%">Tanggal Bayar</th>
        <th>Kelas</th>
        <th>Tahun Ajaran</th>
        <th width="10%">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($tagihan)) : ?>
        <?php $i = 1; foreach ($tagihan as $row) : ?>
          <tr>
            <td class="text-center">
              <div class="icheck-primary">
                <input type="checkbox" name="id_tagihan[]" value="<?= esc($row['id']) ?>" id="check<?= $i ?>">
                <label for="check<?= $i ?>"></label>
              </div>
            </td>
            <td class="text-center"><?= $i++ ?></td>
            <td><?= esc($row['nama_siswa'] ?? '-') ?></td>
            <td><?= esc($row['bulan_tagihan']); ?></td>
            <td><?= esc($row['jumlah']); ?></td>
            <td class="text-center"><?= esc(ucwords($row['status'])) ?></td>
            <td class="text-center"><?= $row['tanggal_bayar'] ? date('d-m-Y H:i', strtotime($row['tanggal_bayar'])) : '-' ?></td>
            <td class="text-center"><?= esc($row['nama_kelas'] ?? '-') ?></td>
            <td class="text-center"><?= esc($row['tahun_ajaran'] ?? '-') ?></td>
            <td class="text-center">
              <a href="<?= base_url('staff_keuangan/data_tagihan_siswa/edit/' . $row['id']) ?>" class="btn btn-warning btn-sm mb-1" title="Edit"><i class="fa fa-edit"></i></a>
              <a href="<?= base_url('staff_keuangan/data_tagihan_siswa/delete/' . $row['id']) ?>" class="btn btn-danger btn-sm mb-1 delete-link" title="Hapus"><i class="fa fa-trash"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else : ?>
        <tr>
          <td colspan="7" class="text-center">Data tagihan tidak ditemukan.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?= form_close(); ?>

<div class="clearfix">
  <hr>
</div>
<div class="pull-right"><?= $pagination ?? '' ?></div>
