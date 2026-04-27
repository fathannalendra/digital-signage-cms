<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Preview Playlist</title>
    <style>
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; background: #000; overflow: hidden; font-family: sans-serif; }
        #media-area { width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; position: relative; }
        img, video { width: 100%; height: 100%; object-fit: contain; }
        .info-badge {
            position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.6); color: #fff;
            padding: 5px 10px; border-radius: 4px; font-size: 14px; z-index: 10;
        }
        #empty-msg { color: #fff; text-align: center; }
    </style>
</head>
<body>

    <div id="media-area">
        <div class="info-badge" id="info-badge">Loading...</div>
        <video id="vid" style="display:none;" muted autoplay></video>
        <img id="img" style="display:none;">
        <div id="empty-msg" style="display:none;">Playlist ini masih kosong.<br>Silakan isi media terlebih dahulu.</div>
    </div>

    <script>
        // Data Playlist dikirim dari Controller
        var playlist = <?= $items_json ?>;
        var currentIndex = 0;
        var timeout;

        var vid = document.getElementById('vid');
        var img = document.getElementById('img');
        var badge = document.getElementById('info-badge');
        var emptyMsg = document.getElementById('empty-msg');

        function play() {
            if(playlist.length === 0) {
                badge.style.display = 'none';
                emptyMsg.style.display = 'block';
                return;
            }

            var item = playlist[currentIndex];
            badge.innerText = (currentIndex + 1) + "/" + playlist.length + ": " + item.title;

            if(item.type === 'video') {
                img.style.display = 'none';
                vid.style.display = 'block';
                vid.src = item.src;
                vid.load();
                vid.play().catch(e => console.log(e)); // Auto play

                // Lanjut setelah video selesai
                vid.onended = function() {
                    next();
                };
            } else {
                vid.style.display = 'none';
                vid.pause();
                img.style.display = 'block';
                img.src = item.src;

                // Lanjut setelah durasi gambar habis
                var duration = (item.duration || 10) * 1000;
                clearTimeout(timeout);
                timeout = setTimeout(next, duration);
            }
        }

        function next() {
            currentIndex++;
            if(currentIndex >= playlist.length) currentIndex = 0; // Loop balik ke awal
            play();
        }

        // Mulai
        window.onload = play;
    </script>
</body>
</html>