<?php $request = service('request'); ?>
<!-- Wajib untuk Bootstrap Modal bekerja -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<h4 class="mb-4">Data Tagihan Siswa</h4>

<!-- Notifikasi -->
<?php if (session()->getFlashdata('sukses')): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= session()->getFlashdata('sukses') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= session()->getFlashdata('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

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
<?= form_open(base_url('staff_keuangan/data_tagihan_siswa/update_status'), ['id' => 'form-hapus-data']) ?>
<input type="hidden" name="pengalihan" value="<?= current_url() ?>">

<div class="table-responsive mailbox-messages">
  <table class="display table table-bordered table-sm" cellspacing="0" width="100%">
    <thead>
      <tr class="bg-light text-center">
        <th width="5%"><button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="far fa-square"></i></button></th>
        <th>No</th>
        <th>Nama Siswa</th>
        <th>Tagihan Bulan</th>
        <th>Nominal</th>
        <th>Status</th>
        <th>Tanggal Bayar</th>
        <th>Kelas</th>
        <th>Tahun Ajaran</th>
        <th>Aksi</th>
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
            <td>Rp<?= number_format($row['jumlah'], 0, ',', '.') ?></td>
            <td class="text-center"><?= esc(ucwords($row['status'])) ?></td>
            <td class="text-center"><?= $row['tanggal_bayar'] ? date('d-m-Y H:i', strtotime($row['tanggal_bayar'])) : '-' ?></td>
            <td class="text-center"><?= esc($row['nama_kelas'] ?? '-') ?></td>
            <td class="text-center"><?= esc($row['tahun_ajaran'] ?? '-') ?></td>
            <td class="text-center">

              <?php if (strtolower($row['status']) === 'telat bayar') : ?>
                <!-- Tombol Edit Modal -->
                <button type="button" class="btn btn-warning btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#editStatusModal<?= $row['id'] ?>">
                  <i class="fa fa-edit"></i>
                </button>

                <!-- Modal Ubah Status -->
                <div class="modal fade" id="editStatusModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="editStatusLabel<?= $row['id'] ?>" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form action="<?= base_url('staff_keuangan/data_tagihan_siswa/update_status') ?>" method="post">
                        <div class="modal-header">
                          <h5 class="modal-title" id="editStatusLabel<?= $row['id'] ?>">Konfirmasi Pelunasan</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="id" value="<?= $row['id'] ?>">
                          <input type="hidden" name="status" value="Lunas">
                          <p>Yakin ingin menandai tagihan bulan <strong><?= esc($row['bulan_tagihan']) ?></strong> milik <strong><?= esc($row['nama_siswa']) ?></strong> sebagai <span class="badge bg-success">LUNAS</span>?</p>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" class="btn btn-success">Ya, Tandai Lunas</button>
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              <?php endif; ?>

              <!-- Tombol Hapus -->
              <a href="<?= base_url('staff_keuangan/data_tagihan_siswa/delete/' . $row['id']) ?>" class="btn btn-danger btn-sm mb-1 delete-link" title="Hapus">
                <i class="fa fa-trash"></i>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else : ?>
        <tr>
          <td colspan="10" class="text-center">Data tagihan tidak ditemukan.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?= form_close(); ?>

<div class="clearfix"><hr></div>
<div class="pull-right"><?= $pagination ?? '' ?></div>
