<?php
require 'config.php';
$news = getDisasterNews(8);
$data = $news['events'] ?? [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Berita Bencana Global</title>

<style>
/* ===== STYLE KHUSUS NEWS ===== */

body {
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
/* ===== NAVBAR ===== */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 50px;
    background: linear-gradient(135deg, #4facfe, #00f2fe);
    color: white;
}

.navbar-logo {
    font-size: 20px;
    font-weight: bold;
}

.navbar-menu a {
    color: white;
    text-decoration: none;
    margin-left: 30px;
    padding-bottom: 6px;
    font-size: 15px;
}

.navbar-menu a:hover,
.navbar-menu a.active {
    border-bottom: 2px solid white;
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

</style>
</head>

<body>

<div class="navbar">
    <div class="navbar-logo">
        🌤️ Info Cuaca & Bencana
    </div>

    <div class="navbar-menu">
        <a href="index.php">Home</a>
        <a href="news.php" class="active">Berita Bencana</a>
        <a href="index.php" class="back-btn">⬅ Kembali</a>
    </div>
</div>

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
