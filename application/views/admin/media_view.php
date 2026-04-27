<div class="row">
    <div class="col-md-4">
        
        <div class="card p-4 mb-3">
            <h5 class="card-title fw-bold mb-3"><i class="fas fa-folder-plus me-2"></i>Buat Folder</h5>
            <form action="<?= base_url('media/create_folder') ?>" method="post">
                <div class="input-group">
                    <input type="text" name="folder_name" class="form-control" placeholder="Nama Folder..." required>
                    <button class="btn btn-dark" type="submit">Buat</button>
                </div>
            </form>
        </div>

        <div class="card p-4 mb-3 border-primary">
            <h5 class="card-title fw-bold mb-3 text-primary">
                <i class="fas fa-folder-open me-2"></i>Upload 1 Folder Full
            </h5>
            
            <?php echo form_open_multipart('media/upload_folder_bulk'); ?>
                
                <div class="mb-3">
                    <label class="form-label">Beri Nama Folder Baru</label>
                    <input type="text" name="new_folder_name" class="form-control" placeholder="Misal: Event Januari" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pilih Folder dari Komputer</label>
                    <input type="file" name="folder_files[]" class="form-control" webkitdirectory directory multiple required>
                    <small class="text-muted" style="font-size: 11px;">
                        *Semua Video, Gambar, & PDF di dalam folder akan diupload otomatis.
                    </small>
                </div>

                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fas fa-cloud-upload-alt me-1"></i> UPLOAD FOLDER
                </button>

            <?php echo form_close(); ?>
        </div>

       <div class="card p-4 mb-3">
            <h5 class="card-title fw-bold mb-3"><i class="fas fa-plus-circle me-2"></i>Upload File Tambahan</h5>

            <?php echo form_open_multipart('media/upload'); ?>
            
            <input type="hidden" name="redirect_playlist_id" value=""> 

            <div class="mb-3">
                <label class="form-label">Simpan di Folder</label>
                <select name="target_folder_id" class="form-control bg-light">
                    <option value="0" <?= ($current_folder_id == 0) ? 'selected' : '' ?>>
                        --- Root (Halaman Utama) ---
                    </option>
                    
                    <?php foreach($all_folders_list as $af): ?>
                        <option value="<?= $af->id ?>" <?= ($current_folder_id == $af->id) ? 'selected' : '' ?>>
                            📁 <?= $af->name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted" style="font-size: 11px;">Pilih lokasi penyimpanan file ini.</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Judul File</label>
                <input type="text" name="title" class="form-control" placeholder="Judul..." required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">File</label>
                <input type="file" name="userfile" class="form-control" accept=".mp4,.jpg,.jpeg,.png,.pdf" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Durasi (Detik)</label>
                <input type="number" name="duration" class="form-control" placeholder="Auto">
            </div>
            
            <button type="submit" class="btn btn-dark w-100">UPLOAD SATUAN</button>
            <?php echo form_close(); ?>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card p-4">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="card-title fw-bold mb-0">
                        <?php if(isset($current_folder_id) && $current_folder_id != 0): ?>
                            <a href="<?= base_url('media') ?>" class="btn btn-sm btn-secondary me-2"><i class="fas fa-arrow-left"></i></a>
                        <?php endif; ?>
                        
                        <?= isset($current_folder_name) ? $current_folder_name : 'Semua Media' ?>
                    </h5>
                </div>

               <div class="btn-group">
                    <a href="<?= base_url('media') ?>" 
                       class="btn btn-sm <?= ($current_filter == '' || $current_filter == null) ? 'btn-primary active' : 'btn-outline-primary' ?>">
                       Semua
                    </a>

                    <a href="<?= base_url('media?filter=folder') ?>" 
                       class="btn btn-sm <?= ($current_filter == 'folder') ? 'btn-primary active' : 'btn-outline-primary' ?>">
                       Folder
                    </a>

                    <a href="<?= base_url('media?filter=video') ?>" 
                       class="btn btn-sm <?= ($current_filter == 'video') ? 'btn-primary active' : 'btn-outline-primary' ?>">
                       Video
                    </a>

                    <a href="<?= base_url('media?filter=image') ?>" 
                       class="btn btn-sm <?= ($current_filter == 'image') ? 'btn-primary active' : 'btn-outline-primary' ?>">
                       Gambar
                    </a>

                    <a href="<?= base_url('media?filter=pdf') ?>" 
                       class="btn btn-sm <?= ($current_filter == 'pdf') ? 'btn-primary active' : 'btn-outline-primary' ?>">
                       PDF
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Preview</th>
                            <th>Info</th>
                            <th>Tipe</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($folders)): ?>
                            <?php foreach($folders as $f): ?>
                           <tr class="table-light">
                                <td class="text-center"><i class="fas fa-folder text-warning"></i></td>
                                <td class="text-center">
                                    <a href="<?= base_url('media?folder_id='.$f->id) ?>">
                                        <i class="fas fa-folder fa-3x text-warning"></i>
                                    </a>
                                </td>
                                <td>
                                    <strong><a href="<?= base_url('media?folder_id='.$f->id) ?>" class="text-dark text-decoration-none"><?= $f->name ?></a></strong>
                                    <br><small class="text-muted">Folder Penyimpanan</small>
                                </td>
                                <td><span class="badge bg-secondary">DIR</span></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning mb-1"
    onclick="openEditFolder('<?= $f->id ?>', '<?= $f->name ?>', '<?= isset($f->pdf_file) ? $f->pdf_file : '' ?>')">
    <i class="fas fa-edit"></i>
</button>

                                    <a href="<?= site_url('media/delete_folder/' . $f->id) ?>"
                                        class="btn btn-sm btn-danger mb-1"
                                        onclick="return confirm('PERINGATAN: Menghapus folder akan menghapus SEMUA FILE di dalamnya secara permanen. Lanjutkan?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php 
                        $no = 1;
                        if(empty($media_list) && empty($folders)) {
                            echo '<tr><td colspan="5" class="text-center py-4">Folder ini kosong.</td></tr>';
                        }
                        foreach ($media_list as $m): ?>
                            <tr>
                                <td><?= $no++ ?></td>

                                <td>
                                    <div style="cursor: pointer; position: relative;"
                                        onclick="previewMedia('<?= base_url($m->file_path) ?>', '<?= $m->file_type ?>', '<?= $m->title ?>')">

                                        <?php if ($m->file_type == 'image'): ?>
                                            <img src="<?= base_url($m->file_path) ?>" class="img-thumb-list" style="width: 80px; height: 50px; object-fit: cover; border-radius: 5px;">
                                        
                                        <?php elseif ($m->file_type == 'video'): ?>
                                            <div style="width: 80px; height: 50px; background: #000; border-radius: 5px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                                <video src="<?= base_url($m->file_path) ?>" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.7;"></video>
                                                <i class="fas fa-play-circle text-white fa-lg" style="position: absolute; z-index: 2;"></i>
                                            </div>

                                        <?php elseif ($m->file_type == 'pdf'): ?>
                                            <div style="width: 80px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                </td>
                                <td>
                                    <strong><?= $m->title ?></strong><br>
                                    <small class="text-muted">
                                        <?php if($m->file_type == 'pdf'): ?>
                                            Dokumen PDF
                                        <?php else: ?>
                                            Durasi: <?= $m->duration ?> detik
                                        <?php endif; ?>
                                    </small><br>
                                    <?php if ($m->caption): ?>
                                        <small class="text-success"><i class="fas fa-comment-alt"></i> <?= substr($m->caption, 0, 20) ?>...</small>
                                    <?php else: ?>
                                        <small class="text-secondary">Tanpa Caption</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                        $badgeColor = 'secondary';
                                        if($m->file_type == 'video') $badgeColor = 'info';
                                        if($m->file_type == 'image') $badgeColor = 'warning';
                                        if($m->file_type == 'pdf') $badgeColor = 'danger';
                                    ?>
                                    <span class="badge bg-<?= $badgeColor ?>">
                                        <?= strtoupper($m->file_type) ?>
                                    </span>
                                </td>
                                <td>
                                  <button type="button" class="btn btn-sm btn-warning btn-edit mb-1"
    data-id="<?= $m->id ?>"
    data-title="<?= $m->title ?>"
    data-caption="<?= $m->caption ?>"
    data-duration="<?= $m->duration ?>"
    data-folder-id="<?= $m->folder_id ?>"  data-bs-toggle="modal" data-bs-target="#editModal">
    <i class="fas fa-edit"></i>
</button>

                                    <a href="<?= site_url('media/delete/' . $m->id) ?>"
                                        class="btn btn-sm btn-danger mb-1"
                                        onclick="return confirm('Yakin hapus file ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="previewTitle">Preview</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closePreview()"></button>
            </div>
            <div class="modal-body text-center p-0" id="previewBody" style="min-height: 300px; display: flex; align-items: center; justify-content: center; background: black;">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Data Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('media/update') ?>" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">

                    <div class="mb-3">
                        <label class="form-label">Judul File</label>
                        <input type="text" name="title" id="edit_title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lokasi Folder</label>
                        <select name="folder_id" id="edit_folder_id" class="form-control">
                            <option value="0">Root (Halaman Utama)</option>
                            <?php foreach($all_folders_list as $af): ?>
                                <option value="<?= $af->id ?>"><?= $af->name ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Pilih folder untuk memindahkan file ini.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Caption</label>
                        <textarea name="caption" id="edit_caption" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Durasi (Detik)</label>
                        <input type="number" name="duration" id="edit_duration" class="form-control" required>
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

<div class="modal fade" id="editFolderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Setting Folder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('media/update_folder') ?>" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="folder_edit_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Folder</label>
                        <input type="text" name="name" id="folder_edit_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pilih PDF Stok (QR Code)</label>
                        <select name="selected_pdf" id="folder_edit_pdf" class="form-select">
                            <option value="">-- Tidak Ada QR Code --</option>
                            <?php if(!empty($existing_pdfs)): ?>
                                <?php foreach($existing_pdfs as $pdf): ?>
                                    <option value="<?= $pdf->file_path ?>">
                                        📄 <?= $pdf->title ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="text-muted">Pilih file PDF yang sudah pernah diupload sebelumnya.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // --- SETUP VARIABEL ---
    var previewModalObj; 

    document.addEventListener("DOMContentLoaded", function() {
        // Init Button Edit
      const editButtons = document.querySelectorAll('.btn-edit');
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const title = this.getAttribute('data-title');
                const caption = this.getAttribute('data-caption');
                const duration = this.getAttribute('data-duration');
                const folderId = this.getAttribute('data-folder-id'); // <-- Tangkap ID Folder

                document.getElementById('edit_id').value = id;
                document.getElementById('edit_title').value = title;
                document.getElementById('edit_caption').value = caption;
                document.getElementById('edit_duration').value = duration;
                document.getElementById('edit_folder_id').value = folderId; // <-- Set Dropdown
            });
        });

        // Init Modal Preview
        var modalElement = document.getElementById('previewModal');
        if (modalElement) {
            previewModalObj = new bootstrap.Modal(modalElement, {
                keyboard: false,
                backdrop: 'static'
            });

            modalElement.addEventListener('hidden.bs.modal', function () {
                document.getElementById('previewBody').innerHTML = '';
            });
        }
    });

    // --- FUNGSI FILTER (UPDATED PDF) ---
    /*function filterMedia(category, btnElement) {
        let buttons = document.querySelectorAll('.btn-group .btn');
        buttons.forEach(btn => {
            btn.classList.remove('active');
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-primary');
        });

        btnElement.classList.remove('btn-outline-primary');
        btnElement.classList.add('btn-primary');
        btnElement.classList.add('active');

        let rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => {
            // Jangan filter baris Folder (yg punya icon folder)
            if(row.innerHTML.includes('fa-folder')) {
                row.style.display = '';
                return; 
            }

            let textInRow = row.innerText.toLowerCase();
            if (category === 'all') {
                row.style.display = '';
            } else {
                // Trik simpel: Cek class badge atau text tipe
                if (textInRow.includes(category)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }*/

    // --- FUNGSI PREVIEW (UPDATED PDF) ---
    function previewMedia(url, type, title) {
        const modalBody = document.getElementById('previewBody');
        const modalTitle = document.getElementById('previewTitle');
        
        modalTitle.innerText = title;

        if (type === 'video') {
            modalBody.innerHTML = `
                <video controls autoplay style="max-width: 100%; max-height: 80vh; outline: none;">
                    <source src="${url}" type="video/mp4">
                    Browser Anda tidak mendukung tag video.
                </video>
            `;
        } else if (type === 'image') {
            modalBody.innerHTML = `
                <img src="${url}" style="max-width: 100%; max-height: 80vh; object-fit: contain;">
            `;
        } else if (type === 'pdf') {
            // Untuk PDF, kita kasih opsi Buka di Tab Baru atau Embed
            modalBody.innerHTML = `
                <div class="text-center p-5">
                    <i class="fas fa-file-pdf fa-5x text-danger mb-3"></i><br>
                    <p>File PDF tidak bisa dipreview di popup ini.</p>
                    <a href="${url}" target="_blank" class="btn btn-danger">
                        <i class="fas fa-external-link-alt"></i> Buka PDF di Tab Baru
                    </a>
                </div>
            `;
        }

        if(previewModalObj) previewModalObj.show();
    }

    function closePreview() {
        if(previewModalObj) {
            previewModalObj.hide();
            document.getElementById('previewBody').innerHTML = '';
        }
    }

    // --- FUNGSI EDIT FOLDER ---
   function openEditFolder(id, name, pdfFile) {
        document.getElementById('folder_edit_id').value = id;
        document.getElementById('folder_edit_name').value = name;
        
        // Auto-select dropdown sesuai data database
        var selectBox = document.getElementById('folder_edit_pdf');
        if (pdfFile && pdfFile !== '') {
            selectBox.value = pdfFile; 
        } else {
            selectBox.value = ""; 
        }
        
        var myModal = new bootstrap.Modal(document.getElementById('editFolderModal'));
        myModal.show();
    }
</script>