<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Showroom</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: #0d47a1;
        }

        /* Biru Gelap */
        .navbar-brand,
        .nav-link {
            color: white !important;
        }

        .card {
            border: none;
            shadow-sm: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .img-thumb-list {
            width: 100px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">SHOWROOM ADMIN</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('dashboard') ?>">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('media') ?>">Media (Upload)</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('playlists') ?>">Playlist</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('TvPoints') ?>">Titik TV</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('schedules') ?>">Jadwal</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-sm btn-light text-danger fw-bold ms-3" href="<?= site_url('auth/logout') ?>">LOGOUT</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
        <?php endif; ?>

        <?php $this->load->view($content_view); ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>