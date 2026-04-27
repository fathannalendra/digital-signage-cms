<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card p-4 shadow-sm border-warning">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-edit"></i> Edit Jadwal</h5>
                <a href="<?= site_url('schedules') ?>" class="btn btn-sm btn-secondary">Batal / Kembali</a>
            </div>

            <form action="<?= site_url('schedules/update/' . $schedule->id) ?>" method="POST">

                <div class="mb-3">
                    <label class="form-label">Playlist</label>
                    <div class="input-group">

                        <input type="hidden" name="playlist_id" id="playlist_selector" value="<?= $schedule->playlist_id ?>">

                        <?php
                        // Cari nama playlist yang sedang dipakai sekarang
                        $current_playlist_name = '-';
                        foreach ($playlists as $p) {
                            if ($p->id == $schedule->playlist_id) {
                                $current_playlist_name = $p->name;
                                break;
                            }
                        }
                        ?>
                        <input type="text" class="form-control bg-light fw-bold text-dark" value="<?= $current_playlist_name ?>" readonly>

                        <button type="button" class="btn btn-outline-primary" onclick="openPlaylistManager()">
                            <i class="fas fa-photo-video"></i> Kelola Isi
                        </button>
                    </div>
                    <small class="text-muted">Klik "Kelola Isi" untuk mengedit video di dalamnya.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label d-block">Target Tayang</label>
                    <div class="card p-3 border-secondary" style="background:#fdfdfe; max-height: 200px; overflow-y: auto;">

                        <?php
                        $selected_targets = explode(',', $schedule->target_tv_id);
                        if (empty($selected_targets)) $selected_targets = [];
                        ?>

                        <div class="form-check mb-2 border-bottom pb-2">
                            <input class="form-check-input" type="checkbox" name="target_tv_id[]" value="0" id="edit_tv_all">
                            <label class="form-check-label fw-bold text-primary" for="edit_tv_all">
                                🌐 PILIH SEMUA TV
                            </label>
                        </div>

                        <?php foreach ($tvs as $tv): ?>
                            <div class="form-check">
                                <input class="form-check-input edit-tv-item" type="checkbox" name="target_tv_id[]" value="<?= $tv->id ?>" id="edit_tv_<?= $tv->id ?>"
                                    <?= in_array($tv->id, $selected_targets) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="edit_tv_<?= $tv->id ?>">
                                    📺 <?= $tv->name ?>
                                </label>
                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mulai Tanggal</label>
                        <input type="date" name="start_date" class="form-control" required value="<?= $schedule->start_date ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Sampai Tanggal</label>
                        <?php $is_forever = ($schedule->end_date == NULL); ?>
                        <div class="input-group">
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                value="<?= $is_forever ? '' : $schedule->end_date ?>"
                                <?= $is_forever ? 'disabled' : 'required' ?>>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="is_forever" id="is_forever" value="1" <?= $is_forever ? 'checked' : '' ?>>
                            <label class="form-check-label text-primary fw-bold" for="is_forever">
                                ♾️ Tayang Selamanya
                            </label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-warning w-100 fw-bold"><i class="fas fa-save"></i> SIMPAN PERUBAHAN</button>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="playlistManagerModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Isi Playlist (Quick Mode)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="playlistFrame" src="" style="width: 100%; height: 600px; border: none;"></iframe>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup & Lanjut Simpan Jadwal</button>
            </div>
        </div>
    </div>
</div>

<script>
    // --- 1. LOGIKA BUKA MODAL ---
    var modalManager;
    document.addEventListener("DOMContentLoaded", function() {
        var modalEl = document.getElementById('playlistManagerModal');
        if (modalEl) {
            modalManager = new bootstrap.Modal(modalEl, {
                backdrop: 'static'
            });
        }
    });

    function openPlaylistManager() {
        // Ambil ID playlist yang sedang dipilih
        var playlistId = document.getElementById('playlist_selector').value;

        if (!playlistId) {
            alert("Harap pilih Playlist terlebih dahulu!");
            return;
        }

        var url = "<?= site_url('playlists/manage/') ?>" + playlistId + "?mode=simple";

        document.getElementById('playlistFrame').src = url;

        // Tampilkan Modal
        modalManager.show();
    }


    document.addEventListener("DOMContentLoaded", function() {
        // Check All TV
        const checkAll = document.getElementById('edit_tv_all');
        const items = document.querySelectorAll('.edit-tv-item');
        if (checkAll && items.length > 0) {
            const totalItems = items.length;
            const checkedItemsInit = document.querySelectorAll('.edit-tv-item:checked').length;
            if (totalItems > 0 && totalItems === checkedItemsInit) checkAll.checked = true;

            checkAll.addEventListener('change', function() {
                items.forEach(item => item.checked = this.checked);
            });
            items.forEach(item => {
                item.addEventListener('change', function() {
                    checkAll.checked = (items.length === document.querySelectorAll('.edit-tv-item:checked').length);
                });
            });
        }

        // Forever Checkbox
        const checkForever = document.getElementById('is_forever');
        const inputEndDate = document.getElementById('end_date');
        if (checkForever && inputEndDate) {
            checkForever.addEventListener('change', function() {
                if (this.checked) {
                    inputEndDate.value = '';
                    inputEndDate.disabled = true;
                    inputEndDate.removeAttribute('required');
                } else {
                    inputEndDate.disabled = false;
                    inputEndDate.setAttribute('required', 'required');
                }
            });
        }
    });
</script>