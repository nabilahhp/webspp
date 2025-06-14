<?php include('tambah.php'); ?>
<table class="table table-bordered table-sm" id="example3">
	<thead>
		<tr class="bg-secondary text-center">
			<th width="5%">No</th>
			<th width="20%">Nama</th>
			<th width="40%">Keterangan</th>
			<th width="10%">Status</th>
			<th width="10%">Urutan</th>
			<th width="10%">Aksi</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$no = 1;
		foreach ($kelas as $kelas) {
			$kelasnya = $m_kelas->jenjang($kelas->id_jenjang);
		?>

			<?php if ($kelasnya) {
				$i = 1;
				foreach ($kelasnya as $kelasnya) { ?>
					<tr>
						<td class="text-center"><?php echo $i ?></td>
						<td><?php echo $kelasnya->nama_kelas ?></td>
						<td><?php echo $kelasnya->keterangan ?></td>
						<td><?php echo $kelasnya->status_kelas ?></td>
						<td><?php echo $kelasnya->urutan ?></td>
						<td>
							<a href="<?php echo base_url('admin/kelas/edit/' . $kelasnya->id_kelas) ?>" class="btn btn-success btn-xs mb-1">
								<i class="fa fa-edit"></i>
							</a>
							<a href="<?php echo base_url('admin/kelas/delete/' . $kelasnya->id_kelas) ?>" class="btn btn-dark btn-sm delete-link">
								<i class="fa fa-trash"></i>
							</a>
						</td>
					</tr>
		<?php $i++;
				}
			}
			$no++;
		} ?>
	</tbody>
	<!-- ✅ Perbaikan di baris ini -->
	<tr class="bg-light group-row" id="jenjang<?php echo $kelas->id_jenjang ?>">
		<td colspan="6"><strong><?php echo $kelas->nama_jenjang ?> (<?php echo $kelas->keterangan_jenjang ?>)</strong></td>
	</tr>
</table>

<!-- Tambahkan skrip DataTables -->
<script>
	$(document).ready(function() {
		$.fn.dataTable.ext.errMode = 'none'; // (Opsional) menyembunyikan warning error
		$('#example3').DataTable({
			"order": [], // Tidak mengurutkan default
			"columnDefs": [{
					"orderable": false,
					"targets": [0, 5]
				} // Kolom No & Aksi tidak bisa diurut
			]
		});
	});
</script>