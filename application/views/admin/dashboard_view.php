<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">👋 Selamat Datang, Admin!</h2>
        <p class="text-muted">Berikut adalah ringkasan sistem Digital Advertising Anda hari ini.</p>
    </div>
</div>

<div class="row g-3 mb-5">
    
    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #0d47a1 0%, #1565c0 100%); color: white;">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-1" style="opacity: 0.8;">Total Media</h6>
                    <h2 class="mb-0 fw-bold"><?= $total_media ?></h2>
                </div>
                <div style="font-size: 2.5rem; opacity: 0.3;">
                    <i class="fas fa-photo-video"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="<?= site_url('media') ?>" class="text-white text-decoration-none small">Lihat File &rarr;</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100 bg-success text-white">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-1" style="opacity: 0.8;">Playlist</h6>
                    <h2 class="mb-0 fw-bold"><?= $total_playlist ?></h2>
                </div>
                <div style="font-size: 2.5rem; opacity: 0.3;">
                    <i class="fas fa-list-ul"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="<?= site_url('playlists') ?>" class="text-white text-decoration-none small">Kelola Playlist &rarr;</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100 bg-warning text-dark">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-1" style="opacity: 0.8;">Sedang Tayang</h6>
                    <h2 class="mb-0 fw-bold"><?= $active_schedules ?></h2>
                    <small>Jadwal Aktif</small>
                </div>
                <div style="font-size: 2.5rem; opacity: 0.3;">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="<?= site_url('schedules') ?>" class="text-dark text-decoration-none small fw-bold">Cek Jadwal &rarr;</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100 bg-secondary text-white">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-1" style="opacity: 0.8;">Titik TV</h6>
                    <h2 class="mb-0 fw-bold"><?= $total_tv ?></h2>
                </div>
                <div style="font-size: 2.5rem; opacity: 0.3;">
                    <i class="fas fa-tv"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="<?= site_url('tvpoints') ?>" class="text-white text-decoration-none small">Atur Lokasi &rarr;</a>
            </div>
        </div>
    </div>
</div>

<div class="row mb-5">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark mb-2 mb-md-0">
                    <i class="fas fa-list-alt me-2 text-primary"></i> Laporan Tayang Harian
                </h5>
                
                <form action="<?= site_url('dashboard') ?>" method="GET" class="d-flex">
                    <input type="date" name="date" class="form-control me-2" value="<?= $filter_date ?>" required>
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-search"></i> Tampilkan</button>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th width="40%">Nama Playlist</th>
                                <th width="35%">Lokasi TV</th>
                                <th class="text-center" width="20%">Total Diputar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($daily_reports)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                                    <br>Belum ada data penayangan pada tanggal <b><?= date('d M Y', strtotime($filter_date)) ?></b>.
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($daily_reports as $row): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    
                                    <td class="fw-bold text-dark">
                                        <?= $row->playlist_name ? $row->playlist_name : '<span class="text-danger fst-italic">Playlist Telah Dihapus</span>' ?>
                                    </td>
                                    
                                    <td>
                                        <span class="fw-bold text-primary"><?= $row->tv_name ? $row->tv_name : 'TV Tidak Diketahui' ?></span><br>
                                        <small class="text-muted"><i class="fas fa-map-marker-alt"></i> <?= $row->tv_location ? $row->tv_location : '-' ?></small>
                                    </td>
                                    
                                    <td class="text-center">
                                        <span class="badge bg-success rounded-pill px-3 py-2" style="font-size: 0.9rem;">
                                            <?= $row->play_count ?> Kali
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-rocket me-2 text-primary"></i>Akses Cepat</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    
                    <div class="col-md-3 mb-3">
                        <a href="<?= site_url('media') ?>" class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center">
                            <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                            <span>Upload Iklan Baru</span>
                        </a>
                    </div>

                    <div class="col-md-3 mb-3">
                        <a href="<?= site_url('playlists') ?>" class="btn btn-outline-success w-100 py-3 d-flex flex-column align-items-center">
                            <i class="fas fa-plus-circle fa-2x mb-2"></i>
                            <span>Buat Playlist</span>
                        </a>
                    </div>

                    <div class="col-md-3 mb-3">
                        <a href="<?= site_url('schedules') ?>" class="btn btn-outline-warning w-100 py-3 d-flex flex-column align-items-center text-dark">
                            <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                            <span>Atur Jadwal Tayang</span>
                        </a>
                    </div>

                    <div class="col-md-3 mb-3">
                        <a href="<?= site_url('tv') ?>" target="_blank" class="btn btn-dark w-100 py-3 d-flex flex-column align-items-center">
                            <i class="fas fa-desktop fa-2x mb-2"></i>
                            <span>Buka Player TV</span>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>