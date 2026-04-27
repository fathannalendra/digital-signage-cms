<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Showroom System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #0d47a1;
            /* Biru Utama */
            border-color: #0d47a1;
        }

        .btn-primary:hover {
            background-color: #002171;
            /* Biru Gelap (saat mouse hover) */
            border-color: #002171;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <h3 class="text-center mb-4">Admin Panel</h3>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger">
                <?= $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('auth/process') ?>" method="POST">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">LOGIN</button>
        </form>

        <div class="text-center mt-3">
            <small class="text-muted">&copy; Showroom Digital Advertising</small>
        </div>
    </div>

</body>

</html>