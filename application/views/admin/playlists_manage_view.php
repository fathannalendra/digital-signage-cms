<div class="row">
    <div class="col-md-5">
        <div class="card p-3 shadow-sm border-0 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-photo-video me-2"></i>Media Tersedia</h5>

                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#quickUploadModal">
                    <i class="fas fa-plus me-1"></i> Upload
                </button>
            </div>

            <form action="" method="GET" class="mb-3">
                <div class="input-group">
                    <select name="folder_id" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Tampilkan Semua File --</option>
                        <?php foreach ($folders as $f): ?>
                            <option value="<?= $f->id ?>" <?= ($selected_folder_id == $f->id) ? 'selected' : '' ?>>
                                📁 Folder: <?= $f->name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>

            <?php if ($selected_folder_id): ?>
                <div class="alert alert-warning p-2 mb-3 text-center">
                    <small>Ingin memasukkan semua isi folder ini?</small><br>
                    <a href="<?= site_url('playlists/add_folder_items/' . $playlist->id . '/' . $selected_folder_id) ?>"
                        class="btn btn-sm btn-dark w-100 mt-1 fw-bold"
                        onclick="return confirm('Yakin masukkan semua file dari folder ini ke playlist?')">
                        <i class="fas fa-layer-group me-1"></i> MASUKKAN SEMUA KE PLAYLIST
                    </a>
                </div>
            <?php else: ?>
                <div class="alert alert-info py-2 small">
                    Pilih folder di atas untuk memasukkan banyak file sekaligus.
                </div>
            <?php endif; ?>

            <div style="height: 450px; overflow-y: auto; padding-right: 5px;">
                <?php if (empty($all_media)): ?>
                    <p class="text-center text-muted py-5">Tidak ada media di folder ini.</p>
                <?php endif; ?>

                <?php foreach ($all_media as $m): ?>
                    <div class="d-flex align-items-center border-bottom py-2">

                        <div class="me-2" style="cursor: pointer; position: relative;"
                            onclick="previewMedia('<?= base_url($m->file_path) ?>', '<?= $m->file_type ?>', '<?= $m->title ?>')">
                            <?php if ($m->file_type == 'image'): ?>
                                <img src="<?= base_url($m->file_path) ?>" style="width:70px; height:45px; object-fit:cover; border-radius:4px;">
                            <?php elseif ($m->file_type == 'pdf'): ?>
                                <div style="width:70px; height:45px; background:#dc3545; color:#fff; display:flex; align-items:center; justify-content:center; border-radius:4px;">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </div>
                            <?php else: ?>
                                <div style="width:70px; height:45px; background:#000; color:#fff; display:flex; align-items:center; justify-content:center; border-radius:4px; font-size:0.7rem; position: relative;">
                                    <video src="<?= base_url($m->file_path) ?>" style="width:100%; height:100%; object-fit:cover; opacity: 0.6; position: absolute;"></video>
                                    <i class="fas fa-play text-white" style="position: relative; z-index: 2;"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex-grow-1" style="line-height: 1.2;">
                            <div class="fw-bold" style="font-size:0.9rem;"><?= substr($m->title, 0, 25) ?></div>
                            <div class="text-muted" style="font-size:0.75rem;">
                                <span class="badge bg-secondary"><?= strtoupper($m->file_type) ?></span> <?= $m->duration ?>s
                            </div>
                        </div>

                        <a href="<?= site_url('playlists/add_item/' . $playlist->id . '/' . $m->id) ?>" class="btn btn-sm btn-outline-primary rounded-circle" style="width: 32px; height: 32px; padding: 0; line-height: 30px;">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card p-4 shadow-sm border-primary">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-primary mb-0">Isi Playlist: <?= $playlist->name ?></h5>
                    <small class="text-muted">Tarik baris (Drag & Drop) untuk ubah urutan.</small>
                </div>
                <a href="<?= site_url('playlists') ?>" class="btn btn-sm btn-secondary">Kembali</a>
            </div>

            <form action="<?= site_url('playlists/update_order/' . $playlist->id) ?>" method="POST" id="playlistForm">

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%" class="text-center">
                                    <input type="checkbox" id="checkAll" style="cursor: pointer;">
                                </th>
                                <th width="10%">Urutan</th>
                                <th>Media</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="sortable-list">
                            <?php if (empty($existing_items)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Playlist ini masih kosong.</td>
                                </tr>
                            <?php else: ?>

                                <?php
                                $no = 1;
                                foreach ($existing_items as $item):
                                ?>
                                    <tr class="draggable-item" style="cursor: grab;">
                                        <td class="text-center bg-light">
                                            <input type="checkbox" name="selected_items[]" value="<?= $item->item_id ?>" class="item-checkbox form-check-input">
                                        </td>
                                        <td>
                                            <input type="number" name="items[<?= $item->item_id ?>]" value="<?= $no++ ?>"
                                                class="form-control form-control-sm text-center fw-bold sort-number" readonly>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3 text-secondary">
                                                    <i class="fas fa-bars"></i>
                                                </div>
                                                <div class="me-2 text-muted">
                                                    <i class="fas fa-<?= $item->file_type == 'video' ? 'video' : 'image' ?>"></i>
                                                </div>
                                                <div>
                                                    <strong><?= $item->title ?></strong>

                                                    <div style="font-size: 0.75rem; margin-top: 2px;">
                                                    <?php 
                                                    // 1. Definisikan pilihan warna (Bootstrap Classes)
                                                    $colors = [
                                                        'bg-primary',       // Biru
                                                        'bg-success',       // Hijau
                                                        'bg-danger',        // Merah
                                                        'bg-info text-dark',// Biru Langit
                                                        'bg-dark',          // Hitam
                                                        'bg-secondary',     // Abu-abu
                                                        'bg-warning text-dark' // Kuning
                                                    ];
                                                    
                                                    if($item->folder_name): 
                                                        // 2. Pilih warna berdasarkan ID Folder (Pake Modulo/Sisa Bagi)
                                                        // folder_id 1 dpt warna ke-1, folder_id 8 dpt warna ke-1 lagi, dst.
                                                        $idx = $item->folder_id % count($colors);
                                                        $badge_class = $colors[$idx];
                                                    ?>
                                                        <span class="badge <?= $badge_class ?> border">
                                                            <i class="fas fa-folder-open"></i> <?= $item->folder_name ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-secondary border">
                                                            <i class="fas fa-folder"></i> Root/Utama
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                    <?php if ($item->caption): ?>
                                                        <small class="text-muted fst-italic d-block">"<?= substr($item->caption, 0, 30) ?>..."</small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="<?= site_url('playlists/remove_item/' . $item->item_id . '/' . $playlist->id) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus item ini?')">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (!empty($existing_items)): ?>
                    <div class="d-flex justify-content-between mt-3">
                        <button type="submit"
                            formaction="<?= site_url('playlists/bulk_delete_items/' . $playlist->id) ?>"
                            class="btn btn-danger"
                            onclick="return confirm('Yakin hapus item yang dicentang?')">
                            <i class="fas fa-trash me-2"></i> Hapus Terpilih
                        </button>

                        <button type="submit" class="btn btn-success fw-bold">
                            <i class="fas fa-save me-2"></i> SIMPAN URUTAN
                        </button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>


</div>

<div class="modal fade" id="quickUploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Upload Media Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <?php echo form_open_multipart('media/upload'); ?>
            <div class="modal-body">

                <input type="hidden" name="redirect_playlist_id" value="<?= $playlist->id ?>">

                <div class="mb-3">
                    <label class="form-label">Judul File</label>
                    <input type="text" name="title" class="form-control" required placeholder="Contoh: Iklan Promo">
                </div>
                <div class="mb-3">
                    <label class="form-label">File (Video/Gambar)</label>
                    <input type="file" name="userfile" class="form-control" required>
                    <small class="text-muted">Format: MP4, JPG, PNG</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Caption (Opsional)</label>
                    <textarea name="caption" class="form-control" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Durasi (Detik)</label>
                    <input type="number" name="duration" class="form-control" placeholder="Biarkan kosong untuk default">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Upload & Simpan</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header border-0 py-2">
                <h6 class="modal-title" id="previewTitle">Preview</h6>
                <button type="button" class="btn-close btn-close-white" onclick="closePreview()"></button>
            </div>
            <div class="modal-body text-center p-0 bg-black d-flex align-items-center justify-content-center" id="previewBody" style="min-height: 300px;">
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // 1. LOGIKA DRAG & DROP
        var el = document.getElementById('sortable-list');
        if (el) {
            new Sortable(el, {
                animation: 150,
                handle: '.draggable-item', // Area yang bisa didrag (seluruh baris)
                onEnd: function(evt) {
                    // Setelah didrop, urutkan ulang angka 1, 2, 3...
                    updateNumbers();
                }
            });
        }

        function updateNumbers() {
            var inputs = document.querySelectorAll('.sort-number');
            inputs.forEach(function(input, index) {
                input.value = index + 1; // Set urutan baru (1, 2, 3...)
            });
        }

        // 2. LOGIKA CHECK ALL
        var checkAll = document.getElementById('checkAll');
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                var checkboxes = document.querySelectorAll('.item-checkbox');
                checkboxes.forEach(function(cb) {
                    cb.checked = checkAll.checked;
                });
            });
        }
    });
</script>
<script>
    var previewModalObj;

    document.addEventListener("DOMContentLoaded", function() {
        // Siapkan Modal Preview
        var modalEl = document.getElementById('previewModal');
        if (modalEl) {
            previewModalObj = new bootstrap.Modal(modalEl, {
                keyboard: false,
                backdrop: 'static'
            });
            // Stop video saat modal ditutup
            modalEl.addEventListener('hidden.bs.modal', function() {
                document.getElementById('previewBody').innerHTML = '';
            });
        }
    });

    function previewMedia(url, type, title) {
        const body = document.getElementById('previewBody');
        document.getElementById('previewTitle').innerText = title;

        if (type === 'video') {
            body.innerHTML = `<video controls autoplay style="max-width:100%; max-height:70vh;"><source src="${url}" type="video/mp4"></video>`;
        } else {
            body.innerHTML = `<img src="${url}" style="max-width:100%; max-height:70vh; object-fit:contain;">`;
        }
        if (previewModalObj) previewModalObj.show();
    }

    function closePreview() {
        if (previewModalObj) {
            previewModalObj.hide();
            document.getElementById('previewBody').innerHTML = '';
        }
    }
</script>