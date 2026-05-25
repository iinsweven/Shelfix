<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location:index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM playlists WHERE user_id='$user_id' AND media_title != '__empty__' ORDER BY playlist_name";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>

<head>
    <title>My Playlists — Shelfix</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg: #0a0a0a;
            --surface: #111;
            --surface2: #1a1a1a;
            --surface3: #222;
            --border: #2a2a2a;
            --gold: #f5a623;
            --gold-hover: #ffbe45;
            --text: #fff;
            --muted: #888;
            --success: #2ecc71;
            --danger: #e74c3c;
        }

        body {
            margin: 0;
            padding: 0;
            background: #0a0a0a;
            background-image:
                radial-gradient(ellipse at top left, rgba(245, 166, 35, 0.12) 0%, transparent 50%),
                radial-gradient(ellipse at bottom right, rgba(245, 166, 35, 0.08) 0%, transparent 50%);
            background-attachment: fixed;
            font-family: 'Outfit', Arial, sans-serif;
            overflow-x: hidden;
        }

        /* ── HEADER ── */
        .header {
            width: 100%;
            height: 70px;
            background: var(--surface);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            box-sizing: border-box;
            border-bottom: 1px solid var(--border);
            position: fixed;
            top: 0;
            z-index: 999;
        }

        .left-section {
            display: flex;
            align-items: center;
            gap: 35px;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logoh {
            width: 40px;
            border-radius: 50%;
        }

        .logo-section h1 {
            color: white;
            font-size: 26px;
            font-family: 'Bebas Neue', sans-serif;
            letter-spacing: 2px;
        }

        .nav-links {
            display: flex;
            gap: 22px;
        }

        .nav-links a {
            text-decoration: none;
            color: #999;
            font-size: 15px;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: white;
        }

        .right-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .menu-container {
            position: relative;
        }

        .menu-btn {
            font-size: 28px;
            color: white;
            cursor: pointer;
            margin-right: 10px;
        }

        .dropdown {
            position: absolute;
            right: 0;
            top: 42px;
            background: #1a1a1a;
            width: 160px;
            border-radius: 10px;
            display: none;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5);
            border: 1px solid #2a2a2a;
            z-index: 1000;
        }

        .dropdown a {
            display: block;
            padding: 14px 16px;
            color: white;
            text-decoration: none;
            border-bottom: 1px solid #2a2a2a;
            font-size: 14px;
            transition: background 0.15s;
        }

        .dropdown a:hover {
            background: #2a2a2a;
        }

        .dropdown a:last-child {
            border-bottom: none;
        }


        /* ── PAGE ── */
        .page-body {
            padding: 90px 40px 60px;
            max-width: 900px;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
        }

        .page-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 42px;
            letter-spacing: 3px;
            color: var(--gold);
        }

        .create-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--gold);
            color: #000;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            font-size: 15px;
            font-family: 'Outfit', sans-serif;
            transition: background 0.2s, transform 0.15s;
        }

        .create-btn:hover {
            background: var(--gold-hover);
            transform: translateY(-2px);
        }

        /* ── PLAYLIST CARDS ── */
        .playlist-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            margin-bottom: 20px;
            overflow: hidden;
            transition: border-color 0.2s;
        }

        .playlist-card:hover {
            border-color: #333;
        }

        .playlist-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 22px;
            cursor: pointer;
            user-select: none;
            border-bottom: 1px solid transparent;
            transition: background 0.15s, border-color 0.2s;
        }

        .playlist-card-header:hover {
            background: var(--surface2);
        }

        .playlist-card-header.open {
            border-bottom-color: var(--border);
        }

        .playlist-name-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .playlist-icon {
            font-size: 22px;
        }

        .playlist-name {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 22px;
            letter-spacing: 1.5px;
            color: var(--gold);
        }

        .playlist-count {
            font-size: 12px;
            color: var(--muted);
            background: var(--surface2);
            border: 1px solid var(--border);
            padding: 2px 10px;
            border-radius: 20px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .add-item-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(245, 166, 35, 0.1);
            color: var(--gold);
            border: 1px solid rgba(245, 166, 35, 0.3);
            padding: 7px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Outfit', sans-serif;
            transition: all 0.2s;
        }

        .add-item-btn:hover {
            background: rgba(245, 166, 35, 0.2);
        }

        .chevron {
            font-size: 18px;
            color: var(--muted);
            transition: transform 0.25s;
        }

        .chevron.rotated {
            transform: rotate(180deg);
        }

        .playlist-items-list {
            padding: 0 22px 16px;
            display: none;
        }

        .playlist-items-list.open {
            display: block;
        }

        .media-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--surface3);
            transition: background 0.1s;
        }

        .media-item:last-child {
            border-bottom: none;
        }

        .media-item-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .media-item-icon {
            font-size: 16px;
        }

        .media-item a {
            color: white;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            transition: color 0.15s;
        }

        .media-item a:hover {
            color: var(--gold);
        }

        .empty-playlist {
            padding: 20px 0 8px;
            color: var(--muted);
            font-size: 14px;
            text-align: center;
        }

        .no-playlists {
            text-align: center;
            padding: 60px 20px;
            color: var(--muted);
            font-size: 16px;
        }

        .no-playlists span {
            font-size: 48px;
            display: block;
            margin-bottom: 16px;
        }

        /* ── MODAL ── */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(6px);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.open {
            display: flex;
        }

        .modal {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 36px 32px;
            width: 460px;
            max-width: 92vw;
            box-shadow: 0 40px 80px rgba(0, 0, 0, 0.7);
            position: relative;
            animation: modalIn 0.25s ease;
        }

        @keyframes modalIn {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.97);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .modal h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 28px;
            letter-spacing: 2px;
            margin-bottom: 22px;
            color: var(--gold);
        }

        .modal-close {
            position: absolute;
            top: 18px;
            right: 20px;
            font-size: 22px;
            cursor: pointer;
            color: var(--muted);
            background: none;
            border: none;
            line-height: 1;
        }

        .modal-close:hover {
            color: var(--text);
        }

        .modal label {
            display: block;
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 6px;
            font-weight: 600;
            margin-top: 16px;
        }

        .modal label:first-of-type {
            margin-top: 0;
        }

        .modal input[type="text"],
        .modal select {
            width: 100%;
            padding: 12px 16px;
            background: var(--surface3);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }

        .modal input[type="text"]:focus,
        .modal select:focus {
            border-color: var(--gold);
        }

        .modal-footer {
            display: flex;
            gap: 10px;
            margin-top: 24px;
            justify-content: flex-end;
        }

        .btn-cancel {
            padding: 11px 22px;
            border-radius: 10px;
            background: transparent;
            color: var(--muted);
            border: 1px solid var(--border);
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            color: var(--text);
            border-color: #444;
        }

        .btn-save {
            padding: 11px 26px;
            border-radius: 10px;
            background: var(--gold);
            color: #000;
            border: none;
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 700;
            transition: background 0.2s, transform 0.15s;
        }

        .btn-save:hover {
            background: var(--gold-hover);
            transform: translateY(-1px);
        }

        /* ── TOAST ── */
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px 22px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            z-index: 9999;
            transform: translateY(80px);
            opacity: 0;
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        .toast.toast-success {
            border-color: rgba(46, 204, 113, 0.4);
        }

        .toast.toast-warn {
            border-color: rgba(245, 166, 35, 0.4);
        }

        .toast.toast-error {
            border-color: rgba(231, 76, 60, 0.4);
        }

        /* msg box inside modal */
        .modal-msg {
            margin-top: 12px;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            display: none;
        }

        .modal-msg.warn {
            background: rgba(245, 166, 35, 0.12);
            color: #f5a623;
            border: 1px solid rgba(245, 166, 35, 0.3);
            display: block;
        }

        .modal-msg.error {
            background: rgba(231, 76, 60, 0.12);
            color: #e74c3c;
            border: 1px solid rgba(231, 76, 60, 0.3);
            display: block;
        }

        .modal-msg.ok {
            background: rgba(46, 204, 113, 0.12);
            color: #2ecc71;
            border: 1px solid rgba(46, 204, 113, 0.3);
            display: block;
        }

        /* Media picker rows */
        .pick-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            background: var(--surface3);
            border: 1px solid var(--border);
            border-radius: 10px;
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
        }

        .pick-row:hover {
            border-color: var(--gold);
            background: rgba(245, 166, 35, 0.07);
        }

        .pick-row.in-playlist {
            border-color: rgba(46, 204, 113, 0.4);
            background: rgba(46, 204, 113, 0.05);
            cursor: default;
        }

        .pick-row img {
            width: 38px;
            height: 54px;
            object-fit: cover;
            border-radius: 5px;
            flex-shrink: 0;
        }

        .pick-row-info {
            flex: 1;
            min-width: 0;
        }

        .pick-row-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pick-row-sub {
            font-size: 11px;
            color: var(--muted);
            margin-top: 2px;
        }

        .pick-row-badge {
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 20px;
            background: var(--gold);
            color: #000;
            font-weight: 700;
            flex-shrink: 0;
        }

        .pick-row-badge.added {
            background: var(--success);
        }

        .pick-no-results {
            color: var(--muted);
            font-size: 13px;
            text-align: center;
            padding: 20px 0;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <div class="left-section">
            <div class="logo-section">
                <img src="logoshelfix.png" class="logoh">
                <h1>Shelfix</h1>
            </div>
            <div class="nav-links">
                <a href="home.php">Home</a>
                <a href="playlists.php" class="active">My Playlist</a>
            </div>
        </div>


        <div class="right-section">
            <div class="menu-container">
                <div class="menu-btn" onclick="toggleMenu()">☰</div>
                <div class="dropdown" id="dropdownMenu">
                    <a href="profile.php">Profile</a>
                    <a href="#">Settings</a>
                    <a href="index.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="page-header">
            <div class="page-title">📋 My Playlists</div>
            <button class="create-btn" onclick="openCreateModal()">+ Create Playlist</button>
        </div>

        <?php
        if (mysqli_num_rows($result) == 0) {
            echo "<div class='no-playlists'><span>🎬</span>No playlists yet. Create one above!</div>";
        } else {
            $playlists = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $pname = $row['playlist_name'];
                if (!isset($playlists[$pname])) $playlists[$pname] = [];
                $playlists[$pname][] = $row['media_title'];
            }

            foreach ($playlists as $pname => $titles) {
                $count = count($titles);
                $safeName = htmlspecialchars($pname);
                $slugId = 'pl_' . md5($pname);
                echo "
            <div class='playlist-card'>
               <div class='playlist-card-header' onclick=\"window.location.href='playlist_items.php?playlist=" . urlencode($pname) . "'\">
                    <div class='playlist-name-row'>
                        <span class='playlist-icon'>🎵</span>
                        <span class='playlist-name'>$safeName</span>
                        <span class='playlist-count'>$count item" . ($count !== 1 ? 's' : '') . "</span>
                    </div>
                    <div class='header-actions'>
                        <button class='add-item-btn' onclick=\"event.stopPropagation(); openAddItemModal('$safeName')\">
                            ＋ Add Item
                        </button>
                        <span class='chevron' id='chev_$slugId'>▼</span>
                    </div>
                </div>
                <div class='playlist-items-list' id='$slugId'>
            ";

                if (empty($titles)) {
                    echo "<div class='empty-playlist'>No items yet — add some!</div>";
                } else {
                    foreach ($titles as $title) {
                        $safeTitle = htmlspecialchars($title);
                        $encTitle  = urlencode($title);
                        echo "
                    <div class='media-item'>
                        <div class='media-item-left'>
                            <span class='media-item-icon'>🎬</span>
                            <a href='media_details.php?title=$encTitle'>$safeTitle</a>
                        </div>
                    </div>";
                    }
                }

                echo "</div></div>";
            }
        }
        ?>
    </div>

    <!-- CREATE PLAYLIST MODAL -->
    <div class="modal-overlay" id="createModal">
        <div class="modal">
            <button class="modal-close" onclick="closeModal('createModal')">✕</button>
            <h2>🎵 New Playlist</h2>
            <label>Playlist Name</label>
            <input type="text" id="newPlaylistName" placeholder="e.g. Weekend Watchlist" maxlength="80">
            <div class="modal-msg" id="createMsg"></div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeModal('createModal')">Cancel</button>
                <button class="btn-save" onclick="createPlaylist()">Create</button>
            </div>
        </div>
    </div>

    <!-- ADD ITEM MODAL -->
    <div class="modal-overlay" id="addItemModal">
        <div class="modal" style="width:520px">
            <button class="modal-close" onclick="closeModal('addItemModal')">✕</button>
            <h2>➕ Add Item</h2>
            <p style="color:var(--muted);font-size:13px;margin-bottom:14px">
                Adding to: <strong id="addItemPlaylistLabel" style="color:var(--gold)"></strong>
            </p>
            <input type="text" id="addItemSearch" placeholder="🔍 Search movies, books, songs..."
                oninput="filterMediaList(this.value)"
                style="margin-bottom:12px">
            <div id="addItemList" style="max-height:300px;overflow-y:auto;display:flex;flex-direction:column;gap:6px"></div>
            <div class="modal-msg" id="addItemMsg"></div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeModal('addItemModal')">Close</button>
            </div>
        </div>
    </div>

    <!-- TOAST -->
    <div class="toast" id="toast">
        <span id="toastIcon">✅</span>
        <span id="toastText"></span>
    </div>

    <script src="script.js"></script>
    <script>
        function toggleMenu() {
            const menu = document.getElementById('dropdownMenu');
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }
        document.addEventListener('click', e => {
            const mc = document.querySelector('.menu-container');
            if (mc && !mc.contains(e.target)) {
                document.getElementById('dropdownMenu').style.display = 'none';
            }
        });
        /* ── Toggle playlist expand ── */
        function togglePlaylist(id, header) {
            const list = document.getElementById(id);
            const chev = document.getElementById('chev_' + id);
            const isOpen = list.classList.contains('open');
            list.classList.toggle('open', !isOpen);
            header.classList.toggle('open', !isOpen);
            if (chev) chev.classList.toggle('rotated', !isOpen);
        }

        /* ── Modals ── */
        function openCreateModal() {
            document.getElementById('newPlaylistName').value = '';
            setMsg('createMsg', '', '');
            openModal('createModal');
            setTimeout(() => document.getElementById('newPlaylistName').focus(), 100);
        }
        let _currentAddPlaylist = '';

        function openAddItemModal(playlistName) {
            _currentAddPlaylist = playlistName;
            document.getElementById('addItemPlaylistLabel').textContent = playlistName;
            document.getElementById('addItemSearch').value = '';
            setMsg('addItemMsg', '', '');
            openModal('addItemModal');
            renderMediaList(items);
            setTimeout(() => document.getElementById('addItemSearch').focus(), 100);
        }

        function filterMediaList(query) {
            const q = query.toLowerCase().trim();
            const filtered = q ? items.filter(i => i.title.toLowerCase().includes(q) || (i.type || '').toLowerCase().includes(q)) : items;
            renderMediaList(filtered);
        }

        // Track which titles are already in the current playlist (from DOM)
        function getExistingTitles(playlistName) {
            const existing = new Set();
            document.querySelectorAll('.playlist-name').forEach(el => {
                if (el.textContent.trim() === playlistName) {
                    const card = el.closest('.playlist-card');
                    card.querySelectorAll('.media-item a').forEach(a => existing.add(a.textContent.trim()));
                }
            });
            return existing;
        }

        function renderMediaList(list) {
            const container = document.getElementById('addItemList');
            const existing = getExistingTitles(_currentAddPlaylist);

            if (!list.length) {
                container.innerHTML = '<div class="pick-no-results">No results found.</div>';
                return;
            }

            container.innerHTML = list.map(item => {
                const inPl = existing.has(item.title);
                const safeTitle = item.title.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                return `
            <div class="pick-row ${inPl ? 'in-playlist' : ''}"
                 ${inPl ? '' : `onclick="pickItem('${safeTitle}', this)"`}>
                <img src="${item.image}" onerror="this.src='logoshelfix.png'" alt="">
                <div class="pick-row-info">
                    <div class="pick-row-title">${item.title}</div>
                    <div class="pick-row-sub">${item.type || ''} ${item.year ? '• ' + item.year : ''}</div>
                </div>
                <span class="pick-row-badge ${inPl ? 'added' : ''}">${inPl ? '✅ Added' : '+ Add'}</span>
            </div>`;
            }).join('');
        }

        function pickItem(title, rowEl) {
            if (rowEl.classList.contains('in-playlist')) return;

            // Optimistically mark as added
            rowEl.classList.add('in-playlist');
            rowEl.onclick = null;
            const badge = rowEl.querySelector('.pick-row-badge');
            badge.textContent = '⏳';

            fetch('save_playlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'title=' + encodeURIComponent(title) + '&playlist=' + encodeURIComponent(_currentAddPlaylist)
                })
                .then(r => r.text())
                .then(data => {
                    const d = data.trim();
                    if (d === 'saved') {
                        badge.textContent = '✅ Added';
                        badge.classList.add('added');
                        setMsg('addItemMsg', '✅ "' + title + '" added!', 'ok');
                        showToast('"' + title + '" added to ' + _currentAddPlaylist, '📋');
                        injectItemIntoPlaylist(_currentAddPlaylist, title);
                    } else if (d === 'already exists') {
                        badge.textContent = '✅ Added';
                        badge.classList.add('added');
                        setMsg('addItemMsg', '⚠️ "' + title + '" is already in this playlist.', 'warn');
                    } else {
                        rowEl.classList.remove('in-playlist');
                        rowEl.onclick = () => pickItem(title, rowEl);
                        badge.textContent = '+ Add';
                        badge.classList.remove('added');
                        setMsg('addItemMsg', '❌ Error: ' + d, 'error');
                    }
                })
                .catch(() => {
                    rowEl.classList.remove('in-playlist');
                    rowEl.onclick = () => pickItem(title, rowEl);
                    badge.textContent = '+ Add';
                    setMsg('addItemMsg', '❌ Network error.', 'error');
                });
        }

        function openModal(id) {
            document.getElementById(id).classList.add('open');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('open');
        }

        document.querySelectorAll('.modal-overlay').forEach(el => {
            el.addEventListener('click', e => {
                if (e.target === el) el.classList.remove('open');
            });
        });

        /* ── Msg helper ── */
        function setMsg(id, text, type) {
            const el = document.getElementById(id);
            el.textContent = text;
            el.className = 'modal-msg' + (type ? ' ' + type : '');
        }

        /* ── Toast ── */
        let toastTimer;

        function showToast(msg, icon = '✅', type = 'success') {
            const t = document.getElementById('toast');
            document.getElementById('toastText').textContent = msg;
            document.getElementById('toastIcon').textContent = icon;
            t.className = 'toast toast-' + type + ' show';
            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => t.classList.remove('show'), 3000);
        }

        function createPlaylist() {
            const name = document.getElementById('newPlaylistName').value.trim();
            if (!name) {
                setMsg('createMsg', '⚠️ Please enter a playlist name.', 'warn');
                return;
            }

            const existing = document.querySelectorAll('.playlist-name');
            for (let el of existing) {
                if (el.textContent.trim().toLowerCase() === name.toLowerCase()) {
                    setMsg('createMsg', '⚠️ A playlist with this name already exists.', 'warn');
                    return;
                }
            }

            // ✅ Actually save to DB now
            fetch('create_playlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'playlist=' + encodeURIComponent(name)
                })
                .then(r => r.text())
                .then(data => {
                    const d = data.trim();
                    if (d === 'created' || d === 'already exists') {
                        setMsg('createMsg', '✅ Playlist ready! Now use "Add Item" to add media to it.', 'ok');
                        showToast('Playlist "' + name + '" created!', '🎵');
                        injectPlaylistCard(name);
                        setTimeout(() => closeModal('createModal'), 1200);
                    } else {
                        setMsg('createMsg', '❌ Error: ' + d, 'error');
                    }
                });
        }

        function injectPlaylistCard(name) {
            const safeName = name.replace(/"/g, '&quot;').replace(/'/g, "\\'");
            const slugId = 'pl_dyn_' + Date.now();
            const html = `
        <div class='playlist-card' id='card_${slugId}'>
            <div class='playlist-card-header' onclick="togglePlaylist('${slugId}', this)">
                <div class='playlist-name-row'>
                    <span class='playlist-icon'>🎵</span>
                    <span class='playlist-name'>${name}</span>
                    <span class='playlist-count' id='cnt_${slugId}'>0 items</span>
                </div>
                <div class='header-actions'>
                    <button class='add-item-btn' onclick="event.stopPropagation(); openAddItemModal('${safeName}')">
                        ＋ Add Item
                    </button>
                    <span class='chevron' id='chev_${slugId}'>▼</span>
                </div>
            </div>
            <div class='playlist-items-list' id='${slugId}'>
                <div class='empty-playlist' id='empty_${slugId}'>No items yet — add some!</div>
            </div>
        </div>`;

            // Remove "no playlists" message if present
            const noPl = document.querySelector('.no-playlists');
            if (noPl) noPl.remove();

            const pageBody = document.querySelector('.page-body');
            pageBody.insertAdjacentHTML('beforeend', html);
        }

        function injectItemIntoPlaylist(playlistName, title) {
            // Find the playlist card by name
            const allNames = document.querySelectorAll('.playlist-name');
            let targetList = null,
                countEl = null,
                emptyEl = null;

            allNames.forEach(el => {
                if (el.textContent.trim() === playlistName) {
                    const card = el.closest('.playlist-card');
                    targetList = card.querySelector('.playlist-items-list');
                    countEl = card.querySelector('.playlist-count');
                    emptyEl = card.querySelector('.empty-playlist');
                }
            });

            if (!targetList) return;

            // Remove empty message
            if (emptyEl) emptyEl.remove();

            const encTitle = encodeURIComponent(title);
            const safeTitle = title.replace(/</g, '&lt;').replace(/>/g, '&gt;');

            targetList.insertAdjacentHTML('beforeend', `
            <div class='media-item'>
                <div class='media-item-left'>
                    <span class='media-item-icon'>🎬</span>
                    <a href='media_details.php?title=${encTitle}'>${safeTitle}</a>
                </div>
            </div>
        `);

            // Update count
            if (countEl) {
                const current = parseInt(countEl.textContent) || 0;
                const next = current + 1;
                countEl.textContent = next + ' item' + (next !== 1 ? 's' : '');
            }

            // Open the list if closed
            targetList.classList.add('open');
        }

        /* Enter key support */
        document.getElementById('newPlaylistName').addEventListener('keydown', e => {
            if (e.key === 'Enter') createPlaylist();
        });
    </script>
</body>

</html>