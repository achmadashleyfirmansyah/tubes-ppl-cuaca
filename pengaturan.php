<?php
require_once 'config.php';

/* =============================
   LOAD & SIMPAN PENGATURAN
============================= */
$settingsFile = 'user_settings.json';

$defaultSettings = [
    'auto_location' => '1',
    'default_city' => DEFAULT_LOCATION,
    'latitude' => DEFAULT_LATITUDE,
    'longitude' => DEFAULT_LONGITUDE,
    'temperature_unit' => 'c',
    'theme' => 'dark',
    'forecast_days' => 7,
    'cache_time' => 15
];

if (file_exists($settingsFile)) {
    $settings = array_merge(
        $defaultSettings,
        json_decode(file_get_contents($settingsFile), true)
    );
} else {
    $settings = $defaultSettings;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'auto_location' => $_POST['auto_location'],
        'default_city' => $_POST['default_city'],
        'latitude' => $_POST['latitude'],
        'longitude' => $_POST['longitude'],
        'temperature_unit' => $_POST['temperature_unit'],
        'theme' => $_POST['theme'],
        'forecast_days' => $_POST['forecast_days'],
        'cache_time' => $_POST['cache_time']
    ];

    file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT));

    // redirect ke halaman utama
    header("Location: index.php");
    exit;
}


$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<meta charset="UTF-8">
<title>Pengaturan Pengguna</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* ===== STYLE ASLI DIPERTAHANKAN ===== */
body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background: linear-gradient(135deg,#1e3c72,#2a5298);
    color: #fff;
    padding: 20px;
}
.container { max-width: 1200px; margin: auto; }
.topbar { width: 100%; margin-bottom: 30px; }
.topbar-left { display: flex; align-items: center; gap: 30px; }
.brand { font-size: 22px; font-weight: bold; display: flex; gap: 8px; }

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
    gap: 8px;
    padding: 10px 18px;
    border-radius: 30px;
    color: white;
    text-decoration: none;
}
.menu-pill li a.active {
    background: #ffc107;
    color: #1e3c72;
    font-weight: bold;
}

.title {
    font-size: 26px;
    font-weight: bold;
    margin: 25px 0 10px;
    display: flex;
    gap: 10px;
}

.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit,minmax(260px,1fr));
    gap: 18px;
}

.card {
    background: rgba(255,255,255,.12);
    border-radius: 18px;
    padding: 20px;
}

.card span {
    font-size: 13px;
    opacity: .8;
}

.card input, .card select {
    width: 100%;
    margin-top: 8px;
    padding: 10px;
    border-radius: 10px;
    border: none;
}

.save-btn {
    background: #ffc107;
    border: none;
    padding: 14px;
    border-radius: 12px;
    font-weight: bold;
    width: 100%;
    cursor: pointer;
}
.success {
    color: #00ffcc;
    margin-bottom: 20px;
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
                <a href="pengaturan.php" class="<?= $currentPage == 'pengaturan.php' ? 'active' : '' ?>">
                    <i class="fas fa-cog"></i> Pengaturan
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

<div class="title">
    <i class="fas fa-cog"></i> Pengaturan Pengguna
</div>

<?php if (!empty($saved)): ?>
<div class="success">✅ Pengaturan berhasil disimpan</div>
<?php endif; ?>

<form method="post">
<div class="settings-grid">

    <div class="card">
        <span>Lokasi Otomatis (IP)</span>
        <select name="auto_location">
            <option value="1" <?= $settings['auto_location']=='1'?'selected':'' ?>>Aktif</option>
            <option value="0" <?= $settings['auto_location']=='0'?'selected':'' ?>>Nonaktif</option>
        </select>
    </div>

    <div class="card">
        <span>Kota Default</span>
        <input type="text" name="default_city" value="<?= $settings['default_city']; ?>">
    </div>

    <div class="card">
        <span>Latitude</span>
        <input type="text" name="latitude" value="<?= $settings['latitude']; ?>">
    </div>

    <div class="card">
        <span>Longitude</span>
        <input type="text" name="longitude" value="<?= $settings['longitude']; ?>">
    </div>

    <div class="card">
        <span>Satuan Suhu</span>
        <select name="temperature_unit">
            <option value="c" <?= $settings['temperature_unit']=='c'?'selected':'' ?>>Celsius (°C)</option>
            <option value="f" <?= $settings['temperature_unit']=='f'?'selected':'' ?>>Fahrenheit (°F)</option>
        </select>
    </div>

    <div class="card">
        <span>Tema Tampilan</span>
        <select name="theme">
            <option value="dark">Gelap</option>
            <option value="light">Terang</option>
        </select>
    </div>

    <div class="card">
        <span>Jumlah Hari Prakiraan</span>
        <select name="forecast_days">
            <option>3</option>
            <option>5</option>
            <option selected>7</option>
        </select>
    </div>

    <div class="card">
        <span>Cache Data (menit)</span>
        <input type="number" name="cache_time" value="<?= $settings['cache_time']; ?>">
    </div>

    <div class="card">
        <button class="save-btn">
            Simpan Pengaturan
        </button>
    </div>

</div>
</form>

</div>
</body>
</html>
