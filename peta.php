<?php
require_once 'config.php';

/* DETEKSI LOKASI */
if (!isset($_GET['lat']) && !isset($_GET['lon'])) {
    $ipLocation = getLocationFromIP();

    if ($ipLocation) {
        $latitude = $ipLocation['latitude'];
        $longitude = $ipLocation['longitude'];
        $locationName = $ipLocation['location'];
    } else {
        $latitude = DEFAULT_LATITUDE;
        $longitude = DEFAULT_LONGITUDE;
        $locationName = DEFAULT_LOCATION;
    }
} else {
    $latitude = floatval($_GET['lat']);
    $longitude = floatval($_GET['lon']);
    $locationName = $_GET['location'] ?? DEFAULT_LOCATION;
}

/* DATA CUACA */
$weatherData = getWeatherData($latitude, $longitude);
$currentTemp = round($weatherData['current']['temperature_2m']);
$weatherInfo = getWeatherDescription($weatherData['current']['weather_code']);

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Peta Cuaca - <?= htmlspecialchars($locationName); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
        }

        .container {
            max-width: 1400px;
            margin: auto;
            padding: 20px;
        }

        .title {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .subtitle {
            opacity: .8;
            margin-bottom: 20px;
        }

        #map {
            height: 550px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0,0,0,.4);
        }

        .map-info {
            margin-top: 20px;
            background: rgba(255,255,255,.12);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .map-info .icon {
            font-size: 48px;
        }

        .map-info .temp {
            font-size: 36px;
            font-weight: bold;
        }

        .map-info .desc {
            font-size: 18px;
            opacity: .9;
        }

        @media(max-width:768px){
            #map { height: 400px; }
            .map-info { flex-direction: column; text-align: center; }
        }
        .search-box {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.search-box input {
    flex: 1;
    padding: 12px 16px;
    border-radius: 30px;
    border: none;
    outline: none;
    font-size: 14px;
}

.search-box button {
    padding: 12px 18px;
    border-radius: 30px;
    border: none;
    background: #ffc107;
    cursor: pointer;
    font-weight: bold;
}
.topbar {
    width: 100%;
    margin-bottom: 25px;
}

.topbar-left {
    display: flex;
    align-items: center;
    gap: 30px;
}

.brand {
    font-size: 22px;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 8px;
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
    transition: 0.25s;
}

.menu-pill li a:hover {
    background: rgba(255,255,255,0.18);
}

.menu-pill li a.active {
    background: #ffc107;
    color: #1e3c72;
    font-weight: bold;
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
        <i class="fas fa-map"></i> Peta Cuaca Interaktif
    </div>
    <div class="subtitle">
        Lokasi: <strong><?= htmlspecialchars($locationName); ?></strong>
    </div>
        <div class="search-box">
    <input type="text" id="searchLocation" placeholder="Cari kota atau lokasi...">
    <button onclick="searchLocation()">
        <i class="fas fa-search"></i>
    </button>
</div>

    <!-- MAP -->
    <div id="map"></div>

    <!-- INFO -->
    <div class="map-info">
        <div class="icon"><?= $weatherInfo['icon']; ?></div>
        <div>
            <div class="temp"><?= $currentTemp; ?>°C</div>
            <div class="desc"><?= $weatherInfo['desc']; ?></div>
        </div>
    </div>

</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
function searchLocation() {
    const query = document.getElementById('searchLocation').value;
    if (!query) return alert('Masukkan nama lokasi');

    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}`)
        .then(res => res.json())
        .then(data => {
            if (data.length === 0) {
                alert('Lokasi tidak ditemukan');
                return;
            }

            const lat = data[0].lat;
            const lon = data[0].lon;
            const name = data[0].display_name;

            window.location.href =
                `peta.php?lat=${lat}&lon=${lon}&location=${encodeURIComponent(name)}`;
        })
        .catch(() => alert('Gagal mencari lokasi'));
}
</script>
<script>
    const map = L.map('map').setView([<?= $latitude; ?>, <?= $longitude; ?>], 7);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker = L.marker([<?= $latitude; ?>, <?= $longitude; ?>]).addTo(map);

    marker.bindPopup(`
        <strong><?= addslashes($locationName); ?></strong><br>
        Suhu: <?= $currentTemp; ?>°C<br>
        <?= addslashes($weatherInfo['desc']); ?>
    `).openPopup();

    // 🔥 KLIK PETA TANPA POPUP
    map.on('click', function(e) {
        const lat = e.latlng.lat.toFixed(4);
        const lon = e.latlng.lng.toFixed(4);

        // update URL & reload HALAMAN PETA
        window.location.href =
            `peta.php?lat=${lat}&lon=${lon}&location=Koordinat ${lat}, ${lon}`;
    });
</script>

<script>
    // Inisialisasi peta
    const map = L.map('map').setView([<?= $latitude; ?>, <?= $longitude; ?>], 7);

    // Tile map (OpenStreetMap)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Marker lokasi
    const marker = L.marker([<?= $latitude; ?>, <?= $longitude; ?>]).addTo(map);

    marker.bindPopup(`
        <strong><?= addslashes($locationName); ?></strong><br>
        Suhu: <?= $currentTemp; ?>°C<br>
        <?= addslashes($weatherInfo['desc']); ?>
    `).openPopup();

    // Klik peta untuk pindah lokasi
    map.on('click', function(e) {
        const lat = e.latlng.lat.toFixed(4);
        const lon = e.latlng.lng.toFixed(4);

        if (confirm('Lihat cuaca di lokasi ini?')) {
            map.on('click', function(e) {
    const lat = e.latlng.lat.toFixed(4);
    const lon = e.latlng.lng.toFixed(4);

    if (confirm('Lihat cuaca di lokasi ini?')) {
        window.location.href =
            `peta.php?lat=${lat}&lon=${lon}&location=Koordinat ${lat}, ${lon}`;
    }
});

        }
    });
</script>

</body>
</html>
