<?php
// pengaturan.php
session_start();

/* =============================
   Simpan pengaturan (session)
============================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['unit_suhu']   = $_POST['unit_suhu'] ?? 'celsius';
    $_SESSION['tema']        = $_POST['tema'] ?? 'biru';
    $_SESSION['bahasa']      = $_POST['bahasa'] ?? 'id';
    $_SESSION['auto_lokasi'] = isset($_POST['auto_lokasi']);
    $_SESSION['notifikasi']  = isset($_POST['notifikasi']);
    $saved = true;
}

/* =============================
   Ambil nilai pengaturan
============================= */
$unit   = $_SESSION['unit_suhu'] ?? 'celsius';
$tema   = $_SESSION['tema'] ?? 'biru';
$lang   = $_SESSION['bahasa'] ?? 'id';
$auto   = $_SESSION['auto_lokasi'] ?? true;
$notif  = $_SESSION['notifikasi'] ?? false;
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pengaturan Cuaca</title>

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(180deg,#1e3c72,#2a5298);
    color: #fff;
}

.container {
    max-width: 900px;
    margin: auto;
    padding: 30px;
}

h2 {
    margin-bottom: 10px;
}

.section {
    background: rgba(255,255,255,0.14);
    padding: 22px;
    border-radius: 18px;
    margin-bottom: 25px;
}

.section h3 {
    margin-top: 0;
    font-size: 18px;
}

.row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 15px 0;
    gap: 10px;
}

label {
    font-size: 14px;
}

select, input[type="checkbox"] {
    padding: 6px;
    border-radius: 6px;
    border: none;
}

button {
    width: 100%;
    padding: 12px;
    background: #ffc107;
    color: #1e3c72;
    font-weight: bold;
    border: none;
    border-radius: 12px;
    cursor: pointer;
}

button:hover {
    opacity: 0.9;
}

.success {
    background: rgba(40,167,69,0.85);
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 15px;
    font-size: 14px;
}
</style>
</head>

<body>

<div class="container">

<h2>⚙️ Pengaturan</h2>
<p>Sesuaikan tampilan dan perilaku aplikasi cuaca</p>

<?php if (!empty($saved)): ?>
<div class="success">✅ Pengaturan berhasil disimpan</div>
<?php endif; ?>

<form method="post">

<!-- UNIT CUACA -->
<div class="section">
<h3>Unit Cuaca</h3>
<div class="row">
    <label>Satuan Suhu</label>
    <select name="unit_suhu">
        <option value="celsius" <?= $unit=='celsius'?'selected':'' ?>>Celsius (°C)</option>
        <option value="fahrenheit" <?= $unit=='fahrenheit'?'selected':'' ?>>Fahrenheit (°F)</option>
    </select>
</div>
</div>

<!-- TAMPILAN -->
<div class="section">
<h3>Tampilan</h3>
<div class="row">
    <label>Tema Warna</label>
    <select name="tema">
        <option value="biru" <?= $tema=='biru'?'selected':'' ?>>Biru (Default)</option>
        <option value="gelap">Gelap</option>
        <option value="terang">Terang</option>
    </select>
</div>
</div>

<!-- BAHASA -->
<div class="section">
<h3>Bahasa</h3>
<div class="row">
    <label>Bahasa Aplikasi</label>
    <select name="bahasa">
        <option value="id" <?= $lang=='id'?'selected':'' ?>>Indonesia</option>
        <option value="en">English</option>
    </select>
</div>
</div>

<!-- LOKASI -->
<div class="section">
<h3>Lokasi</h3>
<div class="row">
    <label>Deteksi Lokasi Otomatis</label>
    <input type="checkbox" name="auto_lokasi" <?= $auto?'checked':'' ?>>
</div>
</div>

<!-- NOTIFIKASI -->
<div class="section">
<h3>Notifikasi Cuaca</h3>
<div class="row">
    <label>Peringatan Cuaca Ekstrem</label>
    <input type="checkbox" name="notifikasi" <?= $notif?'checked':'' ?>>
</div>
</div>

<button type="submit">Simpan Pengaturan</button>

</form>

</div>

</body>
</html>
