<?php
require 'config.php';
$news = getDisasterNews(8);
$data = $news['events'] ?? [];
?>
<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<meta charset="UTF-8">
<title>Berita Bencana Global</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ===== STYLE KHUSUS NEWS ===== */

body {
    font-family: 'Inter', 'Segoe UI', Tahoma, sans-serif;
    background: linear-gradient(135deg, #1e3c72, #2a5298);
    color: #333;
}

/* Container utama (nyatu & gede) */
.container {
    max-width: 1300px;
    margin: 40px auto;
    padding: 0 30px;
}

/* Judul halaman */
.page-title {
    color: white;
    font-size: 26px;
    margin-bottom: 30px;
}

/* Grid card */
.card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 28px;
}

/* Card berita */
.card {
    background: rgba(255,255,255,0.95);
    border-radius: 18px;
    padding: 26px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.18);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 45px rgba(0,0,0,0.25);
}

/* Judul bencana */
.card h3 {
    font-size: 17px;
    font-weight: 700;
    color: #111;   /* HITAM TEGAS */
    margin-bottom: 10px;
}


.card-location {
    font-size: 13px;
    color: #374151;        /* abu gelap */
    margin-bottom: 6px;
    line-height: 1.5;
}

.card-date {
    font-size: 13px;
    color: #111827;        /* hampir hitam */
    font-weight: 600;
    margin-bottom: 18px;
}

/* Emoji icon biar tegas */
.icon {
    margin-right: 6px;
}



/* Link */
.card-link {
    align-self: flex-start;
    color: #1e3c72;
    font-weight: bold;
    text-decoration: none;
}

.card-link:hover {
    text-decoration: underline;
}

/* Jika tidak ada berita */
.card-grid p {
    color: white;
    font-size: 16px;
}
.card-img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    border-radius: 14px;
    margin-bottom: 15px;
}

/* tombol kembali */
.back-btn {
    background: rgba(255,255,255,0.2);
    padding: 8px 16px;
    border-radius: 10px;
}

.back-btn:hover {
    background: rgba(255,255,255,0.35);
}
.container {
    max-width: 1300px;
    margin: 40px auto;
    padding: 0 30px;
}
/* ===== TOPBAR (SAMA DENGAN INDEX) ===== */
.topbar {
    width: 100%;
    margin-bottom: 30px;
}

.topbar-left {
    display: flex;
    align-items: center;
    gap: 30px;
}

.brand {
    font-size: 22px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
    color: white;
}

.menu-pill {
    list-style: none;
    display: flex;
    gap: 10px;
    padding: 6px;
    background: rgba(255,255,255,0.08);
    border-radius: 40px;
}

.menu-pill li a {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 30px;
    color: white;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.25s ease;
}

.menu-pill li a:hover {
    background: rgba(255,255,255,0.18);
}

.menu-pill li a.active {
    background: #ffc107;
    color: #1e3c72;
    font-weight: 700;
}

/* Paksa warna teks di dalam card */
.card {
    color: #111; /* default hitam */
}

/* Lokasi */
.card-location {
    color: #374151 !important; /* abu gelap */
}

/* Tanggal */
.card-date {
    color: #1f2937 !important; /* hampir hitam */
}

/* Emoji icon */
.card-location .icon,
.card-date .icon {
    color: #1e3c72; /* biru gelap biar kontras */
}


</style>
</head>
<body>

<nav class="topbar">
    <div class="topbar-left">
        <div class="brand">

            ☁️ <span>Cuaca Indonesia</span>
        </div>
        <ul class="menu-pill">
            <li>
                <a href="index.php" class="<?= $currentPage == 'index.php' ? 'active' : '' ?>">
                    <i class="fas fa-house"></i> Beranda
                </a>
            </li>
            <li>
                <a href="prakiraan.php" class="<?= $currentPage == 'prakiraan.php' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-days"></i> Prakiraan
                </a>
            </li>
            <li>
                <a href="peta.php" class="<?= $currentPage == 'peta.php' ? 'active' : '' ?>">
                    <i class="fas fa-map"></i> Peta
                </a>
            </li>
            <li>
                <a href="detail.php" class="<?= $currentPage == 'detail.php' ? 'active' : '' ?>">
                    <i class="fas fa-circle-info"></i> Detail
                </a>
            </li>
            <li>
                <a href="news.php" class="<?= $currentPage == 'news.php' ? 'active' : '' ?>">
                    <i class="fas fa-newspaper"></i> Berita
                </a>
            </li>

            <li>
                <a href="bantuan.php" class="<?= $currentPage == 'bantuan.php' ? 'active' : '' ?>">
                    <i class="fas fa-question-circle"></i> Bantuan
                </a>
            </li>
        </ul>
    </div>
</nav>
<div class="container">
    <div class="card-grid">
        <?php if (empty($data)): ?>
            <p>Tidak ada berita tersedia.</p>
        <?php else: ?>
     <?php foreach ($data as $event): ?>
<?php
    $info = formatDisaster($event['title']);
    $date = date('d M Y', strtotime($event['geometry'][0]['date']));
    $place = $event['geometry'][0]['coordinates'][1] ?? '';
?>
    <div class="card">
        <img src="assets/<?= $info['image'] ?>" class="card-img">

        <h3><?= $info['type'] ?></h3>

       <p class="card-location">
    <span class="icon">📍</span>
    <?= htmlspecialchars($event['title']) ?>
</p>

<p class="card-date">
    <span class="icon">📅</span>
    <?= $date ?>
</p>


        <a class="card-link"
           href="<?= $event['sources'][0]['url'] ?? '#' ?>"
           target="_blank">
            Lihat detail
        </a>
    </div>
<?php endforeach; ?>

        <?php endif; ?>
    </div>
</div>

</body>
</html>
