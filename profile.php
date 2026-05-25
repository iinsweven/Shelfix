<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle profile update — standard POST so file upload always works
$flash = '';
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile'){
    $new_username = trim($_POST['username']);
    $new_bio      = trim($_POST['bio']);
    $new_pic_path = '';

    if(isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK){
        $ext     = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if(in_array($ext, $allowed)){
            $upload_dir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
            if(!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            $filename = 'pfp_' . $user_id . '_' . time() . '.' . $ext;
            $dest = $upload_dir . $filename;
            if(move_uploaded_file($_FILES['profile_pic']['tmp_name'], $dest)){
                $new_pic_path = 'uploads/' . $filename;
            } else {
                $flash = 'error:Could not save image. Make sure the uploads/ folder exists and is writable.';
            }
        } else {
            $flash = 'error:Invalid image type. Use JPG, PNG, GIF or WEBP.';
        }
    }

    if(empty($flash)){
        if(empty($new_username)){
            $flash = 'error:Username cannot be empty.';
        } else {
            $esc_user = mysqli_real_escape_string($conn, $new_username);
            $chk = mysqli_query($conn, "SELECT id FROM users WHERE username='$esc_user' AND id != '$user_id'");
            if(mysqli_num_rows($chk) > 0){
                $flash = 'error:That username is already taken.';
            } else {
                $esc_bio = mysqli_real_escape_string($conn, $new_bio);
                $pic_sql = !empty($new_pic_path)
                    ? ", profile_pic='" . mysqli_real_escape_string($conn, $new_pic_path) . "'"
                    : "";
                mysqli_query($conn, "UPDATE users SET username='$esc_user', bio='$esc_bio'$pic_sql WHERE id='$user_id'");
                $flash = 'ok:Profile updated!';
            }
        }
    }

    $_SESSION['profile_flash'] = $flash;
    header("Location: profile.php");
    exit();
}

if(isset($_SESSION['profile_flash'])){
    $flash = $_SESSION['profile_flash'];
    unset($_SESSION['profile_flash']);
}

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'"));

$playlists = [];
$r = mysqli_query($conn, "SELECT DISTINCT playlist_name FROM playlists WHERE user_id='$user_id' ORDER BY playlist_name");
while($row = mysqli_fetch_assoc($r)) $playlists[] = $row['playlist_name'];

$favorites = [];
$r = mysqli_query($conn, "SELECT media_title FROM favorites WHERE user_id='$user_id'");
while($row = mysqli_fetch_assoc($r)) $favorites[] = $row['media_title'];

$ratings = [];
$r = mysqli_query($conn, "SELECT media_title, rating FROM ratings WHERE user_id='$user_id' ORDER BY id DESC");
while($row = mysqli_fetch_assoc($r)) $ratings[] = $row;

$reviews = [];
$r = mysqli_query($conn, "SELECT media_title, review FROM reviews WHERE user_id='$user_id' ORDER BY id DESC");
while($row = mysqli_fetch_assoc($r)) $reviews[] = $row;

function hasPic($u){
    return !empty($u['profile_pic']) && file_exists(__DIR__ . DIRECTORY_SEPARATOR . ltrim($u['profile_pic'], '/\\'));
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($user['username']); ?> — Shelfix Profile</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{--bg:#0a0a0a;--surface:#111;--surface2:#1a1a1a;--border:#2a2a2a;--gold:#f5a623;--gold-h:#ffbe45;--muted:#888}
        body{background:var(--bg);font-family:'Outfit',sans-serif;color:#fff;min-height:100vh;overflow-x:hidden}
        .header{width:100%;height:70px;background:var(--surface);display:flex;justify-content:space-between;align-items:center;padding:0 30px;border-bottom:1px solid var(--border);position:fixed;top:0;z-index:999}
        .left-section{display:flex;align-items:center;gap:35px}
        .logo-section{display:flex;align-items:center;gap:10px}
        .logoh{width:40px;border-radius:50%}
        .logo-section h1{color:#fff;font-size:26px;font-family:'Bebas Neue',sans-serif;letter-spacing:2px}
        .nav-links{display:flex;gap:22px}
        .nav-links a{text-decoration:none;color:#999;font-size:15px;font-weight:500;transition:color .2s}
        .nav-links a:hover{color:#fff}
        .right-section{display:flex;align-items:center;gap:12px}
        .menu-container{position:relative}
        .menu-btn{font-size:28px;color:#fff;cursor:pointer;margin-right:10px}
        .dropdown{position:absolute;right:0;top:42px;background:#1a1a1a;width:160px;border-radius:10px;display:none;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,.5);border:1px solid #2a2a2a;z-index:1000}
        .dropdown a{display:block;padding:14px 16px;color:#fff;text-decoration:none;border-bottom:1px solid #2a2a2a;font-size:14px;transition:background .15s}
        .dropdown a:hover{background:#2a2a2a}
        .dropdown a:last-child{border-bottom:none}
        .page-body{padding-top:70px;max-width:860px;margin:0 auto;padding-bottom:60px}
        .profile-hero{background:linear-gradient(135deg,#0f0f0f 0%,#1a0a00 60%,#0a0a0a 100%);padding:50px 40px 40px;display:flex;align-items:center;gap:30px;border-bottom:1px solid #1e1e1e}
        .avatar-circle{width:110px;height:110px;border-radius:50%;border:3px solid var(--gold);background:#1a1a1a;display:flex;align-items:center;justify-content:center;font-size:42px;font-weight:700;color:var(--gold);flex-shrink:0;overflow:hidden}
        .avatar-circle img{width:100%;height:100%;object-fit:cover;display:block}
        .profile-info{flex:1}
        .profile-username{font-family:'Bebas Neue',sans-serif;font-size:46px;letter-spacing:3px;color:#fff;line-height:1}
        .profile-username span{color:var(--gold)}
        .profile-bio{color:var(--muted);font-size:15px;margin-top:8px;font-weight:300}
        .profile-stats{display:flex;gap:28px;margin-top:18px}
        .stat{text-align:center}
        .stat-num{font-size:22px;font-weight:700;color:var(--gold);font-family:'Bebas Neue',sans-serif;letter-spacing:1px}
        .stat-label{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-top:2px}
        .edit-btn{padding:10px 22px;border:1px solid var(--gold);background:transparent;color:var(--gold);border-radius:25px;cursor:pointer;font-family:'Outfit',sans-serif;font-size:14px;font-weight:600;transition:all .2s;margin-top:16px;display:inline-block}
        .edit-btn:hover{background:var(--gold);color:#000}
        .section{padding:32px 40px;border-bottom:1px solid #1e1e1e}
        .section:last-child{border-bottom:none}
        .section-title{font-family:'Bebas Neue',sans-serif;font-size:26px;letter-spacing:2px;color:#fff;margin-bottom:18px;display:flex;align-items:center;gap:10px}
        .section-title::after{content:'';flex:1;height:1px;background:#222}
        .playlist-list{display:flex;flex-direction:column;gap:10px}
        .playlist-row{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:14px 20px;display:flex;align-items:center;justify-content:space-between;text-decoration:none;color:#fff;transition:border-color .2s,background .2s}
        .playlist-row:hover{border-color:var(--gold);background:#161616}
        .pl-left{display:flex;align-items:center;gap:12px;font-size:15px;font-weight:600}
        .pl-arrow{color:var(--muted);font-size:18px}
        .fav-grid{display:flex;flex-wrap:wrap;gap:10px}
        .fav-chip{background:var(--surface2);border:1px solid var(--border);border-radius:30px;padding:8px 16px;font-size:13px;color:#fff;text-decoration:none;transition:border-color .2s,background .2s;display:flex;align-items:center;gap:6px}
        .fav-chip:hover{border-color:var(--gold);background:#1e1e1e}
        .heart{color:#e74c3c}
        .rating-list{display:flex;flex-direction:column;gap:10px}
        .rating-row{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:14px 20px;display:flex;align-items:center;justify-content:space-between}
        .rating-title{font-size:15px;font-weight:600;color:#fff;text-decoration:none}
        .stars{display:flex;gap:3px}
        .star{font-size:18px;color:#333}
        .star.on{color:var(--gold)}
        .review-list{display:flex;flex-direction:column;gap:12px}
        .review-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:16px 20px}
        .review-media{font-size:14px;font-weight:700;color:var(--gold);margin-bottom:6px;text-decoration:none;display:block}
        .review-text{font-size:14px;color:#ccc;line-height:1.6;font-style:italic}
        .empty-msg{color:var(--muted);font-size:14px;padding:10px 0}
        .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.88);z-index:2000;display:none;align-items:center;justify-content:center}
        .modal-overlay.open{display:flex}
        .modal-box{background:#141414;border:1px solid #2a2a2a;border-radius:18px;padding:36px;width:100%;max-width:460px;position:relative}
        .modal-title{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:2px;color:#fff;margin-bottom:24px}
        .modal-close{position:absolute;top:18px;right:20px;font-size:22px;color:var(--muted);cursor:pointer;background:none;border:none;transition:color .2s}
        .modal-close:hover{color:#fff}
        .form-group{margin-bottom:18px}
        .form-label{display:block;font-size:13px;color:var(--muted);margin-bottom:6px;font-weight:600;text-transform:uppercase;letter-spacing:1px}
        .form-input,.form-textarea{width:100%;padding:11px 14px;background:#1a1a1a;border:1px solid #2a2a2a;border-radius:10px;color:#fff;font-family:'Outfit',sans-serif;font-size:14px;outline:none;transition:border-color .2s}
        .form-input:focus,.form-textarea:focus{border-color:var(--gold)}
        .form-textarea{resize:vertical;min-height:90px}
        .avatar-upload-row{display:flex;align-items:center;gap:16px;margin-bottom:20px}
        .avatar-sm{width:64px;height:64px;border-radius:50%;border:2px solid var(--gold);background:#1a1a1a;display:flex;align-items:center;justify-content:center;font-size:24px;color:var(--gold);overflow:hidden;flex-shrink:0}
        .avatar-sm img{width:100%;height:100%;object-fit:cover;border-radius:50%;display:block}
        .file-label{padding:9px 18px;border:1px dashed var(--border);border-radius:10px;color:var(--muted);font-size:13px;cursor:pointer;transition:all .2s;display:inline-block}
        .file-label:hover{border-color:var(--gold);color:var(--gold)}
        #pfp_input{display:none}
        .save-btn{width:100%;padding:13px;background:var(--gold);color:#000;border:none;border-radius:12px;font-family:'Outfit',sans-serif;font-size:15px;font-weight:700;cursor:pointer;transition:background .2s;margin-top:6px}
        .save-btn:hover{background:var(--gold-h)}
        .flash{padding:11px 16px;border-radius:10px;font-size:13px;margin-bottom:18px;display:none}
        .flash.ok{display:block;background:rgba(46,204,113,.15);color:#2ecc71;border:1px solid rgba(46,204,113,.3)}
        .flash.error{display:block;background:rgba(231,76,60,.15);color:#e74c3c;border:1px solid rgba(231,76,60,.3)}
        .toast{position:fixed;bottom:30px;right:30px;background:#1a1a1a;border:1px solid #2a2a2a;border-radius:12px;padding:14px 20px;display:flex;align-items:center;gap:10px;font-size:14px;color:#fff;transform:translateY(80px);opacity:0;transition:all .3s;z-index:3000;box-shadow:0 8px 30px rgba(0,0,0,.5)}
        .toast.show{transform:translateY(0);opacity:1}
    </style>
</head>
<body>

<div class="header">
    <div class="left-section">
        <div class="logo-section">
            <img src="logoshelfix.png" class="logoh" alt="Shelfix">
            <h1>Shelfix</h1>
        </div>
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="playlists.php">My Playlist</a>
        </div>
    </div>
    <div class="right-section">
        <div class="menu-container">
            <div class="menu-btn" onclick="toggleMenu()">&#9776;</div>
            <div class="dropdown" id="dropdownMenu">
                <a href="profile.php">Profile</a>
                <a href="#">Settings</a>
                <a href="index.php">Logout</a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">

    <div class="profile-hero">
        <div class="avatar-circle">
            <?php if(hasPic($user)): ?>
                <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>?v=<?php echo time(); ?>" alt="pfp">
            <?php else: ?>
                <?php echo strtoupper(substr($user['username'],0,1)); ?>
            <?php endif; ?>
        </div>
        <div class="profile-info">
            <div class="profile-username">@<span><?php echo htmlspecialchars($user['username']); ?></span></div>
            <div class="profile-bio"><?php echo !empty($user['bio'])?htmlspecialchars($user['bio']):'No bio yet — click Edit Profile to add one.'; ?></div>
            <div class="profile-stats">
                <div class="stat"><div class="stat-num"><?php echo count($playlists); ?></div><div class="stat-label">Playlists</div></div>
                <div class="stat"><div class="stat-num"><?php echo count($favorites); ?></div><div class="stat-label">Favorites</div></div>
                <div class="stat"><div class="stat-num"><?php echo count($ratings); ?></div><div class="stat-label">Ratings</div></div>
                <div class="stat"><div class="stat-num"><?php echo count($reviews); ?></div><div class="stat-label">Reviews</div></div>
            </div>
            <button class="edit-btn" onclick="openEditModal()">&#9999; Edit Profile</button>
        </div>
    </div>

    <div class="section">
        <div class="section-title">&#127925; My Playlists</div>
        <?php if(empty($playlists)): ?>
            <p class="empty-msg">No playlists yet. <a href="playlists.php" style="color:var(--gold)">Create one!</a></p>
        <?php else: ?>
            <div class="playlist-list">
                <?php foreach($playlists as $pl): ?>
                <a href="playlist_items.php?playlist=<?php echo urlencode($pl); ?>" class="playlist-row">
                    <div class="pl-left"><span>&#127925;</span><?php echo htmlspecialchars($pl); ?></div>
                    <span class="pl-arrow">&#8250;</span>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="section">
        <div class="section-title">&#10084; Favorites</div>
        <?php if(empty($favorites)): ?>
            <p class="empty-msg">No favorites yet.</p>
        <?php else: ?>
            <div class="fav-grid">
                <?php foreach($favorites as $fav): ?>
                <a href="media_details.php?title=<?php echo urlencode($fav); ?>" class="fav-chip">
                    <span class="heart">&#9829;</span><?php echo htmlspecialchars($fav); ?>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="section">
        <div class="section-title">&#11088; My Ratings</div>
        <?php if(empty($ratings)): ?>
            <p class="empty-msg">No ratings yet.</p>
        <?php else: ?>
            <div class="rating-list">
                <?php foreach($ratings as $rat): ?>
                <div class="rating-row">
                    <a href="media_details.php?title=<?php echo urlencode($rat['media_title']); ?>" class="rating-title"><?php echo htmlspecialchars($rat['media_title']); ?></a>
                    <div class="stars">
                        <?php for($s=1;$s<=5;$s++): ?><span class="star <?php echo $s<=$rat['rating']?'on':''; ?>">&#9733;</span><?php endfor; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="section">
        <div class="section-title">&#128172; My Reviews</div>
        <?php if(empty($reviews)): ?>
            <p class="empty-msg">No reviews yet.</p>
        <?php else: ?>
            <div class="review-list">
                <?php foreach($reviews as $rv): ?>
                <div class="review-card">
                    <a href="media_details.php?title=<?php echo urlencode($rv['media_title']); ?>" class="review-media"><?php echo htmlspecialchars($rv['media_title']); ?></a>
                    <div class="review-text">"<?php echo htmlspecialchars($rv['review']); ?>"</div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- EDIT MODAL — plain HTML form POST, no AJAX, guarantees file upload works -->
<div class="modal-overlay" id="editModal">
    <div class="modal-box">
        <button class="modal-close" onclick="closeEditModal()">&#10005;</button>
        <div class="modal-title">Edit Profile</div>

        <?php
            $ftype=''; $ftext='';
            if(!empty($flash)){
                if(strpos($flash,'ok:')===0)    { $ftype='ok';    $ftext=substr($flash,3); }
                if(strpos($flash,'error:')===0) { $ftype='error'; $ftext=substr($flash,6); }
            }
        ?>
        <div class="flash <?php echo $ftype; ?>"><?php echo htmlspecialchars($ftext); ?></div>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_profile">

            <div class="avatar-upload-row">
                <div class="avatar-sm" id="avatarPreview">
                    <?php if(hasPic($user)): ?>
                        <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>?v=<?php echo time(); ?>" id="previewImg" alt="">
                    <?php else: ?>
                        <span id="previewInitial"><?php echo strtoupper(substr($user['username'],0,1)); ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="file-label" for="pfp_input">&#128247; Change Photo</label>
                    <input type="file" id="pfp_input" name="profile_pic" accept="image/*" onchange="previewPic(this)">
                    <div style="font-size:12px;color:var(--muted);margin-top:5px;">JPG &middot; PNG &middot; GIF &middot; WEBP</div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" class="form-input" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Bio</label>
                <textarea class="form-textarea" name="bio" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio']??''); ?></textarea>
            </div>

            <button type="submit" class="save-btn">Save Changes</button>
        </form>
    </div>
</div>

<div class="toast" id="toast">
    <span id="toastIcon">&#9989;</span>
    <span id="toastText"></span>
</div>

<script>
function toggleMenu(){
    const m=document.getElementById('dropdownMenu');
    m.style.display=m.style.display==='block'?'none':'block';
}
document.addEventListener('click',e=>{
    const mc=document.querySelector('.menu-container');
    if(mc&&!mc.contains(e.target)) document.getElementById('dropdownMenu').style.display='none';
});
function openEditModal(){ document.getElementById('editModal').classList.add('open'); }
function closeEditModal(){ document.getElementById('editModal').classList.remove('open'); }
document.getElementById('editModal').addEventListener('click',e=>{
    if(e.target===document.getElementById('editModal')) closeEditModal();
});
function previewPic(input){
    if(!input.files||!input.files[0]) return;
    const reader=new FileReader();
    reader.onload=e=>{
        document.getElementById('avatarPreview').innerHTML=
            '<img src="'+e.target.result+'" style="width:100%;height:100%;object-fit:cover;border-radius:50%;display:block;">';
    };
    reader.readAsDataURL(input.files[0]);
}
let toastTimer;
function showToast(msg,icon){
    document.getElementById('toastText').textContent=msg;
    document.getElementById('toastIcon').textContent=icon||'✅';
    const t=document.getElementById('toast');
    t.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer=setTimeout(()=>t.classList.remove('show'),3200);
}
<?php if(!empty($flash)): ?>
window.addEventListener('DOMContentLoaded',()=>{
    <?php if(strpos($flash,'ok:')===0): ?>
        showToast(<?php echo json_encode(substr($flash,3)); ?>,'✅');
    <?php elseif(strpos($flash,'error:')===0): ?>
        openEditModal();
    <?php endif; ?>
});
<?php endif; ?>
</script>
</body>
</html>