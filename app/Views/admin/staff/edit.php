<?php echo form_open_multipart(base_url('admin/staff/edit/' . $staff->id_staff)) ?>
<div class="form-group row">
	<label class="col-3">Nama Staff</label>
	<div class="col-6">
		<input type="text" name="nama" class="form-control" placeholder="Nama staff" value="<?php echo $staff->nama ?>" required>
	</div>
</div>

<div class="form-group row">
	<label class="col-3">Jenis Kelamin</label>
	<div class="col-6">
		<div class="form-group">
			<div class="custom-control custom-radio">
				<input class="custom-control-input" type="radio" id="customRadio1" name="jenis_kelamin" value="P" <?= $staff->jenis_kelamin == 'P' ? 'checked' : '' ?> required>
				<label for="customRadio1" class="custom-control-label">Wanita</label>
			</div>
			<div class="custom-control custom-radio">
				<input class="custom-control-input" type="radio" id="customRadio2" name="jenis_kelamin" value="L" <?= $staff->jenis_kelamin == 'L' ? 'checked' : '' ?> required>
				<label for="customRadio2" class="custom-control-label">Laki-laki</label>
			</div>
		</div>
	</div>
</div>

<div class="form-group row">
	<label class="col-3">Jabatan &amp; No Urut Tampil</label>
	<div class="col-4">
		<input type="text" name="jabatan" class="form-control" placeholder="Jabatan" value="<?= $staff->jabatan ?>">
	</div>
	<div class="col-2">
		<input type="number" name="urutan" class="form-control" placeholder="No Urut tampil" value="<?= $staff->urutan ?>">
	</div>
</div>

<div class="form-group row">
	<label class="col-3">Tempat, tanggal lahir</label>
	<div class="col-3">
		<input type="text" name="tempat_lahir" class="form-control" placeholder="Tempat lahir" value="<?= $staff->tempat_lahir ?>">
	</div>
	<div class="col-3">
		<input type="text" name="tanggal_lahir" class="form-control tanggal" placeholder="dd-mm-yyyy" value="<?= $this->website->tanggal_id($staff->tanggal_lahir) ?>">
	</div>
</div>

<div class="form-group row">
	<label class="col-3">Jenis, Status Staff</label>
	<div class="col-3">
		<select name="id_kategori_staff" class="form-control">
			<?php foreach ($kategori_staff as $kategori) : ?>
				<?php if ($kategori->slug_kategori_staff != 'administrator') : ?>
					<option value="<?= $kategori->id_kategori_staff ?>" <?= $staff->id_kategori_staff == $kategori->id_kategori_staff ? 'selected' : '' ?>>
						<?= $kategori->nama_kategori_staff ?>
					</option>
				<?php endif; ?>
			<?php endforeach; ?>
		</select>
		<small class="text-secondary">Jenis Staff</small>
	</div>

	<div class="col-3">
		<select name="status_staff" class="form-control">
			<option value="Publish" <?= $staff->status_staff == 'Publish' ? 'selected' : '' ?>>Publish</option>
			<option value="Draft" <?= $staff->status_staff == 'Draft' ? 'selected' : '' ?>>Draft</option>
		</select>
		<small class="text-secondary">Status Staff</small>
	</div>
</div>

<div class="form-group row">
	<label class="col-3">Telepon & Email</label>
	<div class="col-4">
		<input type="text" name="telepon" class="form-control" placeholder="Telepon" value="<?= $staff->telepon ?>">
	</div>
	<div class="col-5">
		<input type="text" name="email" class="form-control" placeholder="Email staff" value="<?= $staff->email ?>">
	</div>
</div>

<div class="form-group row">
	<label class="col-3">Website & Foto</label>
	<div class="col-4">
		<input type="text" name="website" class="form-control" placeholder="Website" value="<?= $staff->website ?>">
	</div>
	<div class="col-5">
		<input type="file" name="gambar" class="form-control">
	</div>
</div>

<div class="form-group row">
	<label class="col-3">Alamat</label>
	<div class="col-9">
		<textarea name="alamat" placeholder="Alamat" class="form-control"><?= $staff->alamat ?></textarea>
	</div>
</div>

<div class="form-group row">
	<label class="col-3">Keahlian</label>
	<div class="col-9">
		<textarea name="keahlian" placeholder="Keahlian" class="form-control"><?= $staff->keahlian ?></textarea>
	</div>
</div>

<div class="form-group row">
	<label class="col-3">Level Akses</label>
	<div class="col-3">
		<select name="akses_level" class="form-control" required>
			<option value="">- Pilih Level -</option>
			<option value="Keuangan" <?= $staff->akses_level == 'Keuangan' ? 'selected' : '' ?>>Staff Keuangan</option>
			<option value="Walikelas" <?= $staff->akses_level == 'Walikelas' ? 'selected' : '' ?>>Wali Kelas</option>
		</select>
	</div>
</div>

<!-- FORM KELAS -->
<div class="form-group row" id="form-kelas" style="display: none;">
	<label class="col-3">Kelas (Jika Wali Kelas)</label>
	<div class="col-6">
		<select name="id_kelas" class="form-control">
			<option value="">- Pilih Kelas -</option>
			<?php foreach ($kelas as $k) : ?>
				<option value="<?= $k->id_kelas ?>" <?= $staff->id_kelas == $k->id_kelas ? 'selected' : '' ?>>
					<?= $k->nama_kelas ?>
				</option>
			<?php endforeach; ?>
		</select>
	</div>
</div>

<!-- FORM TAHUN -->
<div class="form-group row" id="form-tahun" style="display: none;">
	<label class="col-3">Tahun Ajaran</label>
	<div class="col-6">
		<select name="id_tahun" class="form-control">
			<option value="">- Pilih Tahun -</option>
			<?php foreach ($tahun as $t) : ?>
				<option value="<?= $t->id_tahun ?>" <?= $staff->id_tahun == $t->id_tahun ? 'selected' : '' ?>>
					<?= $t->nama_tahun ?>
				</option>
			<?php endforeach; ?>
		</select>
	</div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const aksesLevelSelect = document.querySelector('select[name="akses_level"]');
		const formKelas = document.getElementById('form-kelas');
		const formTahun = document.getElementById('form-tahun');
		const kelasSelect = formKelas.querySelector('select[name="id_kelas"]');
		const tahunSelect = formTahun.querySelector('select[name="id_tahun"]');

		function toggleDropdowns() {
			if (aksesLevelSelect.value === 'Walikelas') {
				formKelas.style.display = 'flex';
				formTahun.style.display = 'flex';
				kelasSelect.disabled = false;
				tahunSelect.disabled = false;
			} else {
				formKelas.style.display = 'flex';
				formTahun.style.display = 'flex';
				kelasSelect.disabled = true;
				tahunSelect.disabled = true;
			}
		}

		aksesLevelSelect.addEventListener('change', toggleDropdowns);
		toggleDropdowns();
	});
</script>

<div class="form-group row">
	<label class="col-3"></label>
	<div class="col-9">
		<a href="<?= base_url('admin/staff') ?>" class="btn btn-outline-info"><i class="fa fa-arrow-left"></i> Kembali</a>
		<button type="submit" name="staff" value="Update Staff" class="btn btn-success"><i class="fa fa-save"></i> Simpan dan Update</button>
	</div>
</div>
<?php echo form_close(); ?>