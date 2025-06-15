<p>
	<button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-default">
		<i class="fa fa-plus"></i> Tambah Baru
	</button>
</p>
<?php 
echo form_open(base_url('admin/tahun')); 
echo csrf_field(); 
$tahun_selesai = date('Y')+1;
?>
<div class="modal fade" id="modal-default">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Tambah Baru</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">

				<div class="form-group row">
					<label class="col-3">Tahun Ajaran</label>
					<div class="col-2">
						<select name="tahun_ajaran" class="form-control" required>
							<option value="" disabled selected>Pilih Tahun Ajaran</option>
							<option value="2021/2022" <?php echo set_select('tahun_ajaran', '2021/2022'); ?>>2021/2022</option>
							<option value="2022/2023" <?php echo set_select('tahun_ajaran', '2022/2023'); ?>>2022/2023</option>
							<!-- Tambahkan pilihan tahun ajaran lainnya sesuai kebutuhan -->
						</select>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-3">Kelas Saat Masuk</label>
					<div class="col-9">
						<select name="kelas" class="form-control" required>
							<option value="" disabled selected>Pilih Kelas</option>
							<option value="1" <?php echo set_select('kelas', '1'); ?>>Kelas 1</option>
							<option value="2" <?php echo set_select('kelas', '2'); ?>>Kelas 2</option>
							<option value="3" <?php echo set_select('kelas', '3'); ?>>Kelas 3</option>
							<option value="4" <?php echo set_select('kelas', '4'); ?>>Kelas 4</option>
							<option value="5" <?php echo set_select('kelas', '5'); ?>>Kelas 5</option>
							<option value="6" <?php echo set_select('kelas', '6'); ?>>Kelas 6</option>
							<!-- Tambahkan pilihan kelas lainnya sesuai kebutuhan -->
						</select>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-3">Nama Jenjang</label>
					<div class="col-9">
						<input type="text" name="nama_tahun" class="form-control" placeholder="Nama tahun" value="<?php echo set_value('nama_tahun') ?>" required>
						<small class="text-gray">Misal: Tahun Ajaran <?php echo date('Y').'/'.$tahun_selesai; ?></small>
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
<?php echo form_close(); ?>
