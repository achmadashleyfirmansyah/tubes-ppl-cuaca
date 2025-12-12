<?php
// Konfigurasi API Open-Meteo (API Publik - Tidak perlu API Key)
define('API_BASE_URL', 'https://api.open-meteo.com/v1/forecast');
define('GEOCODING_API', 'https://geocoding-api.open-meteo.com/v1/search');

// Konfigurasi default 
define('DEFAULT_LATITUDE', -7.8167); // Kediri, Jawa Timur
define('DEFAULT_LONGITUDE', 112.0167);
define('DEFAULT_LOCATION', 'Kediri, Jawa Timur');
define('TIMEZONE', 'Asia/Jakarta');

// Fungsi untuk mendapatkan lokasi dari IP Otomatidss
function getLocationFromIP() {
    try {
        // Menggunakan API gratis ip-api.com
        $url = 'http://ip-api.com/json/?fields=status,country,regionName,city,lat,lon,timezone';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return null;
        }
        
        $data = json_decode($response, true);
        
        if ($data && isset($data['status']) && $data['status'] === 'success') {
            return [
                'latitude' => $data['lat'],
                'longitude' => $data['lon'],
                'location' => $data['city'] . ', ' . $data['regionName'] . ', ' . $data['country'],
                'timezone' => $data['timezone']
            ];
        }
    } catch (Exception $e) {
        // Jika gagal, return null
        error_log("IP Location Error: " . $e->getMessage());
    }
    
    return null;
}

// Fungsi untuk mendapatkan data cuaca
function getWeatherData($lat, $lon) {
    $url = API_BASE_URL . "?latitude={$lat}&longitude={$lon}&current=temperature_2m,relative_humidity_2m,apparent_temperature,precipitation,weather_code,cloud_cover,wind_speed_10m,wind_direction_10m&hourly=temperature_2m,precipitation_probability,weather_code&daily=weather_code,temperature_2m_max,temperature_2m_min,precipitation_sum,precipitation_probability_max&timezone=" . TIMEZONE;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        error_log("Weather API Error: " . $error);
        return null;
    }
    
    return json_decode($response, true);
}

// Fungsi untuk geocoding (mencari koordinat dari nama kota)
function searchLocation($cityName) {
    $url = GEOCODING_API . "?name=" . urlencode($cityName) . "&count=10&language=id&format=json";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        error_log("Geocoding API Error: " . $error);
        return null;
    }
    
    return json_decode($response, true);
}

// Fungsi untuk mengkonversi kode cuaca ke deskripsi Indonesia
function getWeatherDescription($code) {
    $descriptions = [
        0 => ['desc' => 'Cerah', 'icon' => '☀️'],
        1 => ['desc' => 'Sebagian Cerah', 'icon' => '🌤️'],
        2 => ['desc' => 'Berawan Sebagian', 'icon' => '⛅'],
        3 => ['desc' => 'Berawan', 'icon' => '☁️'],
        45 => ['desc' => 'Berkabut', 'icon' => '🌫️'],
        48 => ['desc' => 'Kabut Beku', 'icon' => '🌫️'],
        51 => ['desc' => 'Gerimis Ringan', 'icon' => '🌦️'],
        53 => ['desc' => 'Gerimis Sedang', 'icon' => '🌦️'],
        55 => ['desc' => 'Gerimis Lebat', 'icon' => '🌧️'],
        61 => ['desc' => 'Hujan Ringan', 'icon' => '🌧️'],
        63 => ['desc' => 'Hujan Sedang', 'icon' => '🌧️'],
        65 => ['desc' => 'Hujan Lebat', 'icon' => '⛈️'],
        71 => ['desc' => 'Salju Ringan', 'icon' => '🌨️'],
        73 => ['desc' => 'Salju Sedang', 'icon' => '🌨️'],
        75 => ['desc' => 'Salju Lebat', 'icon' => '❄️'],
        80 => ['desc' => 'Hujan Ringan', 'icon' => '🌦️'],
        81 => ['desc' => 'Hujan Sedang', 'icon' => '🌧️'],
        82 => ['desc' => 'Hujan Deras', 'icon' => '⛈️'],
        85 => ['desc' => 'Hujan Salju Ringan', 'icon' => '🌨️'],
        86 => ['desc' => 'Hujan Salju Lebat', 'icon' => '❄️'],
        95 => ['desc' => 'Badai Petir', 'icon' => '⛈️'],
        96 => ['desc' => 'Badai dengan Hujan Es', 'icon' => '⛈️'],
        99 => ['desc' => 'Badai Petir Kuat', 'icon' => '⛈️']
    ];
    
    return $descriptions[$code] ?? ['desc' => 'Tidak Diketahui', 'icon' => '🌡️'];
}

// Fungsi untuk mendapatkan arah angin
function getWindDirection($degrees) {
    $directions = ['U', 'TL', 'T', 'TG', 'S', 'BD', 'B', 'BL'];
    $index = round($degrees / 45) % 8;
    return $directions[$index];
}
?>