<?php 
    $validation = \Config\Services::validation();
    $errors = $validation->getErrors();
    if (!empty($errors)) {
        echo '<span class="text-danger">' . $validation->listErrors() . '</span>';
    }
?>

<?php if (session('sukses')): ?>
    <div class="alert alert-success">
        <?= session('sukses') ?>
    </div>
<?php elseif (session('warning')): ?>
    <div class="alert alert-warning">
        <?= session('warning') ?>
    </div>
<?php elseif (session('error')): ?>
    <div class="alert alert-danger">
        <?= session('error') ?>
    </div>
<?php endif; ?>

<?php echo form_open(base_url('login/lupa'), 'class="signin-form" id="resetPasswordForm"'); ?>

<input type="hidden" name="pengalihan" value="<?php echo Session()->get('pengalihan'); ?>">

<div class="form-group mb-3">
    <label class="label" for="name">Email</label>
    <input type="email" name="email" class="form-control" placeholder="Email" required>
</div>

<div class="form-group">
    <button type="submit" class="form-control btn btn-primary submit px-3">Reset Password</button>
</div>

<p class="text-center">
    Kembali ke <a href="<?php echo base_url('login') ?>">Login</a>
</p>

<?php echo form_close(); ?>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $('#resetPasswordForm').on('submit', function(e) {
        e.preventDefault();  // Mencegah form untuk submit secara normal

        var email = $('input[name="email"]').val();  // Mendapatkan email dari form

        $.ajax({
            type: 'POST',
            url: '<?php echo base_url('login/lupa'); ?>',  // URL untuk mengirim email
            data: { email: email },
            success: function(response) {
                // Menampilkan notifikasi sukses atau error setelah response
                if (response.status == 'success') {
                    alert(response.message);  // Tampilkan pesan sukses
                    window.location.href = '<?php echo base_url('login/lupa'); ?>';  // Mengarahkan ulang jika sukses
                } else {
                    alert(response.message);  // Tampilkan pesan error
                }
            },
            error: function() {
                alert('Terjadi kesalahan, coba lagi.');
            }
        });
    });
});
</script>
