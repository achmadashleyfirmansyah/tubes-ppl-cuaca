<?php
require_once 'config.php';

// Auto-detect lokasi jika tidak ada parameter
if (!isset($_GET['lat']) && !isset($_GET['lon'])) {
    // Coba dapatkan lokasi dari IP
    $ipLocation = getLocationFromIP();
    
    if ($ipLocation) {
        $latitude = $ipLocation['latitude'];
        $longitude = $ipLocation['longitude'];
        $locationName = $ipLocation['location'];
    } else {
        // Fallback ke default
        $latitude = DEFAULT_LATITUDE;
        $longitude = DEFAULT_LONGITUDE;
        $locationName = DEFAULT_LOCATION;
    }
} else {
    // Gunakan parameter dari URL
    $latitude = isset($_GET['lat']) ? floatval($_GET['lat']) : DEFAULT_LATITUDE;
    $longitude = isset($_GET['lon']) ? floatval($_GET['lon']) : DEFAULT_LONGITUDE;
    $locationName = isset($_GET['location']) ? $_GET['location'] : DEFAULT_LOCATION;
}

// Ambil data cuaca
$weatherData = getWeatherData($latitude, $longitude);

// Cek jika data cuaca valid
if (!$weatherData || !isset($weatherData['current'])) {
    die("Error: Tidak dapat mengambil data cuaca. Pastikan koneksi internet aktif.");
}

// Data cuaca saat ini
$currentTemp = round($weatherData['current']['temperature_2m']);
$feelsLike = round($weatherData['current']['apparent_temperature']);
$humidity = $weatherData['current']['relative_humidity_2m'];
$windSpeed = round($weatherData['current']['wind_speed_10m']);
$windDirection = getWindDirection($weatherData['current']['wind_direction_10m']);
$cloudCover = $weatherData['current']['cloud_cover'];
$precipitation = $weatherData['current']['precipitation'];

$weatherCode = $weatherData['current']['weather_code'];
$weatherInfo = getWeatherDescription($weatherCode);

// Prediksi 7 hari
$dailyData = [];
for ($i = 0; $i < 7; $i++) {
    $dailyData[] = [
        'date' => $weatherData['daily']['time'][$i],
        'max' => round($weatherData['daily']['temperature_2m_max'][$i]),
        'min' => round($weatherData['daily']['temperature_2m_min'][$i]),
        'code' => $weatherData['daily']['weather_code'][$i],
        'precipitation' => $weatherData['daily']['precipitation_probability_max'][$i]
    ];
}

// Nama hari dalam Bahasa Indonesia
$hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
?>
<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
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

</nav>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prakiraan Cuaca - <?php echo htmlspecialchars($locationName); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .topbar {
            width: 100%;
            margin-bottom: 25px;
        }
        
        .topbar-left {
            display: flex;
            align-items: center;
            gap: 30px;
        }
                #map {
            width: 100%;
            height: 400px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0,0,0,.4);
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-container {
            position: relative;
            width: 450px;
        }

        .search-box {
            width: 100%;
            padding: 14px 45px 14px 20px;
            border-radius: 25px;
            border: 2px solid transparent;
            font-size: 16px;
            background: white;
            color: #333;
            transition: all 0.3s;
        }

        .search-box::placeholder {
            color: rgba(0, 0, 0, 0.4);
        }

        .search-box:focus {
            outline: none;
            border-color: #ffc107;
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
        }

        .search-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #1e3c72;
            cursor: pointer;
            font-size: 20px;
            padding: 5px 10px;
            transition: all 0.3s;
        }

        .search-btn:hover {
            color: #ffc107;
        }

        #searchResults {
            position: absolute;
            top: calc(100% + 10px);
            left: 0;
            right: 0;
            background: white;
            border-radius: 15px;
            max-height: 450px;
            overflow-y: auto;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
            display: none;
            z-index: 1000;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #searchResults.show {
            display: block;
        }

        .search-result-item {
            padding: 16px 20px;
            color: #333;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        .search-result-item:hover {
            background: #f8f9fa;
            padding-left: 25px;
        }

        .result-info {
            flex: 1;
        }

        .result-name {
            font-weight: 600;
            margin-bottom: 4px;
            color: #1e3c72;
            font-size: 16px;
        }

        .result-details {
            font-size: 13px;
            color: #666;
        }

        .result-icon {
            color: #ccc;
            font-size: 16px;
        }

        .loading, .no-results {
            text-align: center;
            padding: 30px 20px;
            color: #666;
        }

        .loading i {
            font-size: 24px;
            color: #1e3c72;
            margin-bottom: 10px;
        }

        .location-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
            font-size: 24px;
        }

        .use-location-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .use-location-btn:hover {
            background: #ffc107;
            color: #1e3c72;
            transform: scale(1.1);
        }

        .use-location-btn:active {
            transform: scale(0.95);
        }

        .alert-banner {
            background: rgba(255, 193, 7, 0.2);
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .weather-main {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .current-weather {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
        }

        .weather-time {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 20px;
        }

        .temp-display {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 20px;
        }

        .temp-icon {
            font-size: 80px;
        }

        .temp-value {
            font-size: 72px;
            font-weight: 300;
        }

        .weather-desc-main {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .feels-like {
            opacity: 0.8;
            font-size: 16px;
        }

        .weather-description {
            font-size: 18px;
            margin-top: 20px;
            opacity: 0.9;
        }

        .weather-details {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 30px;
        }

        .detail-item {
            text-align: center;
        }

        .detail-label {
            font-size: 12px;
            opacity: 0.7;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .detail-value {
            font-size: 20px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .map-placeholder {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            opacity: 0.7;
        }

        .forecast-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .tab-btn {
            padding: 10px 20px;
            border-radius: 25px;
            border: none;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }

        .tab-btn.active {
            background: #ffc107;
            color: #1e3c72;
            font-weight: bold;
        }

        .forecast-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 15px;
        }

        .forecast-day {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
        }

        .forecast-day:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-5px);
        }

        .day-name {
            font-size: 14px;
            margin-bottom: 10px;
            opacity: 0.8;
        }

        .day-date {
            font-size: 12px;
            opacity: 0.6;
            margin-bottom: 15px;
        }

        .forecast-icon {
            font-size: 40px;
            margin: 15px 0;
        }

        .forecast-temp {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }

        .temp-range {
            font-size: 14px;
            opacity: 0.7;
        }

        @media (max-width: 1024px) {
            .weather-main {
                grid-template-columns: 1fr;
            }
            
            .forecast-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .forecast-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .search-container {
                width: 100%;
            }
            
            header {
                flex-direction: column;
                gap: 20px;
            }
        } 

                .menu-pill li a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 20px;
            background: rgba(255,255,255,0.12);
            color: white;
            text-decoration: none;
            transition: 0.25s;
        }

        .menu-pill li a:hover {
            background: rgba(255,255,255,0.22);
        }

        .menu-pill li a.active {
            background: #ffc107;
            color: #1e3c72;
            font-weight: bold;
        }

                .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 80px;
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            color: white;
        }

        .navbar-logo {
            font-size: 18px;
            font-weight: bold;
        }

        .navbar-menu a {
            color: white;
            text-decoration: none;
            margin-left: 30px;
            padding-bottom: 4px;
        }

        .navbar-menu a:hover,
        .navbar-menu a.active {
            border-bottom: 2px solid white;
        }
        
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <i class="fas fa-cloud-sun"></i>
                <span>Cuaca Indonesia</span>
            </div>
            <div class="search-container">
                <input type="text" class="search-box" id="searchInput" placeholder="Cari kota atau lokasi..." autocomplete="off">
                <button class="search-btn" id="searchBtn"><i class="fas fa-search"></i></button>
                <div id="searchResults"></div>
            </div>
        </header>

        <div class="location-header">
            <i class="fas fa-map-marker-alt"></i>
            <span><?php echo htmlspecialchars($locationName); ?></span>
            <button id="useMyLocationBtn" class="use-location-btn" title="Gunakan lokasi saya">
                <i class="fas fa-crosshairs"></i>
            </button>
        </div>

        <?php if ($precipitation > 0): ?>
        <div class="alert-banner">
            <i class="fas fa-bolt" style="font-size: 24px; color: #ffc107;"></i>
            <div>
                <strong>⚡ Peringatan Cuaca</strong>
                <p>Ada kemungkinan hujan di lokasi Anda. Siapkan payung!</p>
            </div>
        </div>
        <?php endif; ?>

        <div class="weather-main">
            <div class="current-weather">
                <div class="weather-time">
                    Cuaca saat ini<br>
                    <?php echo date('H:i'); ?>
                </div>
                
                <div class="temp-display">
                    <div class="temp-icon"><?php echo $weatherInfo['icon']; ?></div>
                    <div class="temp-value"><?php echo $currentTemp; ?>°C</div>
                </div>

                <div class="weather-desc-main"><?php echo $weatherInfo['desc']; ?></div>
                <div class="feels-like">Terasa seperti <?php echo $feelsLike; ?>°</div>

                <div class="weather-description">
                    <?php 
                    if ($currentTemp >= 30) {
                        echo "Cuaca cukup panas hari ini. Tetap terhidrasi dan gunakan pelindung matahari.";
                    } else if ($currentTemp >= 25) {
                        echo "Cuaca hangat dan nyaman. Hari yang bagus untuk beraktivitas.";
                    } else if ($currentTemp >= 20) {
                        echo "Cuaca sejuk dan menyenangkan. Sempurna untuk aktivitas outdoor.";
                    } else {
                        echo "Cuaca agak dingin. Pertimbangkan untuk memakai jaket.";
                    }
                    ?>
                </div>

                <div class="weather-details">
                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="fas fa-wind"></i> Angin
                        </div>
                        <div class="detail-value">
                            <?php echo $windSpeed; ?> km/j
                            <i class="fas fa-arrow-up" style="transform: rotate(<?php echo $weatherData['current']['wind_direction_10m']; ?>deg);"></i>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="fas fa-tint"></i> Kelembapan
                        </div>
                        <div class="detail-value"><?php echo $humidity; ?>%</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="fas fa-eye"></i> Jarak pandang
                        </div>
                        <div class="detail-value"><?php echo $cloudCover < 50 ? '10+' : '5'; ?> km</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="fas fa-umbrella"></i> Curah hujan
                        </div>
                        <div class="detail-value"><?php echo $precipitation; ?> mm</div>
                    </div>
                </div>
            </div>

            <div id="map"></div>
                <div>
                    
                </div>
            </div>
        </div>

        <div class="forecast-tabs">
            <button class="tab-btn active">Gambaran umum</button>
        </div>

        <div class="forecast-grid">
            <?php foreach ($dailyData as $index => $day): 
                $timestamp = strtotime($day['date']);
                $dayName = $index === 0 ? 'Hari ini' : $hariIndo[date('w', $timestamp)];
                $dateFormatted = date('d M', $timestamp);
                $dayWeather = getWeatherDescription($day['code']);
            ?>
            <div class="forecast-day">
                <div class="day-name"><?php echo $dayName; ?></div>
                <div class="day-date"><?php echo $dateFormatted; ?></div>
                <div class="forecast-icon"><?php echo $dayWeather['icon']; ?></div>
                <div class="forecast-temp"><?php echo $day['max']; ?>°</div>
                <div class="temp-range"><?php echo $day['min']; ?>° - <?php echo $day['max']; ?>°</div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        const searchBtn = document.getElementById('searchBtn');
        let searchTimeout;

        // Real-time search saat mengetik
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                searchResults.classList.remove('show');
                searchResults.innerHTML = '';
                return;
            }
            
            // Debounce: tunggu 600ms setelah user berhenti mengetik
            searchTimeout = setTimeout(() => {
                performSearch(query);
            }, 600);
        });

        // Search ketika klik tombol
        searchBtn.addEventListener('click', function() {
            const query = searchInput.value.trim();
            if (query.length >= 2) {
                performSearch(query);
            } else {
                alert('Masukkan minimal 2 karakter untuk mencari');
            }
        });

        // Search ketika tekan Enter
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = this.value.trim();
                if (query.length >= 2) {
                    performSearch(query);
                } else {
                    alert('Masukkan minimal 2 karakter untuk mencari');
                }
            }
        });

        // Fungsi untuk melakukan pencarian
        function performSearch(query) {
            searchResults.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i><br>Mencari lokasi...</div>';
            searchResults.classList.add('show');
            
            // Gunakan Fetch API untuk memanggil api_search.php
            fetch('api_search.php?q=' + encodeURIComponent(query))
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Search results:', data); // Debug
                    
                    if (data.error) {
                        searchResults.innerHTML = '<div class="no-results"><i class="fas fa-exclamation-triangle"></i><br>' + data.error + '</div>';
                        return;
                    }
                    
                    if (!data.results || data.results.length === 0) {
                        searchResults.innerHTML = '<div class="no-results"><i class="fas fa-search"></i><br>Tidak ada hasil ditemukan untuk "' + query + '"</div>';
                        return;
                    }
                    
                    // Tampilkan hasil pencarian
                    displaySearchResults(data.results);
                })
                .catch(error => {
                    console.error('Search error:', error);
                    searchResults.innerHTML = '<div class="no-results"><i class="fas fa-times-circle"></i><br>Terjadi kesalahan. Coba lagi.</div>';
                });
        }

        // Fungsi untuk menampilkan hasil pencarian
        function displaySearchResults(results) {
            let html = '';
            
            results.forEach(result => {
                const locationName = escapeHtml(result.name);
                const country = escapeHtml(result.country || '');
                const admin1 = escapeHtml(result.admin1 || '');
                const details = [admin1, country].filter(Boolean).join(', ');
                const fullLocation = locationName + ', ' + country;
                
                html += `
                    <div class="search-result-item" onclick='selectLocation(${result.latitude}, ${result.longitude}, "${fullLocation.replace(/'/g, "\\'")}");'>
                        <div class="result-info">
                            <div class="result-name">${locationName}</div>
                            <div class="result-details">${details}</div>
                        </div>
                        <div class="result-icon">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                `;
            });
            
            searchResults.innerHTML = html;
        }

        // Fungsi untuk memilih lokasi dan redirect
        function selectLocation(lat, lon, location) {
            console.log('Selected:', lat, lon, location); // Debug
            window.location.href = 'index.php?lat=' + lat + '&lon=' + lon + '&location=' + encodeURIComponent(location);
        }

        // Helper function untuk escape HTML
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        // Tutup hasil pencarian jika klik di luar
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-container')) {
                searchResults.classList.remove('show');
            }
        });

        // Buka kembali hasil jika klik di search box dan ada konten
        searchInput.addEventListener('click', function() {
            if (this.value.trim().length >= 2 && searchResults.innerHTML) {
                searchResults.classList.add('show');
            }
        });

        // Animasi tab
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Tombol "Gunakan Lokasi Saya" dengan Browser Geolocation
        const useMyLocationBtn = document.getElementById('useMyLocationBtn');
        
        if (useMyLocationBtn) {
            useMyLocationBtn.addEventListener('click', function() {
                if (!navigator.geolocation) {
                    alert('Browser Anda tidak mendukung geolocation');
                    return;
                }
                
                // Tampilkan loading
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                this.disabled = true;
                
                navigator.geolocation.getCurrentPosition(
                    // Success callback
                    (position) => {
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;
                        
                        // Dapatkan nama lokasi dari koordinat menggunakan reverse geocoding
                        fetch(`https://geocoding-api.open-meteo.com/v1/search?latitude=${lat}&longitude=${lon}&count=1&language=id&format=json`)
                            .then(response => response.json())
                            .then(data => {
                                let locationName = 'Lokasi Saya';
                                if (data.results && data.results.length > 0) {
                                    const result = data.results[0];
                                    locationName = result.name + ', ' + result.country;
                                }
                                
                                // Redirect ke lokasi baru
                                window.location.href = `index.php?lat=${lat}&lon=${lon}&location=${encodeURIComponent(locationName)}`;
                            })
                            .catch(() => {
                                // Jika reverse geocoding gagal, tetap redirect dengan koordinat
                                window.location.href = `index.php?lat=${lat}&lon=${lon}&location=Lokasi%20Saya`;
                            });
                    },
                    // Error callback
                    (error) => {
                        useMyLocationBtn.innerHTML = '<i class="fas fa-crosshairs"></i>';
                        useMyLocationBtn.disabled = false;
                        
                        let errorMsg = 'Tidak dapat mengakses lokasi Anda';
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMsg = 'Izin lokasi ditolak. Aktifkan izin lokasi di browser Anda.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMsg = 'Informasi lokasi tidak tersedia.';
                                break;
                            case error.TIMEOUT:
                                errorMsg = 'Permintaan lokasi timeout.';
                                break;
                        }
                        alert(errorMsg);
                    },
                    // Options
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            });
        }
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Inisialisasi map
    const map = L.map('map').setView(
        [<?= $latitude ?>, <?= $longitude ?>],
        7
    );

    // Tile OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Marker lokasi
    const marker = L.marker([<?= $latitude ?>, <?= $longitude ?>]).addTo(map);

    marker.bindPopup(`
        <strong><?= addslashes($locationName) ?></strong><br>
        Suhu: <?= $currentTemp ?>°C<br>
        <?= addslashes($weatherInfo['desc']) ?>
    `).openPopup();

  // ✅ KLIK MAP → PINDAH HALAMAN TANPA POPUP BROWSER
    map.on('click', function(e) {
        const lat = e.latlng.lat.toFixed(4);
        const lon = e.latlng.lng.toFixed(4);

        window.location.href =
            `index.php?lat=${lat}&lon=${lon}&location=Koordinat ${lat}, ${lon}`;
    });
</script>

</body>
</html>