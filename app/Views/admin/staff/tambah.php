<?php echo form_open_multipart(base_url('admin/staff/tambah')) ?>


<div class="form-group row">
	<label class="col-3">Nama Staff</label>
	<div class="col-6">
		<input type="text" name="nama" class="form-control" placeholder="Nama staff" value="<?php echo set_value('nama') ?>" required>
	</div>
</div>

<div class="form-group row">
	<label class="col-3">Username Akun</label>
	<div class="col-6">
		<input type="text" name="username" class="form-control" placeholder="Username staff" value="<?php echo set_value('username') ?>" required>
	</div>
</div>


<div class="form-group row">
	<label class="col-3">Jenis Kelamin</label>
	<div class="col-6">
		<div class="form-group">
			<div class="custom-control custom-radio">
				<input class="custom-control-input" type="radio" id="customRadio1" name="jenis_kelamin" value="P" required>
				<label for="customRadio1" class="custom-control-label">Wanita</label>
			</div>
			<div class="custom-control custom-radio">
				<input class="custom-control-input" type="radio" id="customRadio2" name="jenis_kelamin" value="L" required>
				<label for="customRadio2" class="custom-control-label">Laki-laki</label>
			</div>
		</div>
	</div>
</div>

<div class="form-group row">
	<label class="col-3">Jabatan &amp; No Urut</label>
	<div class="col-4">
		<input type="text" name="jabatan" class="form-control" placeholder="Jabatan" value="<?php echo set_value('jabatan') ?>" required>
	</div>
	<div class="col-2">
		<input type="number" name="urutan" class="form-control" placeholder="No Urut tampil" value="<?php echo set_value('urutan') ?>" required>
	</div>
</div>

<div class="form-group row">
	<label class="col-3">Tempat, tanggal lahir</label>
	<div class="col-3">
		<input type="text" name="tempat_lahir" class="form-control" placeholder="Tempat lahir" value="<?php echo set_value('tempat_lahir') ?>" required>
	</div>
	<div class="col-3">
		<input type="text" name="tanggal_lahir" class="form-control tanggal" placeholder="dd-mm-yyyy" value="<?php echo set_value('tanggal_lahir') ?>" required>
	</div>
</div>

<div class="form-group row">
	<label class="col-3">Jenis, Status Staff</label>
	<div class="col-3">
		<select name="id_kategori_staff" class="form-control" required>
			<?php foreach ($kategori_staff as $kategori_staff) { ?>
				<?php if ($kategori_staff->slug_kategori_staff != 'administrator') { ?>
					<option value="<?php echo $kategori_staff->id_kategori_staff ?>">
						<?php echo $kategori_staff->nama_kategori_staff ?>
					</option>
				<?php } ?>
			<?php } ?>
		</select>
		<small class="text-secondary">Jenis Staff</small>
	</div>

	<div class="col-3">
		<select name="status_staff" class="form-control" required>
			<option value="Publish">Publish</option>
			<option value="Draft">Draft</option>
		</select>
		<small class="text-secondary">Status Staff</small>
	</div>
</div>

<div class="form-group row">
	<label class="col-3">Email dan Telepon</label>
	<div class="col-4">
		<input type="text" name="email" class="form-control" placeholder="Email staff" value="<?php echo set_value('email') ?>" required>
	</div>
	<div class="col-5">
		<input type="text" name="telepon" class="form-control" placeholder="Telepon" value="<?php echo set_value('telepon') ?>" required>
	</div>
</div>

<div class="form-group row">
	<label class="col-3">Upload Foto dan Website</label>
	<div class="col-4">
		<input type="text" name="website" class="form-control" placeholder="Website" value="<?php echo set_value('website') ?>">
	</div>
	<div class="col-5">
		<input type="file" name="gambar" class="form-control" placeholder="gambar" value="<?php echo set_value('gambar') ?>">
	</div>
</div>

<div class="form-group row">
	<label class="col-3">Alamat</label>
	<div class="col-9">
		<textarea name="alamat" placeholder="Alamat" class="form-control" required><?php echo set_value('alamat') ?></textarea>
	</div>
</div>

<div class="form-group row">
	<label class="col-3">Keahlian</label>
	<div class="col-9">
		<textarea name="keahlian" placeholder="Keahlian" class="form-control"><?php echo set_value('keahlian') ?></textarea>
	</div>
</div>

<div class="form-group row">
	<label class="col-3">Level Akses</label>
	<div class="col-3">
		<select name="akses_level" class="form-control" required>
			<option value="">- Pilih Level -</option>
			<option value="Keuangan">Staff Keuangan</option>
			<option value="Walikelas">Wali Kelas</option>
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
				<option value="<?= $k->id_kelas ?>"><?= $k->nama_kelas ?></option>
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
				<option value="<?= $t->id_tahun ?>"><?= $t->nama_tahun ?></option>
			<?php endforeach; ?>
		</select>
	</div>
</div>


<script>
	document.addEventListener('DOMContentLoaded', function() {
		const aksesLevelSelect = document.querySelector('select[name="akses_level"]');
		const formKelas = document.getElementById('form-kelas');
		const kelasSelect = formKelas.querySelector('select[name="id_kelas"]');

		const formTahun = document.getElementById('form-tahun');
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
				kelasSelect.value = "";
				tahunSelect.value = "";
			}
		}

		aksesLevelSelect.addEventListener('change', toggleDropdowns);
		toggleDropdowns(); // on page load
	});
</script>


<div class="form-group row">
	<label class="col-3"></label>
	<div class="col-9">
		<a href="<?php echo base_url('admin/staff') ?>" class="btn btn-outline-info">
			<i class="fa fa-arrow-left"></i> Kembali
		</a>
		<button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>
	</div>
</div>




<?php echo form_close(); ?>