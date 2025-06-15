<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://sandbox.doku.com/jokul-checkout-js/v1/jokul-checkout-1.0.0.js"></script>
    <title>Redirect Pembayaran</title>
</head>
<body>
    <p>Mohon tunggu, sedang mengarahkan ke halaman pembayaran...</p>

    <script>
        loadJokulCheckout("<?= $payment_url ?>");
    </script>
</body>
</html>
