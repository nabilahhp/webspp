<p>
	<button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-default">
		<i class="fa fa-plus"></i> Tambah Baru
	</button>
</p>

<?= form_open(base_url('admin/tahun')) ?>
<?= csrf_field() ?>
<?php $tahun_mulai = date('Y');
$tahun_selesai = $tahun_mulai + 1; ?>

<div class="modal fade" id="modal-default">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Tambah Tahun Ajaran Baru</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<div class="form-group row">
					<label class="col-3">Tahun Ajaran</label>
					<div class="col-2">
						<input type="number" name="tahun_mulai" class="form-control" placeholder="Tahun Mulai" value="" required>
					</div>
					<div class="col-1 text-center">/</div>
					<div class="col-2">
						<input type="number" name="tahun_selesai" class="form-control" placeholder="Tahun Selesai" value="" required>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-3">Nama Jenjang</label>
					<div class="col-9">
						<input type="text" name="nama_tahun" class="form-control" placeholder="Contoh: 2025/2026 - Kelas 1" value="" required>
					</div>
				</div>
			</div>

			<div class="modal-footer justify-content-end">
				<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
				<button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>
			</div>
		</div>
	</div>
</div>

<?= form_close() ?>