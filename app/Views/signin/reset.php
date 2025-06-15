<!-- signin/reset.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>

    <!-- Bootstrap CSS Link -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
        }

        .reset-password-form {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .reset-password-form .form-group {
            margin-bottom: 1.5rem;
        }

        .reset-password-form .form-control {
            border-radius: 5px;
            padding: 0.75rem;
        }

        .reset-password-form .btn {
            background-color: #ff4d5d; /* Red Button */
            color: white;
            border-radius: 5px;
            padding: 0.75rem;
            width: 100%;
        }

        .reset-password-form .btn:hover {
            background-color: #ff3d4c; /* Darker red on hover */
        }

        .reset-password-form p {
            text-align: center;
        }

        .reset-password-form p a {
            color: #5a5a5a;
            text-decoration: none;
        }

        .reset-password-form p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="reset-password-form">
        <!-- Menampilkan pesan error atau sukses -->
        <?php if (session('error')): ?>
            <div class="alert alert-danger">
                <?= session('error') ?>
            </div>
        <?php elseif (session('sukses')): ?>
            <div class="alert alert-success">
                <?= session('sukses') ?>
            </div>
        <?php endif; ?>

        <h3>Reset Password</h3>

        <!-- Form Reset Password -->
        <?php echo form_open(base_url('signin/sendResetPasswordEmail')); ?>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" placeholder="Masukkan email Anda" required>
        </div>

        <div class="form-group">
            <button type="submit" class="btn">Kirim Link Reset Password</button>
        </div>

        <p>
            Kembali ke <a href="<?= base_url('signin') ?>">Login</a>
        </p>

        <?php echo form_close(); ?>
    </div>
</div>

<!-- jQuery (optional, for Bootstrap plugins) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS (optional, for Bootstrap plugins like modals) -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    $('#resetPasswordForm').on('submit', function(e) {
        e.preventDefault();  // Mencegah form untuk submit secara normal

        var email = $('input[name="email"]').val();  // Mendapatkan email dari form

        $.ajax({
            type: 'POST',
            url: '<?php echo base_url('signin/sendResetPasswordEmail'); ?>',  // URL yang benar untuk mengirim email
            data: { email: email },
            success: function(response) {
                // Menampilkan notifikasi sukses atau error setelah response
                if (response.status == 'success') {
                    alert(response.message);  // Tampilkan pesan sukses
                    window.location.href = '<?php echo base_url('signin'); ?>';  // Mengarahkan ulang ke halaman login jika sukses
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

</body>
</html>
