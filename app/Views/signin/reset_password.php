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
            background-color: #ff4d5d;
            /* Red Button */
            color: white;
            border-radius: 5px;
            padding: 0.75rem;
            width: 100%;
        }

        .reset-password-form .btn:hover {
            background-color: #ff3d4c;
            /* Darker red on hover */
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
                    <?= is_array(session('error')) ? implode('<br>', session('error')) : session('error') ?>
                </div>
            <?php elseif (session('sukses')): ?>
                <div class="alert alert-success">
                    <?= is_array(session('sukses')) ? implode('<br>', session('sukses')) : session('sukses') ?>
                </div>
            <?php endif; ?>


            <h3>Reset Password</h3>

            <!-- Form Reset Password -->
            <form action="<?= base_url('signin/updatePassword') ?>" method="post">
                <input type="hidden" name="token" value="<?= $token ?>">

                <div class="form-group">
                    <label for="new_password">Password Baru</label>
                    <input
                        type="password"
                        name="new_password"
                        id="new_password"
                        class="form-control"
                        placeholder="Masukkan password baru"
                        required
                        minlength="8"
                        maxlength="255">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">Reset Password</button>
                </div>

                <p>
                    Kembali ke <a href="<?= base_url('signin') ?>">Login</a>
                </p>
            </form>

        </div>
    </div>

    <!-- jQuery (optional, for Bootstrap plugins) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS (optional, for Bootstrap plugins like modals) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

</body>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.querySelector('form');
        const passwordInput = document.getElementById('new_password');
        const errorMsg = document.createElement('div');
        errorMsg.style.color = 'red';

        form.addEventListener('submit', function(event) {
            // Bersihkan pesan error sebelumnya
            errorMsg.textContent = '';

            // Periksa apakah password baru sudah diisi
            if (passwordInput.value.trim() === '') {
                errorMsg.textContent = 'Password baru harus diisi.';
                form.insertBefore(errorMsg, passwordInput);
                event.preventDefault(); // Mencegah form untuk dikirim
                return;
            }

            // Periksa panjang password
            if (passwordInput.value.length < 8) {
                errorMsg.textContent = 'Password baru harus lebih dari 8 karakter.';
                form.insertBefore(errorMsg, passwordInput);
                event.preventDefault(); // Mencegah form untuk dikirim
                return;
            }

            if (passwordInput.value.length > 255) {
                errorMsg.textContent = 'Password baru maksimal 255 karakter.';
                form.insertBefore(errorMsg, passwordInput);
                event.preventDefault(); // Mencegah form untuk dikirim
                return;
            }
        });
    });
</script>

</html>