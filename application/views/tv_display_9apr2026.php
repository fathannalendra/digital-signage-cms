<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Showroom Display</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* --- CSS RESET --- */
        body,
        html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background-color: #000;
            overflow: hidden;
            font-family: 'Segoe UI', sans-serif;
        }

        /* ============================================================
           BAGIAN 1: DESAIN MENU & OVERLAY (DESAIN ASLI)
           ============================================================ */

        #tv-selector {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #0d47a1 0%, #000 100%);
            color: white;
            z-index: 2000;
        }

        .tv-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            width: 80%;
            max-width: 1000px;
            margin-top: 30px;
        }

        .btn-tv {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.5);
            color: white;
            padding: 30px;
            border-radius: 15px;
            font-size: 1.5rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .btn-tv:hover,
        .btn-tv:focus,
        .btn-tv.force-focus {
            background: #fff;
            color: #000;
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
            border-color: #fff;
            outline: none;
        }

        #overlay-start {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #000;
            z-index: 3000;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            color: white;
        }

        .btn-start {
            padding: 20px 60px;
            font-size: 30px;
            background: #0d47a1;
            color: white;
            border: 2px solid white;
            cursor: pointer;
            border-radius: 50px;
            margin-top: 30px;
            transition: all 0.3s;
        }

        .btn-start:hover,
        .btn-start:focus,
        .btn-start.force-focus {
            background: #fff;
            color: #0d47a1;
            transform: scale(1.1);
            outline: none;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.8);
        }

        #btn-manual-back {
            transition: all 0.2s;
        }

        #btn-manual-back:hover,
        #btn-manual-back:focus,
        #btn-manual-back.force-focus {
            background: #fff !important;
            color: #000 !important;
            border-color: #fff !important;
            transform: scale(1.1);
            outline: none;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
        }

        /* ============================================================
           BAGIAN 2: PLAYER & QR CODE (DESAIN ASLI 80PX)
           ============================================================ */

        #app-container {
            display: none;
            position: relative;
            width: 100%;
            height: 100%;
        }

        #media-area {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #000;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .media-element {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
            z-index: 1;
        }

        .media-element.show {
            opacity: 1;
            z-index: 2;
        }

        video.media-element {
            background: black;
        }

        /* --- QR BOX (ANIMASI BOLAK-BALIK) --- */
        #qr-box {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 50;

            background: rgba(255, 255, 255, 0.95);
            padding: 8px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            text-align: center;
            width: 100px; /* Ukuran Kecil */

            /* Animasi Default (Posisi Hilang/Di Atas) */
            display: block;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-80px); /* Geser ke atas */
            
            /* Transisi Halus 2.5 Detik untuk Masuk & Keluar */
            transition: all 2.5s ease-out;
        }

        /* Saat kelas .muncul ditambahkan via JS */
        #qr-box.muncul {
            opacity: 1;
            transform: translateY(0); /* Turun ke posisi normal */
            visibility: visible;
        }

        #qr-box img {
            width: 100%;
            height: auto;
            display: block;
            margin-bottom: 3px;
        }

        .qr-title {
            font-size: 10px;
            color: #000;
            font-weight: 800;
            margin-bottom: 2px;
            text-transform: uppercase;
            line-height: 1.1;
            word-wrap: break-word;
        }

        .qr-subtitle {
            font-size: 8px;
            color: #333;
            font-weight: 600;
        }

        /* --- CAPTION STYLE --- */
        #caption-area {
            position: absolute;
            bottom: 50px;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            background-color: rgba(0, 0, 0, 0.6);
            color: #fff;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            display: none;
            z-index: 10;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        #caption-text {
            font-size: 2.5vw;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* --- STANDBY SCREEN --- */
        #standby-screen {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #000;
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 5;
            color: #555;
        }

        .pulse-text {
            font-size: 2rem;
            font-weight: bold;
            text-transform: uppercase;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 0.3;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.3;
            }
        }
    </style>
</head>

<body>
<!--     
    <audio id="bg-music" loop>
        <source src="<?= base_url('uploads/music.mp3') ?>" type="audio/mpeg">
    </audio> -->

    <div id="tv-selector">
        <h1><i class="fas fa-satellite-dish"></i> PILIH LAYAR TV</h1>
        <p>Gunakan tombol Panah & OK di Remote untuk memilih</p>
        <div class="tv-grid">
            <?php foreach ($tv_list as $tv): ?>
                <button class="btn-tv" onclick="selectTv(<?= $tv->id ?>)">
                    <i class="fas fa-tv fa-2x"></i>
                    <span><?= $tv->name ?></span>
                    <small style="font-size: 0.8rem; font-weight: normal;"><?= $tv->location ?></small>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="overlay-start">
        <h1>SISTEM DIGITAL SHOWROOM</h1>
        <p>TV Siap. Tekan tombol OK untuk Fullscreen</p>
        <button id="btn-start" class="btn-start" onclick="startFullscreen()">MULAI TAYANGAN ▶</button>
    </div>

    <div id="app-container">
        <div id="media-area">
            <video id="main-video" class="media-element" playsinline></video>
            <img id="img-A" class="media-element" src="" alt="">
            <img id="img-B" class="media-element" src="" alt="">

            <div id="qr-box">
                <div class="qr-title">INFO STOK</div> <img id="qr-image" src="" alt="Scan Me">
                <div class="qr-subtitle">SCAN HERE</div>
            </div>
        </div>

        <div id="standby-screen">
            <i class="fas fa-clock fa-3x mb-3"></i>
            <div class="pulse-text">MENUNGGU JADWAL...</div>
        </div>

        <div id="caption-area">
            <div id="caption-text"></div>
        </div>
    </div>

    <script>
        // --- VARS ---
        var selectorEl = document.getElementById('tv-selector');
        var overlayEl = document.getElementById('overlay-start');
        var appEl = document.getElementById('app-container');
        var btnStart = document.getElementById('btn-start');

        var videoEl = document.getElementById('main-video');
        var imgA = document.getElementById('img-A');
        var imgB = document.getElementById('img-B');

        var captionArea = document.getElementById('caption-area');
        var captionText = document.getElementById('caption-text');
        var standbyScreen = document.getElementById('standby-screen');
        var qrBox = document.getElementById('qr-box');
        var qrImage = document.getElementById('qr-image');
        var activeOverlayBtn = 'start';
        
        // --- AUDIO ---
        // var bgMusic = document.getElementById('bg-music');
        // if(bgMusic) bgMusic.volume = 0.5;

        var playlist = [];
        var currentIndex = 0;
        var mediaTimeout = null;
        var currentTvId = null;
        var activeImg = 'A';

        // LOGIKA FOLDER
        var activeFolderId = null;
        var folderSlideCount = 0;

        // --- INIT ---
        window.onload = function() {
            window.focus();
            if (document.body) document.body.focus();
            checkUrlAndRender();
        };

        window.onpopstate = function(event) {
            checkUrlAndRender();
        };

        function checkUrlAndRender() {
            var urlParams = new URLSearchParams(window.location.search);
            currentTvId = urlParams.get('id');

            // RESET
            videoEl.pause();
            // if(bgMusic) bgMusic.pause();

            imgA.classList.remove('show');
            imgB.classList.remove('show');
            videoEl.classList.remove('show');
            
            // Saat Reset Hard (Ganti TV), boleh langsung hide tanpa animasi
            qrBox.classList.remove('muncul');
            qrBox.style.display = 'none'; 
            
            clearTimeout(mediaTimeout);

            if (currentTvId) {
                selectorEl.style.display = 'none';
                overlayEl.style.display = 'flex';
                appEl.style.display = 'none';
                addBackButton();
                highlightOverlayButton('start');
            } else {
                selectorEl.style.display = 'flex';
                overlayEl.style.display = 'none';
                appEl.style.display = 'none';
                removeBackButton();
                setTimeout(function() {
                    var firstBtn = document.querySelector('.btn-tv');
                    if (firstBtn) firstBtn.focus();
                }, 100);
            }
        }

        function selectTv(id) {
            if (currentTvId == id) return;
            var newUrl = "?id=" + id;
            window.history.pushState({
                path: newUrl
            }, '', newUrl);
            checkUrlAndRender();
        }

        function goBackToMenu() {
            var cleanUrl = window.location.pathname;
            window.history.pushState({
                path: cleanUrl
            }, '', cleanUrl);
            checkUrlAndRender();
        }

        function startFullscreen() {
            var el = document.documentElement;
            if (el.requestFullscreen) el.requestFullscreen();
            else if (el.webkitRequestFullscreen) el.webkitRequestFullscreen();

            overlayEl.style.display = 'none';
            appEl.style.display = 'block';
            
            // Play Audio
            // if(bgMusic) {
            //     bgMusic.play().catch(e => console.log("Gagal play audio awal:", e));
            // }

            loadPlaylist();
        }

        // --- PLAYER LOGIC ---
        function loadPlaylist() {
            var xhr = new XMLHttpRequest();
            var url = "<?= base_url('player_api') ?>?id=" + currentTvId + "&ts=" + new Date().getTime();

            xhr.open("GET", url, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            var res = JSON.parse(xhr.responseText);
                            if (res.status === 'success' && res.data.length > 0) {
                                playlist = res.data;
                                currentIndex = 0;

                                // Reset Counter
                                activeFolderId = null;
                                folderSlideCount = 0;

                                standbyScreen.style.display = 'none';
                                playSequence();
                            } else {
                                standbyScreen.style.display = 'flex';
                                setTimeout(loadPlaylist, 10000);
                            }
                        } catch (e) {
                            standbyScreen.style.display = 'flex';
                            setTimeout(loadPlaylist, 10000);
                        }
                    } else {
                        setTimeout(loadPlaylist, 10000);
                    }
                }
            };
            xhr.send();
        }

        function playSequence() {
            if (playlist.length === 0) return;

            clearTimeout(mediaTimeout);
            var item = playlist[currentIndex];

            // ==========================================
            // LOGIKA QR CODE & NAMA FOLDER (DENGAN ANIMASI KELUAR)
            // ==========================================

            // 1. Cek Ganti Folder
            if (item.folder_id !== activeFolderId) {
                // Folder Baru -> Reset
                activeFolderId = item.folder_id;
                folderSlideCount = 1;

                // HIDE QR: Cukup hapus class 'muncul'. CSS akan animasi naik ke atas.
                // JANGAN pakai display = 'none', nanti animasinya mati.
                qrBox.classList.remove('muncul');
            } else {
                // Folder Sama -> Tambah Hitungan
                folderSlideCount++;
            }

            // 2. Cek Syarat Tampil (Slide >= 2 DAN Ada PDF)
            var pdfPath = item.pdf_relative || item.pdf_file;

            if (folderSlideCount >= 2 && pdfPath && pdfPath !== "") {

                // --- UPDATE JUDUL ---
                var titleEl = qrBox.querySelector('.qr-title');
                if (titleEl) {
                    var folderName = item.folder_name ? item.folder_name : "INFO STOK";
                    titleEl.innerText = folderName;
                }

                // --- GENERATE QR ---
                var baseUrl = "<?= base_url() ?>"; 
                var fullPdfUrl = baseUrl + pdfPath;

                try {
                    var urlObj = new URL(fullPdfUrl);
                    urlObj.hostname = window.location.hostname; // Fix Localhost IP
                    var finalQrLink = urlObj.href;

                    var qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" + encodeURIComponent(finalQrLink);
                    qrImage.src = qrApiUrl;

                    // Pastikan Display Block (Tapi invisible krn opacity 0)
                    qrBox.style.display = 'block';

                    // ANIMASI MASUK (Delay dikit)
                    if (!qrBox.classList.contains('muncul')) {
                        setTimeout(function() {
                            qrBox.classList.add('muncul');
                        }, 100);
                    }

                } catch (e) { console.error(e); }

            } else {
                // HIDE QR (Animasi Keluar: Naik ke atas)
                qrBox.classList.remove('muncul');
            }
            // ==========================================

            // CAPTION
            if (item.caption && item.caption !== "" && item.caption !== "null") {
                captionText.innerHTML = item.caption;
                captionArea.style.display = 'block';
            } else {
                captionArea.style.display = 'none';
            }

            // TAMPILKAN MEDIA & MUSIK PINTAR
            if (item.type === 'video') {
                imgA.classList.remove('show');
                imgB.classList.remove('show');

                videoEl.src = item.src;
                videoEl.classList.add('show');

                // Video -> Musik Mati
                videoEl.muted = false;
                // if(bgMusic) bgMusic.pause(); 

                var p = videoEl.play();
                if (p !== undefined) {
                    p.catch(function(e) {
                        videoEl.muted = true;
                        videoEl.play();
                    });
                }

                videoEl.onended = nextIndex;
                videoEl.onerror = nextIndex;

            } else {
                videoEl.pause();
                videoEl.classList.remove('show');

                // Gambar -> Musik Nyala
                // if(bgMusic && bgMusic.paused) {
                //     bgMusic.play().catch(e => {}); 
                // }

                var nextImgEl = (activeImg === 'A') ? imgB : imgA;
                var prevImgEl = (activeImg === 'A') ? imgA : imgB;

                nextImgEl.src = item.src;

                nextImgEl.onload = function() {
                    nextImgEl.classList.add('show');
                    prevImgEl.classList.remove('show');
                    activeImg = (activeImg === 'A') ? 'B' : 'A';
                };

                nextImgEl.onerror = function() {
                    nextIndex();
                };

                var durasi = (parseInt(item.duration) || 10) * 1000;
                mediaTimeout = setTimeout(nextIndex, durasi);
            }
        }

        function nextIndex() {
            currentIndex++;
            if (currentIndex >= playlist.length) {
                loadPlaylist();
            } else {
                playSequence();
            }
        }

        function addBackButton() {
            if (document.getElementById('btn-manual-back')) return;
            var btnBack = document.createElement('button');
            btnBack.id = 'btn-manual-back';
            btnBack.innerHTML = '<i class="fas fa-arrow-left"></i> Ganti TV';
            btnBack.style.position = 'absolute';
            btnBack.style.top = '30px';
            btnBack.style.left = '30px';
            btnBack.style.zIndex = '4000';
            btnBack.style.padding = '15px 25px';
            btnBack.style.background = 'rgba(255,255,255,0.1)';
            btnBack.style.color = '#ccc';
            btnBack.style.border = '1px solid #ccc';
            btnBack.style.borderRadius = '30px';
            btnBack.style.cursor = 'pointer';
            btnBack.style.fontSize = '16px';
            btnBack.style.fontWeight = 'bold';
            btnBack.onclick = goBackToMenu;
            overlayEl.appendChild(btnBack);
        }

        function removeBackButton() {
            var btn = document.getElementById('btn-manual-back');
            if (btn) btn.remove();
        }

        function highlightOverlayButton(target) {
            var btnBack = document.getElementById('btn-manual-back');
            if (btnStart) btnStart.classList.remove('force-focus');
            if (btnBack) btnBack.classList.remove('force-focus');

            if (target === 'start') {
                activeOverlayBtn = 'start';
                if (btnStart) {
                    btnStart.classList.add('force-focus');
                    btnStart.focus();
                }
            } else {
                activeOverlayBtn = 'back';
                if (btnBack) {
                    btnBack.classList.add('force-focus');
                    btnBack.focus();
                }
            }
        }

        // --- REMOTE CONTROL ---
        document.addEventListener('keydown', function(e) {
            var code = e.keyCode;
            var key = e.key;
            var isEnter = (code === 13 || code === 29443 || key === 'Enter');
            var isBack = (code === 8 || code === 27 || code === 461 || code === 10009 || key === 'Escape' || key === 'Backspace');
            var isRight = (code === 39 || key === 'ArrowRight');
            var isLeft = (code === 37 || key === 'ArrowLeft');
            var isUp = (code === 38 || key === 'ArrowUp');
            var isDown = (code === 40 || key === 'ArrowDown');

            if (isBack) {
                if (appEl.style.display !== 'none') {
                    e.preventDefault();
                    videoEl.pause();
                    // if(bgMusic) bgMusic.pause();
                    clearTimeout(mediaTimeout);
                    appEl.style.display = 'none';
                    checkUrlAndRender();
                    return;
                }
                if (overlayEl.style.display !== 'none') {
                    e.preventDefault();
                    goBackToMenu();
                    return;
                }
            }

            if (selectorEl.style.display !== 'none') {
                var buttons = Array.from(document.querySelectorAll('.btn-tv'));
                var active = document.activeElement;
                var index = buttons.indexOf(active);

                if (index === -1) {
                    buttons[0].focus();
                    return;
                }

                if (isRight || isDown) {
                    e.preventDefault();
                    var nextIndex = (index + 1) % buttons.length;
                    buttons[nextIndex].focus();
                } else if (isLeft || isUp) {
                    e.preventDefault();
                    var prevIndex = (index - 1 + buttons.length) % buttons.length;
                    buttons[prevIndex].focus();
                } else if (isEnter) {
                    e.preventDefault();
                    active.click();
                }
            }

            if (overlayEl.style.display !== 'none') {
                if (isUp) {
                    e.preventDefault();
                    highlightOverlayButton('back');
                }
                if (isDown) {
                    e.preventDefault();
                    highlightOverlayButton('start');
                }
                if (isEnter) {
                    e.preventDefault();
                    if (activeOverlayBtn === 'back') {
                        goBackToMenu();
                    } else {
                        startFullscreen();
                    }
                }
            }
        });

        document.body.addEventListener('click', function() {
            if (!document.fullscreenElement && appEl.style.display !== 'none') {
                document.documentElement.requestFullscreen().catch(function(e) {});
            }
        });
    </script>
</body>

</html>