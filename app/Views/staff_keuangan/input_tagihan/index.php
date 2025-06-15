<?php $request = service('request'); ?>

<!-- Form pencarian -->
<form action="<?= base_url('staff_keuangan/input_tagihan') ?>" method="get" accept-charset="utf-8">
  <div class="row">
    <div class="col-md-7">
      <div class="input-group">
        <input type="text" name="keywords" class="form-control" placeholder="Ketik kata kunci pencarian Tagihan...."
          value="<?= esc($request->getGet('keywords') ?? '') ?>" required>
        <span class="input-group-btn">
          <button type="submit" class="btn btn-secondary btn-flat"><i class="fa fa-search"></i></button>
          <button type="button" class="btn btn-success" data-toggle="modal" data-target="#tambahTagihanModal">
            <i class="fa fa-plus"></i> Tambah Tagihan Baru
          </button>
        </span>
      </div>
    </div>
    <div class="col-md-5 text-left">
      <?= isset($pagination) ? str_replace('index.php/', '', $pagination) : '' ?>
    </div>
  </div>
</form>

<div class="clearfix">
  <hr>
</div>

<!-- Tombol kembali jika pencarian -->
<div class="row">
  <div class="col-md-6">
    <?php if ($request->getGet('page') || $request->getGet('keywords')) : ?>
      <a href="<?= base_url('admin/input_tagihan') ?>" class="btn btn-light btn-sm">
        <i class="fa fa-arrow-circle-left"></i> Kembali
      </a>
    <?php endif; ?>
  </div>
</div>

<div class="clearfix">
  <hr>
</div>

<!-- Form bulk hapus -->
<?= form_open(base_url('staff_keuangan/input_tagihan/proses'), ['id' => 'form-hapus-data']) ?>
<input type="hidden" name="pengalihan" value="<?= current_url() ?>">

<!-- Tabel tagihan -->
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
        <th width="20%">Nama Staf</th>
        <th width="15%">Bulan Tagihan</th>
        <th width="10%">Jumlah</th>
        <th width="20%">Detail</th>
        <th width="15%">Dibuat Pada</th>
        <th width="10%">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($tagihan)) : ?>
        <?php $i = 1;
        foreach ($tagihan as $row) : ?>
          <tr>
            <td class="text-center">
              <div class="icheck-primary">
                <input type="checkbox" name="id_tagihan[]" value="<?= esc($row['id']) ?>" id="check<?= $i ?>">
                <label for="check<?= $i ?>"></label>
              </div>
            </td>
            <td class="text-center"><?= $i++ ?></td>
            <td><?= esc($row['nama_staff']) ?></td>
            <td class="text-center"><?= esc($row['bulan_tagihan']) ?></td>
            <td class="text-right">Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
            <td><?= esc($row['detail']) ?></td>
            <td class="text-center"><?= date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>
            <td class="text-center">
              <a href="<?= base_url('admin/input_tagihan/detail/' . $row['id']) ?>" class="btn btn-info btn-sm mb-1" title="Detail"><i class="fa fa-eye"></i></a>
              <a href="<?= base_url('admin/input_tagihan/edit/' . $row['id']) ?>" class="btn btn-warning btn-sm mb-1" title="Edit"><i class="fa fa-edit"></i></a>
              <a href="<?= base_url('staff_keuangan/input_tagihan/delete/' . $row['id']) ?>" class="btn btn-danger btn-sm mb-1 delete-link" title="Hapus"><i class="fa fa-trash"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else : ?>
        <tr>
          <td colspan="8" class="text-center">Data tagihan tidak ditemukan.</td>
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

<!-- Modal Tambah Tagihan -->
<div class="modal fade" id="tambahTagihanModal" tabindex="-1" role="dialog" aria-labelledby="tambahTagihanLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="<?= base_url('staff_keuangan/input_tagihan/tambah') ?>" method="post">
      <?= csrf_field() ?>
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Tagihan Baru</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Nama Staf -->
          <div class="form-group">
            <label>Nama Staf</label>
            <input type="text" class="form-control" value="<?= esc(session()->get('nama')) ?>" readonly>
            <input type="hidden" name="id_staff" value="<?= esc(session()->get('id_user')) ?>">
          </div>

          <!-- Pilih Kelas -->
          <div class="form-group">
            <label for="id_kelas">Pilih Kelas</label>
            <select name="id_kelas" id="id_kelas" class="form-control" required>
              <option value="">-- Pilih Kelas --</option>
              <?php foreach ($kelasList as $kelas) : ?>
                <option value="<?= esc($kelas['id_kelas']) ?>"><?= esc($kelas['nama_kelas']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Pilih Tahun -->
          <div class="form-group">
            <label for="id_tahun">Pilih Tahun Ajaran</label>
            <select name="id_tahun" id="id_tahun" class="form-control" required>
              <option value="">-- Pilih Tahun Ajaran --</option>
              <?php foreach ($tahunList as $tahun) : ?>
                <option value="<?= esc($tahun['id_tahun']) ?>"><?= esc($tahun['nama_tahun']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Bulan Tagihan -->
          <div class="form-group">
            <label for="bulan_tagihan">Bulan Tagihan</label>
            <input type="month" class="form-control" name="bulan_tagihan" required>
          </div>

          <!-- Jumlah -->
          <div class="form-group">
            <label for="jumlah">Jumlah (Rp)</label>
            <input type="number" class="form-control" name="jumlah" min="0" required>
          </div>

          <!-- Detail -->
          <div class="form-group">
            <label for="detail">Detail</label>
            <textarea name="detail" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>