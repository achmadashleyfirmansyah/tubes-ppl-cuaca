<?php
require_once 'config.php';

/* =============================
   Ambil lokasi
============================= */
if (isset($_GET['lat']) && isset($_GET['lon'])) {
    $lat = $_GET['lat'];
    $lon = $_GET['lon'];
    $locationName = $_GET['loc'] ?? 'Lokasi dipilih';
} else {
    $ipLocation = getLocationFromIP();
    if ($ipLocation) {
        $lat = $ipLocation['latitude'];
        $lon = $ipLocation['longitude'];
        $locationName = $ipLocation['location'];
    } else {
        $lat = DEFAULT_LATITUDE;
        $lon = DEFAULT_LONGITUDE;
        $locationName = DEFAULT_LOCATION;
    }
}

/* =============================
   Ambil data cuaca
============================= */
$weather = getWeatherData($lat, $lon);
$current = $weather['current'];
$hourly  = $weather['hourly'];
$daily = $weather['daily'];


/* =============================
   Data utama
============================= */
$desc = getWeatherDescription($current['weather_code']);
$windDir = getWindDirection($current['wind_direction_10m']);

/* =============================
   Data grafik (12 jam)
============================= */
$labels = array_slice($hourly['time'], 0, 12);
$temp   = array_slice($hourly['temperature_2m'], 0, 12);
$rain   = array_slice($hourly['precipitation_probability'], 0, 12);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Cuaca</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(180deg,#1e3c72,#2a5298);
    color: #fff;
}

.container {
    max-width: 1200px;
    margin: auto;
    padding: 30px;
}

h2 {
    margin-bottom: 10px;
}

.location {
    opacity: 0.85;
    margin-bottom: 25px;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit,minmax(200px,1fr));
    gap: 18px;
}

.card {
    background: rgba(255,255,255,0.14);
    padding: 18px;
    border-radius: 18px;
    transition: .3s;
}

.card span {
    font-size: 13px;
    opacity: .8;
}

.card strong {
    display: block;
    font-size: 20px;
    margin-top: 6px;
}

.card:hover {
    background: rgba(255,255,255,0.24);
}

.chart-box {
    margin-top: 30px;
    background: rgba(255,255,255,0.14);
    padding: 20px;
    border-radius: 18px;
}

@media(max-width:600px){
    .container { padding: 20px; }
}
</style>
</head>

<body>

<div class="container">
    <h2>Detail Cuaca</h2>
    <div class="location">📍 <?= $locationName ?></div>

    <div class="detail-grid">
        <div class="card">
            <span>Suhu</span>
            <strong><?= $current['temperature_2m'] ?>°C</strong>
        </div>

        <div class="card">
            <span>Terasa Seperti</span>
            <strong><?= $current['apparent_temperature'] ?>°C</strong>
        </div>

        <div class="card">
            <span>Kondisi</span>
            <strong><?= $desc['icon'] ?> <?= $desc['desc'] ?></strong>
        </div>

        <div class="card">
            <span>Kelembapan</span>
            <strong><?= $current['relative_humidity_2m'] ?>%</strong>
        </div>

        <div class="card">
            <span>Angin</span>
            <strong><?= $current['wind_speed_10m'] ?> km/j (<?= $windDir ?>)</strong>
        </div>

        <div class="card">
            <span>Tutupan Awan</span>
            <strong><?= $current['cloud_cover'] ?>%</strong>
        </div>

        <div class="card">
            <span>Curah Hujan</span>
            <strong><?= $current['precipitation'] ?> mm</strong>
        </div>
    </div>

    <div class="chart-box">
        <canvas id="tempChart"></canvas>
    </div>

    <div class="chart-box">
        <canvas id="rainChart"></canvas>
    </div>
    <h3 style="margin-top:40px">Prakiraan Harian</h3>

    <div class="detail-grid">
    <?php for ($i=0; $i<5; $i++): 
        $d = getWeatherDescription($daily['weather_code'][$i]);
    ?>
        <div class="card">
            <span><?= date('l, d M', strtotime($daily['time'][$i])) ?></span>
            <strong><?= $d['icon'] ?> <?= $d['desc'] ?></strong>
            <small>
                🌡️ <?= $daily['temperature_2m_min'][$i] ?>° /
                <?= $daily['temperature_2m_max'][$i] ?>°
            </small><br>
            <small>🌧️ <?= $daily['precipitation_probability_max'][$i] ?>%</small>
        </div>
    <?php endfor; ?>
    </div>

</div>

<script>
const labels = <?= json_encode(array_map(fn($t)=>date('H:i', strtotime($t)), $labels)) ?>;

const commonOptions = {
    responsive: true,
    plugins: {
        legend: {
            labels: {
                color: '#ffffff'
            }
        }
    },
    scales: {
        x: {
            ticks: { color: '#ffffff' },
            grid: { color: 'rgba(255,255,255,0.1)' }
        },
        y: {
            ticks: { color: '#ffffff' },
            grid: { color: 'rgba(255,255,255,0.1)' }
        }
    }
};

new Chart(document.getElementById('tempChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [{
            label: 'Suhu (°C)',
            data: <?= json_encode($temp) ?>,
            borderWidth: 2,
            tension: 0.4
        }]
    },
    options: commonOptions
});

new Chart(document.getElementById('rainChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            label: 'Peluang Hujan (%)',
            data: <?= json_encode($rain) ?>
        }]
    },
    options: commonOptions
});
</script>


</body>
</html>
