<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shelfix Home</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0; padding: 0;
            background-color: #0a0a0a;
            font-family: 'Outfit', Arial, sans-serif;
            overflow-x: hidden;
        }

        /* HEADER */
        .header {
            width: 100%; height: 70px;
            background-color: #111;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            box-sizing: border-box;
            border-bottom: 1px solid #222;
            position: fixed; top: 0;
            z-index: 999;
        }
        .left-section { display: flex; align-items: center; gap: 35px; }
        .logo-section { display: flex; align-items: center; gap: 10px; }
        .logoh { width: 40px; border-radius: 50%; }
        .logo-section h1 { color: white; font-size: 26px; font-family: 'Bebas Neue', sans-serif; letter-spacing: 2px; }
        .nav-links { display: flex; gap: 22px; }
        .nav-links a {
            text-decoration: none; color: #999;
            font-size: 15px; font-weight: 500;
            transition: color 0.2s;
        }
        .nav-links a:hover { color: white; }

        .right-section { display: flex; align-items: center; gap: 12px; }
        .search-box { display: flex; align-items: center; gap: 8px; position: relative; }
        .search-input {
            width: 240px; padding: 9px 16px;
            border: 1px solid #2a2a2a; border-radius: 25px;
            outline: none; background: #1a1a1a;
            color: white; font-size: 14px;
            font-family: 'Outfit', sans-serif;
            transition: border-color 0.2s;
        }
        .search-input:focus { border-color: #f5a623; }
        .search-btn {
            padding: 9px 18px; border: none;
            border-radius: 25px; background: #f5a623;
            color: black; cursor: pointer; font-size: 14px;
            font-weight: 700; font-family: 'Outfit', sans-serif;
            transition: background 0.2s;
        }
        .search-btn:hover { background: #ffbe45; }

        #search-results {
            position: absolute; top: calc(100% + 8px);
            left: 0; right: 0;
            background: #141414;
            border: 1px solid #2a2a2a;
            border-radius: 12px;
            overflow-y: auto; max-height: 380px;
            z-index: 999;
            box-shadow: 0 20px 50px rgba(0,0,0,0.7);
        }
        .result-card {
            display: flex; align-items: center; gap: 14px;
            padding: 12px 16px; border-bottom: 1px solid #1e1e1e;
            cursor: pointer; transition: background 0.15s;
        }
        .result-card:last-child { border-bottom: none; }
        .result-card:hover { background: #1e1e1e; }
        .result-image { width: 48px; height: 68px; object-fit: cover; border-radius: 6px; }
        .result-info h3 { font-size: 14px; color: white; font-weight: 600; margin: 0 0 3px; }
        .result-info p { font-size: 12px; color: #888; margin: 0; }

        .menu-container { position: relative; }
        .menu-btn { font-size: 28px; color: white; cursor: pointer; margin-right: 10px; }
        .dropdown {
            position: absolute; right: 0; top: 42px;
            background: #1a1a1a; width: 160px;
            border-radius: 10px; display: none;
            overflow: hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.5);
            border: 1px solid #2a2a2a; z-index: 1000;
        }
        .dropdown a {
            display: block; padding: 14px 16px;
            color: white; text-decoration: none;
            border-bottom: 1px solid #2a2a2a;
            font-size: 14px; transition: background 0.15s;
        }
        .dropdown a:hover { background: #2a2a2a; }
        .dropdown a:last-child { border-bottom: none; }

        /* PAGE BODY */
        .page-body { padding-top: 70px; }

        /* HERO BANNER */
        .home-hero {
            background: linear-gradient(135deg, #0f0f0f 0%, #1a0a00 50%, #0a0a0a 100%);
            padding: 60px 40px 50px;
            text-align: center;
            border-bottom: 1px solid #1e1e1e;
        }
        .home-hero h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 64px;
            letter-spacing: 4px;
            color: white;
            margin-bottom: 12px;
        }
        .home-hero h1 span { color: #f5a623; }
        .home-hero p { color: #888; font-size: 17px; font-weight: 300; }

        /* FILTER TABS */
        .filter-bar {
            display: flex; gap: 10px;
            padding: 24px 40px 10px;
            overflow-x: auto;
        }
        .filter-btn {
            padding: 8px 20px;
            border-radius: 20px;
            border: 1px solid #2a2a2a;
            background: #141414;
            color: #888;
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            transition: all 0.2s;
        }
        .filter-btn:hover, .filter-btn.active {
            background: #f5a623;
            color: #000;
            border-color: #f5a623;
        }

        /* SECTION */
        .media-section { padding: 20px 40px 40px; }
        .section-heading {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 28px;
            letter-spacing: 2px;
            color: white;
            margin-bottom: 20px;
            display: flex; align-items: center; gap: 12px;
        }
        .section-heading::after {
            content: ''; flex: 1;
            height: 1px; background: #222;
        }

        /* MEDIA GRID */
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
        .media-card-img {
            width: 100%; aspect-ratio: 2/3;
            object-fit: cover; display: block;
        }
        .media-card-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, transparent 55%);
            opacity: 0; transition: opacity 0.2s;
        }
        .media-card:hover .media-card-overlay { opacity: 1; }
        .media-card-body { padding: 12px; }
        .media-card-title {
            font-size: 14px; font-weight: 700;
            color: white; margin-bottom: 4px;
            white-space: nowrap; overflow: hidden;
            text-overflow: ellipsis;
        }
        .media-card-sub { font-size: 12px; color: #666; }
        .media-card-rating {
            position: absolute; top: 8px; right: 8px;
            background: rgba(0,0,0,0.8);
            border: 1px solid #f5a623;
            color: #f5a623;
            font-size: 11px; font-weight: 700;
            padding: 3px 8px; border-radius: 5px;
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
            <a href="playlists.php">My Playlist</a>
            
        </div>
    </div>

    <div class="right-section">
        <div class="search-box">
            <input type="text" placeholder="Search..." class="search-input">
            <button class="search-btn">Search</button>
            <div id="search-results"></div>
        </div>
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

    <!-- HERO -->
    <div class="home-hero">
        <h1>Welcome to <span>Shelfix</span> 🎬📚🎵</h1>
        <p>Track movies, books, songs and your personal media journey.</p>
    </div>

    <!-- FILTER TABS -->
    <div class="filter-bar">
        <button class="filter-btn active" onclick="filterMedia('all', this)">All</button>
        <button class="filter-btn" onclick="filterMedia('Hollywood Movie', this)">🎬 Hollywood</button>
        <button class="filter-btn" onclick="filterMedia('Bollywood Movie', this)">🎥 Bollywood</button>
        <button class="filter-btn" onclick="filterMedia('English Song', this)">🎵 English Songs</button>
        <button class="filter-btn" onclick="filterMedia('Book', this)">📚 Books</button>
    </div>

    <!-- MEDIA GRID SECTION -->
    <div class="media-section">
        <div class="section-heading" id="grid-heading">🔥 Featured Media</div>
        <div class="media-grid" id="media-grid"></div>
    </div>

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

    // Build grid
    let currentFilter = 'all';

    function buildGrid(filter) {
        const grid = document.getElementById('media-grid');
        const heading = document.getElementById('grid-heading');
        grid.innerHTML = '';

        const filtered = filter === 'all'
            ? items
            : items.filter(i => i.type && i.type.toLowerCase().includes(filter.toLowerCase()));

        heading.textContent = filter === 'all' ? '🔥 Featured Media' : `🔥 ${filter}`;

        filtered.forEach(item => {
            const card = document.createElement('div');
            card.className = 'media-card';
            card.onclick = () => openMediaDetails(item.title);
            card.innerHTML = `
                <img src="${item.image}" class="media-card-img" alt="${item.title}" onerror="this.src='logoshelfix.png'">
                <div class="media-card-overlay"></div>
                ${item.rating ? `<div class="media-card-rating">⭐ ${item.rating.split('/')[0]}</div>` : ''}
                <div class="media-card-body">
                    <div class="media-card-title">${item.title}</div>
                    <div class="media-card-sub">${item.type || ''} • ${item.year || ''}</div>
                </div>
            `;
            grid.appendChild(card);
        });
    }

    function filterMedia(type, btn) {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentFilter = type;
        buildGrid(type);
    }

    function openMediaDetails(title) {
        window.location.href = 'media_details.php?title=' + encodeURIComponent(title);
    }

    // Override openDetails from script.js to use URL param
    function openDetails(title) {
        openMediaDetails(title);
    }

    buildGrid('all');
</script>
</body>
</html>