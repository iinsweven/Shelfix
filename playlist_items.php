<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location:index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$playlist = $_GET['playlist'] ?? '';

$sql = "SELECT * FROM playlists
        WHERE user_id='$user_id'
        AND playlist_name='$playlist'
        AND media_title != '__empty__'";
$result = mysqli_query($conn, $sql);

$titles = [];
while($row = mysqli_fetch_assoc($result)){
    $titles[] = $row['media_title'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($playlist); ?> — Shelfix</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #0a0a0a;
            color: white;
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
        }

        .header {
            width: 100%; height: 70px;
            background: #111;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            border-bottom: 1px solid #222;
            position: fixed; top: 0;
            z-index: 999;
        }
        .left-section { display: flex; align-items: center; gap: 35px; }
        .logo-section { display: flex; align-items: center; gap: 10px; }
        .logoh { width: 40px; border-radius: 50%; }
        .logo-section h1 { color: white; font-size: 26px; font-family: 'Bebas Neue', sans-serif; letter-spacing: 2px; }
        .nav-links { display: flex; gap: 22px; }
        .nav-links a { text-decoration: none; color: #999; font-size: 15px; font-weight: 500; transition: color 0.2s; }
        .nav-links a:hover { color: white; }
        .right-section { display: flex; align-items: center; gap: 12px; }
        .menu-container { position: relative; }
        .menu-btn { font-size: 28px; color: white; cursor: pointer; margin-right: 10px; }
        .dropdown {
            position: absolute; right: 0; top: 42px;
            background: #1a1a1a; width: 160px;
            border-radius: 10px; display: none;
            overflow: hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.5);
            border: 1px solid #2a2a2a; z-index: 1000;
        }
        .dropdown a { display: block; padding: 14px 16px; color: white; text-decoration: none; border-bottom: 1px solid #2a2a2a; font-size: 14px; transition: background 0.15s; }
        .dropdown a:hover { background: #2a2a2a; }
        .dropdown a:last-child { border-bottom: none; }

        .page-body { padding-top: 70px; }

        .playlist-hero {
            background: linear-gradient(135deg, #0f0f0f 0%, #1a0a00 50%, #0a0a0a 100%);
            padding: 50px 40px 40px;
            border-bottom: 1px solid #1e1e1e;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .back-btn {
            background: #1a1a1a;
            border: 1px solid #2a2a2a;
            color: #999;
            padding: 9px 18px;
            border-radius: 25px;
            text-decoration: none;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .back-btn:hover { color: white; border-color: #444; }
        .hero-text h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 52px;
            letter-spacing: 3px;
            color: white;
        }
        .hero-text h1 span { color: #f5a623; }
        .hero-text p { color: #888; font-size: 15px; margin-top: 6px; }

        .media-section { padding: 30px 40px 60px; }
        .section-heading {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 26px;
            letter-spacing: 2px;
            color: white;
            margin-bottom: 20px;
            display: flex; align-items: center; gap: 12px;
        }
        .section-heading::after { content: ''; flex: 1; height: 1px; background: #222; }

        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            gap: 20px;
        }
        .media-card {
            background: #141414;
            border: 1px solid #1e1e1e;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
            position: relative;
        }
        .media-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.6);
            border-color: #f5a623;
        }
        .media-card-img { width: 100%; aspect-ratio: 2/3; object-fit: cover; display: block; }
        .media-card-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, transparent 55%);
            opacity: 0; transition: opacity 0.2s;
        }
        .media-card:hover .media-card-overlay { opacity: 1; }
        .media-card-body { padding: 12px; }
        .media-card-title { font-size: 14px; font-weight: 700; color: white; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .media-card-sub { font-size: 12px; color: #666; }
        .media-card-rating {
            position: absolute; top: 8px; right: 8px;
            background: rgba(0,0,0,0.8); border: 1px solid #f5a623;
            color: #f5a623; font-size: 11px; font-weight: 700;
            padding: 3px 8px; border-radius: 5px;
        }
        .empty-state { text-align: center; padding: 80px 20px; color: #555; }
        .empty-state span { font-size: 56px; display: block; margin-bottom: 16px; }
        .empty-state p { font-size: 16px; }
    </style>
</head>
<body>

<div class="header">
    <div class="left-section">
        <div class="logo-section">
            <img src="logoshelfix.png" class="logoh">
            <h1>Shelfix</h1>
        </div>
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="playlists.php">My Playlist</a>
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

    <div class="playlist-hero">
        <a href="playlists.php" class="back-btn">← Back</a>
        <div class="hero-text">
            <h1><span><?php echo htmlspecialchars($playlist); ?></span></h1>
        </div>
    </div>

    <div class="media-section">
        <?php if(empty($titles)): ?>
            <div class="empty-state">
                <span>🎬</span>
                <p>No items in this playlist yet.</p>
            </div>
        <?php else: ?>
            <div class="section-heading">All Items</div>
            <div class="media-grid" id="media-grid"></div>
        <?php endif; ?>
    </div>

</div>

<!-- Load script.js FIRST so items array is available -->
<script src="script.js"></script>
<script>
    const playlistTitles = <?php echo json_encode($titles); ?>;

    function toggleMenu() {
        const menu = document.getElementById('dropdownMenu');
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }
    document.addEventListener('click', e => {
        const mc = document.querySelector('.menu-container');
        if(mc && !mc.contains(e.target)){
            document.getElementById('dropdownMenu').style.display = 'none';
        }
    });

    function buildPlaylistGrid() {
        const grid = document.getElementById('media-grid');
        if(!grid) return;

        playlistTitles.forEach(title => {
            const item = items.find(i => i.title === title);
            const card = document.createElement('div');
            card.className = 'media-card';
            card.onclick = () => window.location.href = 'media_details.php?title=' + encodeURIComponent(title);

            if(item){
                card.innerHTML = `
                    <img src="${item.image}" class="media-card-img" alt="${item.title}" onerror="this.src='logoshelfix.png'">
                    <div class="media-card-overlay"></div>
                    ${item.rating ? `<div class="media-card-rating">⭐ ${item.rating.split('/')[0]}</div>` : ''}
                    <div class="media-card-body">
                        <div class="media-card-title">${item.title}</div>
                        <div class="media-card-sub">${item.type || ''} • ${item.year || ''}</div>
                    </div>
                `;
            } else {
                card.innerHTML = `
                    <img src="logoshelfix.png" class="media-card-img" alt="${title}">
                    <div class="media-card-overlay"></div>
                    <div class="media-card-body">
                        <div class="media-card-title">${title}</div>
                        <div class="media-card-sub">—</div>
                    </div>
                `;
            }
            grid.appendChild(card);
        });
    }

    buildPlaylistGrid();
</script>
</body>
</html>