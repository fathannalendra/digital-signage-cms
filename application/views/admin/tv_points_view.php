<div class="row">
    <div class="col-md-4">
        <div class="card p-4 shadow-sm border-0">
            <h5 class="fw-bold mb-3"><i class="fas fa-desktop me-2"></i>Registrasi TV Baru</h5>

            <form action="<?= site_url('TvPoints/add') ?>" method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama TV / Zona</label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: TV Lobby Depan" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Lokasi Detail</label>
                    <textarea name="location" class="form-control" rows="2" placeholder="Dekat pintu masuk..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold">SIMPAN TV</button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card p-4 shadow-sm border-0">
            <h5 class="fw-bold mb-3">Daftar Titik TV Terdaftar</h5>

            <div class="alert alert-info py-2 small">
                <i class="fas fa-info-circle"></i> <b>Cara Setting di TV Fisik:</b><br>
                Gunakan URL khusus di browser TV sesuai ID-nya agar sistem mengenali lokasinya.
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="30%">Nama TV & Lokasi</th>
                            <th>URL Setting (Untuk di Browser TV)</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tvs)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada TV terdaftar.</td>
                            </tr>
                        <?php else: ?>
                            <?php
                            $no = 1; // Inisialisasi Nomor
                            foreach ($tvs as $tv):
                            ?>
                                <tr>
                                    <td class="text-center fw-bold"><?= $no++ ?></td>
                                    <td>
                                        <div class="fw-bold"><?= $tv->name ?></div>
                                        <div class="small text-muted"><i class="fas fa-map-marker-alt me-1"></i> <?= $tv->location ?></div>
                                    </td>

                                    <td>
                                        <code class="user-select-all">
                                            <?= base_url('?id=' . $tv->id) ?>
                                        </code>
                                        <a href="<?= base_url('?id=' . $tv->id) ?>" target="_blank" class="ms-2 text-decoration-none small">
                                            <i class="fas fa-external-link-alt"></i> Test
                                        </a>
                                    </td>

                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-warning text-white me-1 btn-edit"
                                            data-id="<?= $tv->id ?>"
                                            data-name="<?= $tv->name ?>"
                                            data-location="<?= $tv->location ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editTvModal">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <a href="<?= site_url('TvPoints/delete/' . $tv->id) ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin hapus TV ini? Jadwal yang terkait mungkin akan error.')">
                                            <i class="fas fa-trash"></i>
                                        </a>
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

<div class="modal fade" id="editTvModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Data TV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('TvPoints/update') ?>" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label">Nama TV / Zona</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lokasi Detail</label>
                        <textarea name="location" id="edit_location" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const editButtons = document.querySelectorAll('.btn-edit');

        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                // Ambil data dari atribut tombol
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const location = this.getAttribute('data-location');

                // Masukkan ke dalam form modal
                document.getElementById('edit_id').value = id;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_location').value = location;
            });
        });
    });
</script>