<?php
require_once 'config.php';

/*DETEKSI LOKASI*/
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

/*AMBIL DATA CUACA*/
$weatherData = getWeatherData($latitude, $longitude);

if (!$weatherData || !isset($weatherData['daily'])) {
    die("Gagal mengambil data prakiraan cuaca.");
}

/*DATA PRAKIRAAN 7 HARI*/
$dailyData = [];
for ($i = 0; $i < 7; $i++) {
    $dailyData[] = [
        'date' => $weatherData['daily']['time'][$i],
        'max' => round($weatherData['daily']['temperature_2m_max'][$i]),
        'min' => round($weatherData['daily']['temperature_2m_min'][$i]),
        'code' => $weatherData['daily']['weather_code'][$i],
        'rain' => $weatherData['daily']['precipitation_probability_max'][$i]
    ];
}

$hariIndo = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Prakiraan Cuaca - <?= htmlspecialchars($locationName); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

            <style>
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

        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: #fff;
            padding: 20px;
        }

        .container {
            max-width: 1300px;
            margin: auto;
        }

        .title {
            font-size: 26px;
            font-weight: bold;
            margin: 25px 0 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .subtitle {
            opacity: .8;
            margin-bottom: 30px;
        }

        .forecast-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 18px;
        }

        .forecast-card {
            background: rgba(255,255,255,.12);
            backdrop-filter: blur(10px);
            border-radius: 18px;
            padding: 22px;
            text-align: center;
            transition: .3s;
        }

        .forecast-card:hover {
            transform: translateY(-6px);
            background: rgba(255,255,255,.2);
        }

        .day-name {
            font-weight: bold;
            margin-bottom: 6px;
        }

        .date {
            font-size: 13px;
            opacity: .7;
        }

        .icon {
            font-size: 42px;
            margin: 18px 0;
        }

        .temp {
            font-size: 26px;
            font-weight: bold;
        }

        .range {
            font-size: 14px;
            opacity: .8;
            margin-bottom: 10px;
        }

        .rain {
            font-size: 13px;
            background: rgba(0,0,0,.2);
            padding: 6px 10px;
            border-radius: 20px;
            display: inline-block;
        }

        @media(max-width:1024px){
            .forecast-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media(max-width:600px){
            .forecast-grid {
                grid-template-columns: repeat(2, 1fr);
            }
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

    <!-- JUDUL -->
    <div class="title">
        <i class="fas fa-calendar-days"></i>
        Prakiraan Cuaca 7 Hari
    </div>
    <div class="subtitle">
        Lokasi: <strong><?= htmlspecialchars($locationName); ?></strong>
    </div>

    <!-- PRAKIRAAN -->
    <div class="forecast-grid">
        <?php foreach ($dailyData as $i => $day): 
            $time = strtotime($day['date']);
            $dayName = $i === 0 ? 'Hari Ini' : $hariIndo[date('w', $time)];
            $weather = getWeatherDescription($day['code']);
        ?>
        <div class="forecast-card">
            <div class="day-name"><?= $dayName; ?></div>
            <div class="date"><?= date('d M Y', $time); ?></div>

            <div class="icon"><?= $weather['icon']; ?></div>

            <div class="temp"><?= $day['max']; ?>°C</div>
            <div class="range"><?= $day['min']; ?>° – <?= $day['max']; ?>°</div>

            <div class="rain">
                <i class="fas fa-umbrella"></i>
                <?= $day['rain']; ?>%
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>

</body>
</html>
