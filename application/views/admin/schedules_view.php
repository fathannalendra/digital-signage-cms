<div class="row">
    <?php
    $tv_lookup = [];
    if(!empty($tvs)) {
        foreach($tvs as $t) {
            $tv_lookup[$t->id] = $t->name; // Contoh: [1 => "TV Depan", 2 => "TV Kantin"]
        }
    }
    ?>

    <div class="col-md-4">
        <div class="card p-4 mb-4 shadow-sm">
            <h5 class="fw-bold mb-3"><i class="fas fa-calendar-plus"></i> Atur Jadwal Baru</h5>

            <form action="<?= site_url('schedules/create') ?>" method="POST">

                <div class="mb-3">
                    <label class="form-label">Pilih Playlist</label>
                    <select name="playlist_id" class="form-select" required>
                        <option value="">-- Pilih Playlist --</option>
                        <?php foreach ($playlists as $p): ?>
                            <option value="<?= $p->id ?>"><?= $p->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label d-block">Target Tayang</label>
                    <div class="card p-2" style="max-height: 150px; overflow-y: auto;">
                        <div class="form-check bg-light border-bottom mb-2 pb-2">
                            <input class="form-check-input" type="checkbox" name="target_tv_id[]" value="0" id="tv_all">
                            <label class="form-check-label fw-bold text-primary" for="tv_all">
                                🌐 PILIH SEMUA TV
                            </label>
                        </div>
                        <div id="tv_list_container">
                            <?php foreach ($tvs as $tv): ?>
                                <div class="form-check">
                                    <input class="form-check-input tv-item" type="checkbox" name="target_tv_id[]" value="<?= $tv->id ?>" id="tv_<?= $tv->id ?>">
                                    <label class="form-check-label" for="tv_<?= $tv->id ?>">
                                        📺 <?= $tv->name ?> <small class="text-muted">(<?= $tv->location ?>)</small>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mulai Tanggal</label>
                    <input type="date" name="start_date" class="form-control" required value="<?= date('Y-m-d') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <div class="input-group">
                        <input type="date" name="end_date" id="end_date" class="form-control" required>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="is_forever" id="is_forever" value="1">
                        <label class="form-check-label text-primary fw-bold" for="is_forever">
                            ♾️ Tayang Selamanya (Default)
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold">SIMPAN JADWAL</button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card p-4 shadow-sm">
            <h5 class="fw-bold mb-3">Jadwal Tayang Aktif</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Playlist</th>
                            <th width="30%">Target TV</th> <th>Periode Tayang</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($schedules)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-3">Belum ada jadwal diatur.</td></tr>
                        <?php else: ?>
                            <?php foreach ($schedules as $s): ?>
                                <?php
                                // LOGIKA STATUS
                                $today = date('Y-m-d');
                                $is_started = ($today >= $s->start_date);
                                $is_ended   = ($s->end_date != NULL && $today > $s->end_date);
                                $is_running = ($is_started && !$is_ended && $s->is_active);
                                ?>
                                <tr class="<?= $is_running ? 'table-success' : '' ?>">
                                    <td>
                                        <strong><?= $s->playlist_name ?></strong>
                                        <?php if ($is_running): ?>
                                            <br><span class="badge bg-success blink">SEDANG TAYANG</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        // Pecah string ID menjadi Array (Misal: "1,2,3,4")
                                        $raw_targets = explode(',', $s->target_tv_id);
                                        
                                      
                                        if (in_array('0', $raw_targets)): ?>
                                            <span class="badge bg-dark w-100 py-2">🌐 SEMUA TV</span>
                                        
                                        <?php else: ?>
                                            <?php 
                                       
                                            $valid_targets_name = [];
                                            foreach($raw_targets as $tid) {
                                                if(isset($tv_lookup[$tid])) {
                                                    $valid_targets_name[] = $tv_lookup[$tid]; 
                                                }
                                            }
                                            ?>

                                            <?php if(count($valid_targets_name) > 0): ?>
                                                <span class="badge bg-info text-dark mb-1"><?= count($valid_targets_name) ?> Titik TV</span>
                                                <div class="small text-muted border-top pt-1 mt-1">
                                                    <?php 
                                                    // Loop nama TV yang valid saja
                                                    foreach($valid_targets_name as $name) {
                                                        echo '<i class="fas fa-tv fa-xs"></i> ' . $name . '<br>';
                                                    }
                                                    ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Target Tidak Ditemukan</span>
                                                <div class="small text-danger" style="font-size: 0.75rem;">
                                                    Semua TV di jadwal ini telah dihapus.
                                                </div>
                                            <?php endif; ?>

                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <i class="fas fa-play text-success me-1"></i> <?= date('d M Y', strtotime($s->start_date)) ?> <br>
                                        <?php if($s->end_date == NULL): ?>
                                            <span class="text-primary fw-bold">♾️ SELAMANYA</span>
                                        <?php else: ?>
                                            <i class="fas fa-stop text-danger me-1"></i> <?= date('d M Y', strtotime($s->end_date)) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('schedules/toggle_status/' . $s->id . '/' . $s->is_active) ?>"
                                            class="badge text-decoration-none <?= $s->is_active ? 'bg-primary' : 'bg-secondary' ?>">
                                            <?= $s->is_active ? 'AKTIF' : 'NON-AKTIF' ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('schedules/edit/' . $s->id) ?>" class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i></a>
                                        <a href="<?= site_url('schedules/delete/' . $s->id) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus jadwal ini?')"><i class="fas fa-trash"></i></a>
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

<style>
    @keyframes blinker { 50% { opacity: 0; } }
    .blink { animation: blinker 1.5s linear infinite; }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const checkAll = document.getElementById('tv_all');
        const items = document.querySelectorAll('.tv-item');
        const checkForever = document.getElementById('is_forever');
        const inputEndDate = document.getElementById('end_date');

        if(checkAll && items.length > 0) {
            checkAll.addEventListener('change', function() {
                items.forEach(item => item.checked = this.checked);
            });
            items.forEach(item => {
                item.addEventListener('change', function() {
                    const totalItems = items.length;
                    const checkedItems = document.querySelectorAll('.tv-item:checked').length;
                    checkAll.checked = (totalItems === checkedItems);
                });
            });
        }

        if(checkForever && inputEndDate) {
            checkForever.addEventListener('change', function() {
                if(this.checked) {
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