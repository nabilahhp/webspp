<?php 
use App\Models\Agama_model;
use App\Models\Jenjang_model;
use App\Models\Pekerjaan_model;
use App\Models\Hubungan_model;
use App\Models\Kelas_model;
use App\Models\Tahun_model;

$m_agama    = new Agama_model();
$m_jenjang  = new Jenjang_model();
$m_pekerjaan = new Pekerjaan_model();
$m_hubungan = new Hubungan_model();
$m_tahun    = new Tahun_model();
$m_kelas    = new Kelas_model();

echo form_open_multipart(base_url('admin/siswa/edit/'.$siswa->id_siswa));
echo csrf_field(); 
?>

<p class="text-right">
    <a href="<?php echo base_url('admin/siswa') ?>" class="btn btn-outline-info">
        <i class="fa fa-arrow-left"></i> Kembali
    </a>
</p>

<div class="row">
    <!-- FOTO SISWA -->
    <div class="col-md-3 col-sm-4 mb-4">
        <div class="card">
            <div class="card-header bg-light text-center">
                <h4>FOTO SISWA</h4>
            </div>
            <div class="card-body text-center">
                <?php if($siswa->gambar == '') { ?>
                    <div class="alert alert-info">Belum Ada foto</div>
                <?php } else { ?>
                    <img src="<?php echo base_url('assets/upload/image/'.$siswa->gambar) ?>" class="img-fluid img-thumbnail" alt="Foto Siswa" style="max-width: 200px; height: auto;">
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- DATA DASAR SISWA -->
    <div class="col-md-9 col-sm-8">
        <div class="card">
            <div class="card-header bg-light text-center">
                <h4>DATA DASAR SISWA</h4>
            </div>
            <div class="card-body">

                <!-- Nama Siswa -->
                <div class="form-group row">
                    <label class="col-3 col-sm-2 col-form-label">Nama Lengkap<span class="text-danger">*</span></label>
                    <div class="col-9 col-sm-10">
                        <input type="text" name="nama_siswa" class="form-control" placeholder="Nama lengkap siswa" value="<?= set_value('nama_siswa', $siswa->nama_siswa); ?>" required>
                    </div>
                </div>

                <!-- NIS -->
                <div class="form-group row">
                    <label class="col-3 col-sm-2 col-form-label">NIS</label>
                    <div class="col-9 col-sm-10">
                        <input type="text" name="nis" class="form-control" placeholder="Nomor Induk Siswa (NIS)" value="<?= set_value('nis', $siswa->nis); ?>">
                    </div>
                </div>

                <!-- Jenis Kelamin -->
                <div class="form-group row">
                    <label class="col-3 col-sm-2 col-form-label">Jenis Kelamin<span class="text-danger">*</span></label>
                    <div class="col-9 col-sm-10">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input class="custom-control-input" name="jenis_kelamin" type="radio" id="customRadio1" value="L" <?= set_radio('jenis_kelamin', 'L', ($siswa->jenis_kelamin == 'L') ? TRUE : FALSE); ?> required>
                            <label for="customRadio1" class="custom-control-label">Laki-laki</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input class="custom-control-input" name="jenis_kelamin" type="radio" id="customRadio2" value="P" <?= set_radio('jenis_kelamin', 'P', ($siswa->jenis_kelamin == 'P') ? TRUE : FALSE); ?> required>
                            <label for="customRadio2" class="custom-control-label">Perempuan</label>
                        </div>
                    </div>
                </div>

                <!-- Telepon dan Email -->
                <div class="form-group row">
                    <label class="col-3 col-sm-2 col-form-label">Telepon dan Email</label>
                    <div class="col-9 col-sm-5">
                        <input type="text" name="telepon" class="form-control" placeholder="Telepon/HP" value="<?= set_value('telepon', $siswa->telepon); ?>">
                    </div>
                    <div class="col-9 col-sm-5">
                        <input type="email" name="email" class="form-control" placeholder="Email" value="<?= set_value('email', $siswa->email); ?>">
                    </div>
                </div>

                <!-- Gambar/Foto -->
                <div class="form-group row">
                    <label class="col-3 col-sm-2 col-form-label">Gambar/Foto</label>
                    <div class="col-9 col-sm-10">
                        <input type="file" name="gambar" class="form-control" placeholder="Gambar/Foto">
                    </div>
                </div>

                <!-- Status Siswa -->
                <div class="form-group row">
                    <label class="col-3 col-sm-2 col-form-label">Status Siswa<span class="text-danger">*</span></label>
                    <div class="col-9 col-sm-10">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input class="custom-control-input" name="status_siswa" type="radio" id="status_siswa1" value="Aktif" <?= set_radio('status_siswa', 'Aktif', ($siswa->status_siswa == 'Aktif') ? TRUE : FALSE); ?> required>
                            <label for="status_siswa1" class="custom-control-label">Aktif</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input class="custom-control-input" name="status_siswa" type="radio" id="status_siswa2" value="Lulus" <?= set_radio('status_siswa', 'Lulus', ($siswa->status_siswa == 'Lulus') ? TRUE : FALSE); ?> required>
                            <label for="status_siswa2" class="custom-control-label">Lulus</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input class="custom-control-input" name="status_siswa" type="radio" id="status_siswa3" value="Pindah" <?= set_radio('status_siswa', 'Pindah', ($siswa->status_siswa == 'Pindah') ? TRUE : FALSE); ?> required>
                            <label for="status_siswa3" class="custom-control-label">Pindah</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input class="custom-control-input" name="status_siswa" type="radio" id="status_siswa4" value="Meninggal" <?= set_radio('status_siswa', 'Meninggal', ($siswa->status_siswa == 'Meninggal') ? TRUE : FALSE); ?> required>
                            <label for="status_siswa4" class="custom-control-label">Meninggal</label>
                        </div>
                    </div>
                </div>

                <!-- Tahun Ajaran Saat Masuk -->
                <div class="form-group row">
                    <label class="col-3 col-sm-2 col-form-label">Tahun Ajaran Saat Masuk<span class="text-danger">*</span></label>
                    <div class="col-9 col-sm-10">
                        <select name="id_tahun" class="form-control select2" required>
                            <option value="">Pilih Tahun Ajaran</option>
                            <?php foreach($m_tahun->listing() as $tahun) { ?>
                                <option value="<?= $tahun->id_tahun ?>" <?= set_select('id_tahun', $tahun->id_tahun, ($siswa->id_tahun == $tahun->id_tahun) ? TRUE : FALSE); ?>>
                                    <?= $tahun->tahun_mulai ?>/<?= $tahun->tahun_selesai ?> - <?= $tahun->nama_tahun ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <!-- Kelas Saat Masuk -->
                <div class="form-group row">
                    <label class="col-3 col-sm-2 col-form-label">Kelas Saat Masuk<span class="text-danger">*</span></label>
                    <div class="col-9 col-sm-10">
                        <select name="id_kelas" class="form-control select2" required>
                            <option value="">Pilih Kelas</option>
                            <?php foreach($m_kelas->listing() as $kelas) { ?>
                                <option value="<?= $kelas->id_kelas ?>" <?= set_select('id_kelas', $kelas->id_kelas, ($siswa->id_kelas == $kelas->id_kelas) ? TRUE : FALSE); ?>>
                                    <?= $kelas->nama_kelas ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- DATA ORANG TUA (AYAH) -->
<div class="card mt-4">
    <div class="card-header bg-light text-center">
        <h4>DATA ORANG TUA SISWA - AYAH</h4>
    </div>
    <div class="card-body">
        <div class="form-group row">
            <label class="col-3 col-sm-2 col-form-label">Nama Ayah<span class="text-danger">*</span></label>
            <div class="col-9 col-sm-10">
                <input type="text" name="nama_ayah" class="form-control" placeholder="Nama Ayah" value="<?= set_value('nama_ayah', $siswa->nama_ayah); ?>">
            </div>
        </div>

        <div class="form-group row">
            <label class="col-3 col-sm-2 col-form-label">Telepon/HP Ayah</label>
            <div class="col-9 col-sm-10">
                <input type="text" name="telepon_ayah" class="form-control" placeholder="Telepon/HP Ayah" value="<?= set_value('telepon_ayah', $siswa->telepon_ayah); ?>">
            </div>
        </div>
    </div>
</div>

<!-- DATA ORANG TUA (IBU) -->
<div class="card mt-4">
    <div class="card-header bg-light text-center">
        <h4>DATA ORANG TUA SISWA - IBU</h4>
    </div>
    <div class="card-body">
        <div class="form-group row">
            <label class="col-3 col-sm-2 col-form-label">Nama Ibu<span class="text-danger">*</span></label>
            <div class="col-9 col-sm-10">
                <input type="text" name="nama_ibu" class="form-control" placeholder="Nama Ibu" value="<?= set_value('nama_ibu', $siswa->nama_ibu); ?>">
            </div>
        </div>

        <div class="form-group row">
            <label class="col-3 col-sm-2 col-form-label">Telepon/HP Ibu</label>
            <div class="col-9 col-sm-10">
                <input type="text" nam_valueepon_ibu" class="form-control" placeholder="Telepon/HP Ibu" value="<?= set_value('telepon_ibu', $siswa->telepon_ibu); ?>">
            </div>
        </div>
    </div>
</div>

<div class="text-right">
    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>
</div>

<?php echo form_close(); ?>
