<div class="row">
    <div class="col-md-4">
        <div class="card p-4 shadow-sm mb-4">
            <h5 class="fw-bold mb-3">Buat Playlist Baru</h5>
            <form action="<?= site_url('playlists/create') ?>" method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Playlist</label>
                    <input type="text" name="name" class="form-control" placeholder="Misal: Playlist Pagi" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Keterangan singkat..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold">SIMPAN PLAYLIST</button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card p-4 shadow-sm">
            <h5 class="fw-bold mb-3">Daftar Playlist</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle border">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Playlist</th>
                            <th>Deskripsi</th>
                            <th width="25%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($playlists as $p): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <div class="fw-bold text-primary"><?= $p->name ?></div>
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    <i class="fas fa-clock me-1"></i> <?= $p->created_at ?>
                                </small>
                            </td>
                            <td class="text-muted small"><?= $p->description ?></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-info text-white btn-edit-playlist" 
                                            data-id="<?= $p->id ?>" 
                                            data-name="<?= $p->name ?>" 
                                            data-desc="<?= $p->description ?>"
                                            title="Edit Nama">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>

                                    <button class="btn btn-sm btn-success text-white" 
                                            onclick="previewPlaylist(<?= $p->id ?>, '<?= $p->name ?>')"
                                            title="Preview Tayangan">
                                        <i class="fas fa-play"></i>
                                    </button>

                                    <a href="<?= site_url('playlists/manage/' . $p->id) ?>" 
                                       class="btn btn-sm btn-warning text-dark fw-bold" 
                                       title="Kelola Isi">
                                        <i class="fas fa-list me-1"></i> Isi Video
                                    </a>

                                    <a href="<?= site_url('playlists/delete/' . $p->id) ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Hapus playlist ini? Jadwal yang menggunakan playlist ini mungkin akan error.')"
                                       title="Hapus Playlist">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="previewTvModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0 py-2 bg-black text-white">
                <h6 class="modal-title"><i class="fas fa-tv me-2"></i>Preview: <span id="previewLabel"></span></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-black" style="height: 600px;">
                <iframe id="previewFrame" src="" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editPlaylistModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Playlist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('playlists/update') ?>" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Playlist</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" id="edit_desc" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var previewModal;
    var editModal; // Variabel baru buat edit

    document.addEventListener("DOMContentLoaded", function() {
        // Init Preview Modal
        var elPrev = document.getElementById('previewTvModal');
        previewModal = new bootstrap.Modal(elPrev, {backdrop: 'static'});
        elPrev.addEventListener('hidden.bs.modal', function () {
            document.getElementById('previewFrame').src = '';
        });

        // Init Edit Modal (BARU)
        var elEdit = document.getElementById('editPlaylistModal');
        editModal = new bootstrap.Modal(elEdit);

        // Event Listener untuk Tombol Edit
        const editBtns = document.querySelectorAll('.btn-edit-playlist');
        editBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Ambil data dari tombol
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const desc = this.getAttribute('data-desc');

                // Masukkan ke dalam form modal
                document.getElementById('edit_id').value = id;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_desc').value = desc;

                // Tampilkan Modal
                editModal.show();
            });
        });
    });

    function previewPlaylist(id, name) {
        document.getElementById('previewLabel').innerText = name;
        var url = "<?= site_url('playlists/play/') ?>" + id;
        document.getElementById('previewFrame').src = url;
        previewModal.show();
    }
</script>