<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']) ? 'true' : 'false';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shelfix — Media Details</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #0a0a0a;
            --surface: #111111;
            --surface2: #1a1a1a;
            --surface3: #222222;
            --border: #2a2a2a;
            --gold: #f5a623;
            --gold-hover: #ffbe45;
            --text: #ffffff;
            --muted: #888888;
            --dim: #555555;
            --danger: #e74c3c;
            --success: #2ecc71;
            --blue: #3498db;
            --purple: #9b59b6;
        }

        body {
            background: var(--bg);
            font-family: 'Outfit', sans-serif;
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ─── HEADER ─── */
        .header {
            position: fixed; top: 0; left: 0; right: 0;
            height: 68px;
            background: rgba(10,10,10,0.96);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            z-index: 1000;
        }
        .header-left { display: flex; align-items: center; gap: 30px; }
        .logo-wrap {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none;
        }
        .logoh { width: 38px; border-radius: 50%; }
        .logo-wrap h1 {
            color: var(--text);
            font-family: 'Bebas Neue', sans-serif;
            font-size: 26px; letter-spacing: 2px;
        }
        .back-btn {
            display: flex; align-items: center; gap: 6px;
            color: var(--muted); text-decoration: none;
            font-size: 14px; font-weight: 500;
            padding: 7px 14px; border-radius: 20px;
            border: 1px solid var(--border);
            background: var(--surface2);
            transition: all 0.2s; cursor: pointer;
        }
        .back-btn:hover { color: var(--text); border-color: var(--gold); }

        .header-right { display: flex; align-items: center; gap: 12px; }
        .search-wrap { position: relative; display: flex; align-items: center; gap: 8px; }
        .search-input {
            width: 220px; padding: 8px 16px;
            background: var(--surface2); border: 1px solid var(--border);
            border-radius: 25px; color: var(--text);
            font-size: 13px; font-family: 'Outfit', sans-serif;
            outline: none; transition: border-color 0.2s;
        }
        .search-input:focus { border-color: var(--gold); }
        .search-btn {
            padding: 8px 16px; background: var(--gold);
            color: #000; border: none; border-radius: 25px;
            font-size: 13px; font-weight: 700; cursor: pointer;
            font-family: 'Outfit', sans-serif; transition: background 0.2s;
        }
        .search-btn:hover { background: var(--gold-hover); }
        #search-results {
            position: absolute; top: calc(100% + 8px);
            left: 0; right: 0;
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 12px; overflow-y: auto; max-height: 340px;
            z-index: 999; box-shadow: 0 20px 50px rgba(0,0,0,0.7);
        }
        .result-card {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 14px; border-bottom: 1px solid var(--surface3);
            cursor: pointer; transition: background 0.15s;
        }
        .result-card:last-child { border-bottom: none; }
        .result-card:hover { background: var(--surface2); }
        .result-image { width: 40px; height: 56px; object-fit: cover; border-radius: 5px; }
        .result-info h3 { font-size: 13px; color: var(--text); font-weight: 600; margin-bottom: 2px; }
        .result-info p { font-size: 11px; color: var(--muted); }

        .menu-container { position: relative; }
        .menu-btn {
            font-size: 26px; color: var(--text);
            cursor: pointer; padding: 4px 8px;
            border-radius: 8px; transition: background 0.15s;
        }
        .menu-btn:hover { background: var(--surface2); }
        .dropdown {
            position: absolute; right: 0; top: 46px;
            background: var(--surface2); min-width: 160px;
            border-radius: 12px; display: none;
            overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.6);
            border: 1px solid var(--border); z-index: 1001;
        }
        .dropdown a {
            display: block; padding: 13px 16px;
            color: var(--text); text-decoration: none;
            border-bottom: 1px solid var(--border);
            font-size: 14px; transition: background 0.15s;
        }
        .dropdown a:hover { background: var(--surface3); }
        .dropdown a:last-child { border-bottom: none; }

        /* ─── HERO / BACKDROP ─── */
        .page-wrap { padding-top: 68px; }

        .hero-backdrop {
            position: relative;
            min-height: 480px;
            display: flex; align-items: flex-end;
            overflow: hidden;
        }
        .backdrop-blur {
            position: absolute; inset: 0;
            background-size: cover; background-position: center top;
            filter: blur(18px) brightness(0.35) saturate(1.2);
            transform: scale(1.05);
        }
        .backdrop-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to bottom,
                rgba(10,10,10,0.3) 0%,
                rgba(10,10,10,0.5) 50%,
                rgba(10,10,10,1) 100%);
        }
        .hero-content {
            position: relative;
            display: flex; gap: 40px;
            padding: 40px 50px 50px;
            align-items: flex-end;
            width: 100%;
        }

        /* Poster */
        .poster-wrap {
            flex-shrink: 0;
            width: 200px;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 30px 60px rgba(0,0,0,0.8);
            border: 2px solid rgba(245,166,35,0.3);
        }
        .poster-wrap img {
            width: 100%; display: block;
            aspect-ratio: 2/3; object-fit: cover;
        }

        /* Meta block */
        .hero-meta { flex: 1; }
        .type-badge {
            display: inline-block;
            padding: 4px 12px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
            letter-spacing: 1px; text-transform: uppercase;
            margin-bottom: 14px;
            background: var(--gold); color: #000;
        }
        .hero-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 58px; letter-spacing: 3px;
            line-height: 1; margin-bottom: 12px;
            text-shadow: 0 4px 20px rgba(0,0,0,0.8);
        }
        .hero-subtitle {
            font-size: 15px; color: var(--muted);
            margin-bottom: 14px;
        }
        .hero-tags { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px; }
        .tag {
            padding: 5px 14px; border-radius: 20px;
            border: 1px solid var(--border);
            font-size: 12px; color: var(--muted);
            background: rgba(255,255,255,0.04);
        }
        .rating-display {
            display: flex; align-items: center; gap: 16px;
            margin-bottom: 24px;
        }
        .rating-big {
            display: flex; align-items: center; gap: 8px;
        }
        .star-icon { font-size: 28px; }
        .rating-num {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 40px; color: var(--gold); letter-spacing: 1px;
        }
        .rating-label { font-size: 12px; color: var(--muted); margin-top: 2px; }
        .rating-divider { width: 1px; height: 45px; background: var(--border); }
        .user-rating-wrap { text-align: center; }
        .user-rating-num { font-family: 'Bebas Neue', sans-serif; font-size: 32px; color: var(--text); }
        .user-rating-label { font-size: 11px; color: var(--muted); }

        /* Action buttons */
        .action-buttons { display: flex; flex-wrap: wrap; gap: 10px; }
        .btn {
            display: flex; align-items: center; gap: 8px;
            padding: 11px 22px; border-radius: 10px;
            font-family: 'Outfit', sans-serif; font-size: 14px;
            font-weight: 600; cursor: pointer; border: none;
            transition: all 0.2s; text-decoration: none;
        }
        .btn-primary { background: var(--gold); color: #000; }
        .btn-primary:hover { background: var(--gold-hover); transform: translateY(-2px); }
        .btn-outline {
            background: transparent; color: var(--text);
            border: 1px solid var(--border);
        }
        .btn-outline:hover { border-color: var(--gold); color: var(--gold); transform: translateY(-2px); }
        .btn-danger { background: rgba(231,76,60,0.15); color: #e74c3c; border: 1px solid rgba(231,76,60,0.3); }
        .btn-danger:hover { background: rgba(231,76,60,0.25); transform: translateY(-2px); }
        .btn-success { background: rgba(46,204,113,0.15); color: #2ecc71; border: 1px solid rgba(46,204,113,0.3); }
        .btn-success:hover { background: rgba(46,204,113,0.25); transform: translateY(-2px); }
        .btn-purple { background: rgba(155,89,182,0.15); color: #9b59b6; border: 1px solid rgba(155,89,182,0.3); }
        .btn-purple:hover { background: rgba(155,89,182,0.25); transform: translateY(-2px); }

        /* ─── MAIN CONTENT ─── */
        .main-content {
            max-width: 1200px; margin: 0 auto;
            padding: 40px 50px 80px;
        }

        .content-grid { display: grid; grid-template-columns: 1fr 320px; gap: 40px; }

        /* Trailer / video */
        .video-section { margin-bottom: 40px; }
        .section-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 26px; letter-spacing: 2px;
            color: var(--text); margin-bottom: 18px;
            display: flex; align-items: center; gap: 10px;
        }
        .section-title::after {
            content: ''; flex: 1; height: 1px; background: var(--border);
        }
        .video-embed {
            width: 100%; aspect-ratio: 16/9;
            border-radius: 14px; overflow: hidden;
            border: 1px solid var(--border);
            background: #000;
        }
        .video-embed iframe { width: 100%; height: 100%; border: none; }

        /* Description */
        .desc-text {
            font-size: 16px; line-height: 1.8;
            color: #ccc; margin-bottom: 30px;
        }

        /* Details table */
        .details-table { margin-bottom: 40px; }
        .detail-row {
            display: flex; gap: 16px;
            padding: 14px 0; border-bottom: 1px solid var(--surface3);
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label {
            font-size: 13px; font-weight: 700;
            color: var(--muted); min-width: 100px;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .detail-value { font-size: 14px; color: var(--text); }

        /* Reviews */
        .reviews-section { margin-bottom: 40px; }
        .review-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px; padding: 18px 20px;
            margin-bottom: 12px;
        }
        .review-header {
            display: flex; justify-content: space-between;
            align-items: center; margin-bottom: 10px;
        }
        .reviewer-name { font-weight: 600; font-size: 14px; color: var(--gold); }
        .review-stars { font-size: 13px; color: var(--muted); }
        .review-text { font-size: 14px; color: #bbb; line-height: 1.6; }
        .user-review-card {
            background: linear-gradient(135deg, rgba(245,166,35,0.06), rgba(245,166,35,0.02));
            border: 1px solid rgba(245,166,35,0.2);
        }

        /* SIDEBAR */
        .sidebar {}
        .sidebar-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px; padding: 22px;
            margin-bottom: 20px;
        }
        .sidebar-card h3 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 20px; letter-spacing: 1px;
            margin-bottom: 16px; color: var(--gold);
        }
        .stat-row {
            display: flex; justify-content: space-between;
            align-items: center; padding: 10px 0;
            border-bottom: 1px solid var(--surface3);
        }
        .stat-row:last-child { border-bottom: none; }
        .stat-label { font-size: 13px; color: var(--muted); }
        .stat-value { font-size: 14px; font-weight: 600; }

        /* Star rating widget */
        .star-picker { display: flex; gap: 6px; margin: 12px 0; }
        .star-pick {
            font-size: 26px; cursor: pointer;
            transition: transform 0.1s;
            filter: grayscale(1) brightness(0.5);
        }
        .star-pick:hover, .star-pick.active { filter: none; transform: scale(1.2); }

        /* MODALS */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.85);
            backdrop-filter: blur(6px);
            z-index: 2000;
            display: none; align-items: center; justify-content: center;
        }
        .modal-overlay.open { display: flex; }
        .modal {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 36px 32px;
            width: 480px; max-width: 92vw;
            box-shadow: 0 40px 80px rgba(0,0,0,0.7);
            position: relative;
            animation: modalIn 0.25s ease;
        }
        @keyframes modalIn {
            from { opacity: 0; transform: translateY(20px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .modal h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 28px; letter-spacing: 2px;
            margin-bottom: 22px; color: var(--gold);
        }
        .modal-close {
            position: absolute; top: 18px; right: 20px;
            font-size: 22px; cursor: pointer; color: var(--muted);
            background: none; border: none; line-height: 1;
        }
        .modal-close:hover { color: var(--text); }
        .modal label { display: block; font-size: 13px; color: var(--muted); margin-bottom: 6px; font-weight: 600; }
        .modal textarea, .modal input[type="text"], .modal select {
            width: 100%; padding: 12px 16px;
            background: var(--surface3); border: 1px solid var(--border);
            border-radius: 10px; color: var(--text);
            font-family: 'Outfit', sans-serif; font-size: 14px;
            outline: none; resize: vertical;
            transition: border-color 0.2s;
        }
        .modal textarea:focus, .modal input[type="text"]:focus, .modal select:focus {
            border-color: var(--gold);
        }
        .modal-footer { display: flex; gap: 10px; margin-top: 22px; justify-content: flex-end; }

        /* Toast */
        .toast {
            position: fixed; bottom: 30px; right: 30px;
            background: var(--surface2); border: 1px solid var(--border);
            border-radius: 12px; padding: 14px 22px;
            font-size: 14px; color: var(--text);
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            z-index: 3000; opacity: 0;
            transform: translateY(16px);
            transition: all 0.3s;
            display: flex; align-items: center; gap: 10px;
        }
        .toast.show { opacity: 1; transform: translateY(0); }
        .toast-icon { font-size: 20px; }

        /* Saved states */
        .btn.saved { border-color: var(--gold); color: var(--gold); }

        /* Playlist picker */
        .pl-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 16px;
            background: var(--surface3);
            border: 1px solid var(--border);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.15s;
        }
        .pl-item:hover { border-color: var(--gold); background: rgba(245,166,35,0.07); }
        .pl-item.already-in { border-color: rgba(46,204,113,0.4); background: rgba(46,204,113,0.06); cursor: default; }
        .pl-item-name { font-size: 14px; font-weight: 600; color: var(--text); }
        .pl-item-badge {
            font-size: 11px; padding: 3px 10px; border-radius: 20px;
            background: var(--gold); color: #000; font-weight: 700;
        }
        .pl-item-badge.added { background: var(--success); color: #000; }
        .pl-loading { color: var(--muted); font-size: 14px; text-align: center; padding: 14px; }
        .pl-empty { color: var(--muted); font-size: 13px; text-align: center; padding: 10px 0; }

        /* Responsive */
        @media (max-width: 900px) {
            .hero-content { flex-direction: column; align-items: flex-start; padding: 30px 20px 40px; }
            .poster-wrap { width: 160px; }
            .hero-title { font-size: 40px; }
            .content-grid { grid-template-columns: 1fr; }
            .main-content { padding: 30px 20px 60px; }
        }
    </style>
</head>
<body>

<!-- HEADER -->
<div class="header">
    <div class="header-left">
        <a href="<?php echo isset($_SESSION['user_id']) ? 'home.php' : 'index.php'; ?>" class="logo-wrap">
            <img src="logoshelfix.png" class="logoh" alt="Shelfix">
            <h1>Shelfix</h1>
        </a>
        <a href="<?php echo isset($_SESSION['user_id']) ? 'home.php' : 'index.php'; ?>" class="back-btn">← Back</a>
    </div>
    <div class="header-right">
        <div class="search-wrap">
            <input type="text" placeholder="Search..." class="search-input" id="searchInput">
            <button class="search-btn">Search</button>
            <div id="search-results"></div>
        </div>
        <div class="menu-container">
            <div class="menu-btn" onclick="toggleMenu()">☰</div>
            <div class="dropdown" id="dropdownMenu">
                <?php if(isset($_SESSION['user_id'])): ?>
                <a href="profile.php">Profile</a>
                <a href="#">Settings</a>
                <a href="index.php">Logout</a>
                <?php else: ?>
                <a href="index.php">Login</a>
                <a href="register.php">Create Account</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="page-wrap">
    <!-- HERO SECTION (filled by JS) -->
    <div class="hero-backdrop" id="heroBanner">
        <div class="backdrop-blur" id="backdropBlur"></div>
        <div class="backdrop-overlay"></div>
        <div class="hero-content" id="heroContent">
            <!-- JS fills this -->
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="content-grid">
            <div class="left-col">
                <!-- Trailer -->
                <div class="video-section" id="videoSection" style="display:none">
                    <div class="section-title">🎬 Official Trailer</div>
                    <div class="video-embed">
                        <iframe id="trailerFrame" src="" allowfullscreen
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope"></iframe>
                    </div>
                </div>

                <!-- Description -->
                <div id="descSection">
                    <div class="section-title">📖 About</div>
                    <p class="desc-text" id="descText"></p>
                </div>

                <!-- Details -->
                <div class="details-table" id="detailsTable">
                    <div class="section-title">📋 Details</div>
                    <div id="detailRows"></div>
                </div>

                <!-- Reviews -->
                <div class="reviews-section">
                    <div class="section-title">💬 Reviews</div>
                    <div id="reviewsList"></div>
                </div>
            </div>

            <!-- SIDEBAR -->
            <aside class="sidebar">
                <div class="sidebar-card">
                    <h3>Quick Info</h3>
                    <div id="quickStats"></div>
                </div>
                <div class="sidebar-card">
                    <h3>Your Activity</h3>
                    <div id="userActivity">
                        <div class="stat-row">
                            <span class="stat-label">Favourited</span>
                            <span class="stat-value" id="actFav">—</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">In Playlist</span>
                            <span class="stat-value" id="actPlay">—</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Your Rating</span>
                            <span class="stat-value" id="actRate">—</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Reviewed</span>
                            <span class="stat-value" id="actReview">—</span>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

<!-- MODAL: Add Review -->
<div class="modal-overlay" id="reviewModal">
    <div class="modal">
        <button class="modal-close" onclick="closeModal('reviewModal')">✕</button>
        <h2>Write a Review</h2>
        <label>Your Review</label>
        <textarea id="reviewText" rows="5" placeholder="What did you think?"></textarea>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('reviewModal')">Cancel</button>
            <button class="btn btn-primary" onclick="submitReview()">Post Review</button>
        </div>
    </div>
</div>

<!-- MODAL: Add Rating -->
<div class="modal-overlay" id="ratingModal">
    <div class="modal">
        <button class="modal-close" onclick="closeModal('ratingModal')">✕</button>
        <h2>Rate This</h2>
        <p style="color:var(--muted);font-size:14px;margin-bottom:16px;">Tap the stars to rate</p>
        <div class="star-picker" id="starPicker">
            <span class="star-pick" data-val="1">⭐</span>
            <span class="star-pick" data-val="2">⭐</span>
            <span class="star-pick" data-val="3">⭐</span>
            <span class="star-pick" data-val="4">⭐</span>
            <span class="star-pick" data-val="5">⭐</span>
        </div>
        <p style="color:var(--gold);font-size:24px;font-family:'Bebas Neue';letter-spacing:2px;margin:10px 0 20px" id="ratingDisplay">Select a rating</p>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('ratingModal')">Cancel</button>
            <button class="btn btn-primary" onclick="submitRating()">Save Rating</button>
        </div>
    </div>
</div>

<!-- MODAL: Add to Playlist -->
<div class="modal-overlay" id="playlistModal">
    <div class="modal" style="width:520px">
        <button class="modal-close" onclick="closeModal('playlistModal')">✕</button>
        <h2>📋 Add to Playlist</h2>

        <!-- Existing playlists -->
        <div id="plExistingSection">
            <p style="color:var(--muted);font-size:13px;margin-bottom:14px">Choose an existing playlist or create a new one below.</p>
            <div id="plList" style="max-height:220px;overflow-y:auto;display:flex;flex-direction:column;gap:8px;margin-bottom:16px">
                <!-- filled by JS -->
            </div>
        </div>

        <!-- Divider -->
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
            <div style="flex:1;height:1px;background:var(--border)"></div>
            <span style="font-size:12px;color:var(--muted);white-space:nowrap">OR CREATE NEW</span>
            <div style="flex:1;height:1px;background:var(--border)"></div>
        </div>

        <!-- Create new inline -->
        <div style="display:flex;gap:10px;align-items:center">
            <input type="text" id="playlistName" placeholder="New playlist name..." style="flex:1">
            <button class="btn btn-primary" onclick="submitPlaylist()" style="white-space:nowrap;padding:11px 18px">Create & Add</button>
        </div>

        <div id="plMsg" style="margin-top:12px;font-size:13px;display:none;padding:10px 14px;border-radius:8px"></div>

        <div class="modal-footer" style="margin-top:18px">
            <button class="btn btn-outline" onclick="closeModal('playlistModal')">Close</button>
        </div>
    </div>
</div>

<!-- LOGIN REQUIRED POPUP (shown to guests) -->
<div class="modal-overlay" id="loginRequiredModal">
    <div class="modal" style="text-align:center;max-width:380px">
        <h2>🔒 Login Required</h2>
        <p style="color:var(--muted);font-size:15px;margin-bottom:28px;line-height:1.6">
            You need to be logged in to do that.<br>Create a free account or sign in to rate, review, and save media.
        </p>
        <div style="display:flex;gap:12px;justify-content:center">
            <button class="btn btn-primary" onclick="window.location.href='index.php'" style="padding:12px 28px;font-size:15px">
                Login Now
            </button>
            <button class="btn btn-outline" onclick="document.getElementById('loginRequiredModal').classList.remove('open')" style="padding:12px 22px">
                Cancel
            </button>
        </div>
    </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast">
    <span class="toast-icon" id="toastIcon">✅</span>
    <span id="toastMsg">Done!</span>
</div>

<!-- Include the items data -->
<script src="script.js"></script>
<script>
    /* ── Login guard ── */
    const isLoggedIn = <?php echo $isLoggedIn; ?>;

    function requireLogin(action) {
        if (!isLoggedIn) {
            document.getElementById('loginRequiredModal').classList.add('open');
            return false;
        }
        if (typeof action === 'function') action();
        return true;
    }

    /* ── Helpers ── */
    const params = new URLSearchParams(location.search);
    const titleParam = decodeURIComponent(params.get('title') || '');
    const item = items.find(i => i.title === titleParam);

    const storageKey = t => 'shelfix_' + encodeURIComponent(t) + '_';

    function getStore(key) {
        try { return JSON.parse(localStorage.getItem(storageKey(titleParam) + key)) } catch { return null; }
    }
    function setStore(key, val) {
        localStorage.setItem(storageKey(titleParam) + key, JSON.stringify(val));
    }

    function showToast(msg, icon = '✅') {
        const t = document.getElementById('toast');
        document.getElementById('toastMsg').textContent = msg;
        document.getElementById('toastIcon').textContent = icon;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3000);
    }

    /* ── Menu ── */
    function toggleMenu() {
        const m = document.getElementById('dropdownMenu');
        m.style.display = m.style.display === 'block' ? 'none' : 'block';
    }
    document.addEventListener('click', e => {
        const mc = document.querySelector('.menu-container');
        if (mc && !mc.contains(e.target))
            document.getElementById('dropdownMenu').style.display = 'none';
    });

    /* ── Search ── */
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const val = this.value.toLowerCase().trim();
        const box = document.getElementById('search-results');
        box.innerHTML = '';
        if (!val) return;
        items.filter(i => i.title.toLowerCase().includes(val)).forEach(i => {
            box.innerHTML += `<div class="result-card" onclick="location.href='media_details.php?title='+encodeURIComponent('${i.title}')">
                <img src="${i.image}" class="result-image" onerror="this.src='logoshelfix.png'">
                <div class="result-info"><h3>${i.title}</h3><p>${i.type} • ${i.year}</p></div>
            </div>`;
        });
    });

    /* ── Render Page ── */
    function renderPage() {
        if (!item) {
            document.getElementById('heroContent').innerHTML = `<h2 style="color:var(--muted)">Item not found.</h2>`;
            return;
        }

        // Backdrop
        document.getElementById('backdropBlur').style.backgroundImage = `url('${item.image}')`;
        document.title = `${item.title} — Shelfix`;

        const isFav = !!getStore('fav');
        const isPlay = !!getStore('playlist');
        const myRating = getStore('rating');
        const myReview = getStore('review');

        // Hero
        document.getElementById('heroContent').innerHTML = `
            <div class="poster-wrap">
                <img src="${item.image}" alt="${item.title}" onerror="this.src='logoshelfix.png'">
            </div>
            <div class="hero-meta">
                <div class="type-badge">${item.type || 'Media'}</div>
                <h1 class="hero-title">${item.title}</h1>
                <p class="hero-subtitle">${item.year || ''} ${item.duration ? '• ' + item.duration : ''} ${item.genre ? '• ' + item.genre : ''}</p>
                <div class="hero-tags">
                    ${(item.genre || '').split(',').map(g => `<span class="tag">${g.trim()}</span>`).join('')}
                </div>
                <div class="rating-display">
                    <div class="rating-big">
                        <span class="star-icon">⭐</span>
                        <div>
                            <div class="rating-num">${item.rating ? item.rating.replace('/5','') : '—'}</div>
                            <div class="rating-label">/5 Rating</div>
                        </div>
                    </div>
                    ${myRating ? `
                    <div class="rating-divider"></div>
                    <div class="user-rating-wrap">
                        <div class="user-rating-num">${myRating}/5</div>
                        <div class="user-rating-label">Your Rating</div>
                    </div>` : ''}
                </div>
                <div class="action-buttons">
                    <button class="btn btn-danger ${isFav ? 'saved' : ''}" id="favBtn" onclick="requireLogin(toggleFav)">
                        ${isFav ? '❤️ Favourited' : '🤍 Add to Favourites'}
                    </button>
                    <button class="btn btn-success ${isPlay ? 'saved' : ''}" id="playBtn" onclick="requireLogin(openPlaylistModal)">
                        ${isPlay ? '📋 In Playlist' : '➕ Add to Playlist'}
                    </button>
                    <button class="btn btn-outline" onclick="requireLogin(() => openModal('reviewModal'))">💬 Write Review</button>
                    <button class="btn btn-purple" onclick="requireLogin(() => openModal('ratingModal'))">⭐ Rate This</button>
                </div>
            </div>
        `;

        // Video
        if (item.video) {
            document.getElementById('videoSection').style.display = 'block';
            document.getElementById('trailerFrame').src = item.video;
        }

        // Description / caption
        document.getElementById('descText').textContent = item.caption || '';

        // Details table
        const detailFields = [
            ['Type', item.type],
            ['Year', item.year],
            ['Duration', item.duration],
            ['Genre', item.genre],
            ['Director', item.director],
            ['Cast', item.cast],
            ['Singer', item.singer],
            ['Album', item.album],
            ['Producer', item.producer],
            ['Author', item.author],
            ['Publisher', item.publisher],
            ['Pages', item.pages],
        ].filter(([, v]) => v);

        document.getElementById('detailRows').innerHTML = detailFields.map(([l, v]) => `
            <div class="detail-row">
                <span class="detail-label">${l}</span>
                <span class="detail-value">${v}</span>
            </div>
        `).join('');

        // Sidebar quick stats
        document.getElementById('quickStats').innerHTML = `
            <div class="stat-row">
                <span class="stat-label">Rating</span>
                <span class="stat-value" style="color:var(--gold)">⭐ ${item.rating || '—'}</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">Year</span>
                <span class="stat-value">${item.year || '—'}</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">Type</span>
                <span class="stat-value">${item.type || '—'}</span>
            </div>
            ${item.duration ? `<div class="stat-row">
                <span class="stat-label">Duration</span>
                <span class="stat-value">${item.duration}</span>
            </div>` : ''}
        `;

        // User activity sidebar
        updateActivitySidebar();

        // Reviews
        renderReviews();
    }

    function updateActivitySidebar() {
        const isFav = !!getStore('fav');
        const isPlay = !!getStore('playlist');
        const myRating = getStore('rating');
        const myReview = getStore('review');

        document.getElementById('actFav').innerHTML = isFav
            ? `<span style="color:var(--danger)">❤️ Yes</span>`
            : `<span style="color:var(--muted)">No</span>`;
        document.getElementById('actPlay').innerHTML = isPlay
            ? `<span style="color:var(--success)">✅ ${getStore('playlistName') || 'Yes'}</span>`
            : `<span style="color:var(--muted)">No</span>`;
        document.getElementById('actRate').innerHTML = myRating
            ? `<span style="color:var(--gold)">⭐ ${myRating}/5</span>`
            : `<span style="color:var(--muted)">Not rated</span>`;
        document.getElementById('actReview').innerHTML = myReview
            ? `<span style="color:var(--purple)">✍️ Yes</span>`
            : `<span style="color:var(--muted)">No</span>`;
    }

    function renderReviews() {
        const myReview = getStore('review');
        const builtIn = (item && item.reviews) || [];
        let html = '';

        if (myReview) {
            html += `<div class="review-card user-review-card">
                <div class="review-header">
                    <span class="reviewer-name">You</span>
                    <span class="review-stars" style="color:var(--gold)">⭐ ${getStore('rating') || '?'}/5</span>
                </div>
                <p class="review-text">${myReview}</p>
            </div>`;
        }

        builtIn.forEach((r, i) => {
            html += `<div class="review-card">
                <div class="review-header">
                    <span class="reviewer-name">User ${i + 1}</span>
                </div>
                <p class="review-text">${r}</p>
            </div>`;
        });

        if (!html) html = `<p style="color:var(--muted);font-size:14px">No reviews yet. Be the first!</p>`;
        document.getElementById('reviewsList').innerHTML = html;
    }

    function toggleFav() {

    fetch("add_favourite.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "title=" + encodeURIComponent(item.title)
    })

    .then(response => response.text())
    .then(data => {

        // SAVE TO LOCAL STORAGE
        localStorage.setItem(
            storageKey(titleParam) + 'fav',
            JSON.stringify(true)
        );

        showToast("Added to favourites ❤️");

        const btn = document.getElementById('favBtn');

        btn.textContent = "❤️ Favourited";

        btn.classList.add('saved');

        updateActivitySidebar();

    });

}

    function openModal(id) { document.getElementById(id).classList.add('open'); }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }

    // Close modal on overlay click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', e => {
            if (e.target === overlay) overlay.classList.remove('open');
        });
    });
function submitReview() {

    const text = document.getElementById('reviewText').value;

    fetch("add_review.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body:
            "title=" + encodeURIComponent(item.title)
            + "&review=" + encodeURIComponent(text)
    })

    .then(response => response.text())
    .then(data => {

        // SAVE TO LOCAL STORAGE
        localStorage.setItem(
            storageKey(titleParam) + 'review',
            JSON.stringify(text)
        );

        showToast("Review posted 💬");

        closeModal('reviewModal');

        updateActivitySidebar();

        renderReviews();

    });

}

    // Star rating picker
    let selectedRating = 0;
    document.querySelectorAll('.star-pick').forEach(star => {
        star.addEventListener('click', () => {
            selectedRating = parseInt(star.dataset.val);
            document.querySelectorAll('.star-pick').forEach((s, idx) => {
                s.classList.toggle('active', idx < selectedRating);
            });
            document.getElementById('ratingDisplay').textContent = selectedRating + ' / 5 ⭐';
        });
        star.addEventListener('mouseenter', () => {
            const v = parseInt(star.dataset.val);
            document.querySelectorAll('.star-pick').forEach((s, idx) => {
                s.style.filter = idx < v ? 'none' : 'grayscale(1) brightness(0.5)';
            });
        });
        star.addEventListener('mouseleave', () => {
            document.querySelectorAll('.star-pick').forEach((s, idx) => {
                s.style.filter = idx < selectedRating ? 'none' : 'grayscale(1) brightness(0.5)';
            });
        });
    });

function submitRating() {

    fetch("add_rating.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body:
            "title=" + encodeURIComponent(item.title)
            + "&rating=" + encodeURIComponent(selectedRating)
    })

    .then(response => response.text())
    .then(data => {

        // SAVE TO LOCAL STORAGE
        localStorage.setItem(
            storageKey(titleParam) + 'rating',
            JSON.stringify(selectedRating)
        );

        showToast("Rating saved ⭐");

        closeModal('ratingModal');

        updateActivitySidebar();

        renderPage();

    });

}

/* ── Open playlist modal: load existing playlists ── */
function openPlaylistModal() {
    document.getElementById('playlistName').value = '';
    setPlMsg('', '');
    openModal('playlistModal');

    const list = document.getElementById('plList');
    list.innerHTML = '<div class="pl-loading">Loading playlists...</div>';

    fetch('get_playlists.php')
        .then(r => r.json())
        .then(playlists => {
            if(!playlists.length){
                list.innerHTML = '<div class="pl-empty">No playlists yet — create one below!</div>';
                return;
            }
            list.innerHTML = playlists.map(name => {
                const safe = name.replace(/"/g, '&quot;');
                return `
                <div class="pl-item" id="plitem_${btoa(encodeURIComponent(name)).replace(/[^a-zA-Z0-9]/g,'')}" onclick="addToPlaylist('${safe}', this)">
                    <span class="pl-item-name">🎵 ${safe}</span>
                    <span class="pl-item-badge">Add</span>
                </div>`;
            }).join('');
        })
        .catch(() => {
            list.innerHTML = '<div class="pl-empty">Could not load playlists.</div>';
        });
}

function setPlMsg(text, type) {
    const el = document.getElementById('plMsg');
    el.textContent = text;
    el.style.display = text ? 'block' : 'none';
    el.style.background = type === 'warn'  ? 'rgba(245,166,35,0.12)'
                        : type === 'ok'    ? 'rgba(46,204,113,0.12)'
                        : type === 'error' ? 'rgba(231,76,60,0.12)' : '';
    el.style.color      = type === 'warn'  ? '#f5a623'
                        : type === 'ok'    ? '#2ecc71'
                        : type === 'error' ? '#e74c3c' : '';
    el.style.border     = type === 'warn'  ? '1px solid rgba(245,166,35,0.3)'
                        : type === 'ok'    ? '1px solid rgba(46,204,113,0.3)'
                        : type === 'error' ? '1px solid rgba(231,76,60,0.3)' : '';
}

function addToPlaylist(name, el) {
    if(el.classList.contains('already-in')) return;

    saveToPlaylist(name, (result) => {
        if(result === 'saved') {
            el.classList.add('already-in');
            el.querySelector('.pl-item-badge').textContent = '✅ Added';
            el.querySelector('.pl-item-badge').classList.add('added');
            el.onclick = null;
            setStore('playlist', true);
            setStore('playlistName', name);
            updateActivitySidebar();
            document.getElementById('playBtn').textContent = '📋 In Playlist';
            document.getElementById('playBtn').classList.add('saved');
            showToast('Added to "' + name + '"', '📋');
        } else if(result === 'already exists') {
            el.classList.add('already-in');
            el.querySelector('.pl-item-badge').textContent = 'Already in';
            el.querySelector('.pl-item-badge').classList.add('added');
            el.onclick = null;
            setPlMsg('⚠️ Already in "' + name + '"', 'warn');
        } else {
            setPlMsg('❌ Error: ' + result, 'error');
        }
    });
}

function submitPlaylist() {
    const name = document.getElementById('playlistName').value.trim();
    if(!name){
        setPlMsg('⚠️ Please enter a playlist name.', 'warn');
        return;
    }

    saveToPlaylist(name, (result) => {
        if(result === 'saved') {
            setPlMsg('✅ Added to "' + name + '"!', 'ok');
            document.getElementById('playlistName').value = '';
            setStore('playlist', true);
            setStore('playlistName', name);
            updateActivitySidebar();
            document.getElementById('playBtn').textContent = '📋 In Playlist';
            document.getElementById('playBtn').classList.add('saved');
            showToast('Added to "' + name + '"', '📋');
            // Add new playlist to the list if not already there
            const list = document.getElementById('plList');
            const emptyMsg = list.querySelector('.pl-empty');
            if(emptyMsg) emptyMsg.remove();
            const safe = name.replace(/"/g, '&quot;');
            const newEl = document.createElement('div');
            newEl.className = 'pl-item already-in';
            newEl.innerHTML = `<span class="pl-item-name">🎵 ${safe}</span><span class="pl-item-badge added">✅ Added</span>`;
            list.prepend(newEl);
        } else if(result === 'already exists') {
            setPlMsg('⚠️ "' + name + '" already has this item.', 'warn');
        } else {
            setPlMsg('❌ Error: ' + result, 'error');
        }
    });
}

function saveToPlaylist(name, callback) {
    fetch('save_playlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'title=' + encodeURIComponent(item.title) + '&playlist=' + encodeURIComponent(name)
    })
    .then(r => r.text())
    .then(data => callback(data.trim()))
    .catch(() => callback('network error'));
}

    /* ── Init ── */
    renderPage();

    document.getElementById('playlistName').addEventListener('keydown', e => {
        if(e.key === 'Enter') submitPlaylist();
    });
</script>
</body>
</html>