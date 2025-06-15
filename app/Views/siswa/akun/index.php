<div class="row">
	<div class="col-md-5">
		<div class="card">
			<div class="card-header bg-light">
				<strong>DETAIL AKUN</strong>
			</div>
			<div class="card-body">
				<table class="table table-sm table-bordered">
					<thead>
						<tr>
							<th>Nis</th>
							<th><?php echo $siswa->nis ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Nama Siswa</td>
							<td><?php echo $siswa->nama_siswa ?></td>
						</tr>
						<tr>
							<td>Telepon</td>
							<td><?php echo $siswa->telepon ?></td>
						</tr>
						<tr>
							<td>Email</td>
							<td><?php echo $siswa->email ?></td>
						</tr>
						<tr>
							<td>Nama Ayah</td>
							<td><?php echo $siswa->nama_ayah ?></td>
						</tr>
						<tr>
							<td>Nomor Telepon Ayah</td>
							<td><?php echo $siswa->telepon_ayah ?></td>
						</tr>
						<tr>
							<td>Nama Ibu</td>
							<td><?php echo $siswa->nama_ibu ?></td>
						</tr>
						<tr>
							<td>Nomor Telepon Ibu</td>
							<td><?php echo $siswa->telepon_ibu ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="col-md-7">
		<div class="card">
			<div class="card-header bg-light">
				<strong>UPDATE AKUN</strong>
			</div>
			<div class="card-body">
				<?php echo form_open(base_url('siswa/akun')) ?>
				<div class="form-group mb-4">
					<input type="text" class="form-control" name="nama_siswa" value="<?php echo $siswa->nama_siswa ?>" placeholder="Nama Siswa" id="loginName">
					<label for="loginName" class="text-primary">Nama</label>
				</div>

				<div class="form-group mb-4">
					<input type="email" class="form-control" name="email" value="<?php echo $siswa->email ?>" placeholder="Email" id="loginEmail">
					<label for="loginEmail" class="text-primary">Email (Username)</label>
				</div>

				<div class="form-group password-field mb-4">
					<input type="password" class="form-control" name="password" placeholder="Password (opsional)" id="loginPassword" minlength="6" maxlength="32">
					<span class="password-toggle"><i class="uil uil-eye"></i></span>
					<label for="loginPassword" class="text-primary">Password baru (opsional)</label>
				</div>

				<div class="form-group mb-4">
					<input type="text" class="form-control" name="telepon" value="<?php echo $siswa->telepon ?>" placeholder="Telepon/HP" id="Telepon">
					<label for="loginEmail" class="text-primary">Telepon/HP</label>
				</div>
				<div class="form-group row">
					<div class="col-12 text-center">
						<button type="submit" name="user" value="Update User" class="btn btn-success btn-block"><i class="fa fa-save"></i> Update Akun</button>
					</div>
				</div>
				</form>
			</div>
		</div>
	</div>
</div>