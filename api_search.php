<?php
header('Content-Type: application/json');
require_once 'config.php';

if (!isset($_GET['q']) || strlen(trim($_GET['q'])) < 2) {
    echo json_encode([
        'error' => 'Minimal 2 karakter untuk pencarian'
    ]);
    exit;
}

$query = trim($_GET['q']);

// Pakai fungsi dari config.php
$data = searchLocation($query);

if (!$data || !isset($data['results'])) {
    echo json_encode([
        'results' => []
    ]);
    exit;
}

$results = [];

foreach ($data['results'] as $item) {
    $results[] = [
        'name'      => $item['name'] ?? '',
        'latitude'  => $item['latitude'] ?? 0,
        'longitude' => $item['longitude'] ?? 0,
        'country'   => $item['country'] ?? '',
        'admin1'    => $item['admin1'] ?? ''
    ];
}

echo json_encode([
    'results' => $results
]);
