<?php
session_start();
require_once 'config.php';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<meta charset="UTF-8">
<title>Bantuan | Cuaca Indonesia</title>

<style>
/* ================= BASE ================= */
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:linear-gradient(180deg,#1e3c72,#2a5298);
    color:#fff;
}

/* ================= NAVBAR (SAMA SEPERTI ASLI) ================= */
.topbar{
    width:100%;
    margin-bottom:25px;
}

.topbar-left{
    display:flex;
    align-items:center;
    gap:30px;
}

.brand{
    font-size:22px;
    font-weight:bold;
    display:flex;
    align-items:center;
    gap:8px;
}

.menu-pill{
    list-style:none;
    display:flex;
    gap:10px;
    padding:6px;
    background:rgba(255,255,255,.08);
    border-radius:40px;
}

.menu-pill li a{
    display:flex;
    align-items:center;
    gap:8px;
    padding:10px 18px;
    border-radius:30px;
    text-decoration:none;
    font-size:14px;
    color:#fff;
    transition:.25s;
}

.menu-pill li a:hover{
    background:rgba(255,255,255,.18);
}

.menu-pill li a.active{
    background:#ffc107;
    color:#1e3c72;
    font-weight:bold;
}

/* ================ CONTENT ================= */
.container{
    max-width:1100px;
    margin:30px auto;
    padding:0 20px;
}

h1{
    font-size:28px;
    margin-bottom:8px;
}
.subtitle{
    opacity:.85;
    margin-bottom:30px;
}

.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
    gap:20px;
}

.card{
    background:rgba(255,255,255,.12);
    border-radius:18px;
    padding:22px;
}
.card h3{
    margin-top:0;
}

/* ================= LIVE CHAT ================= */
.chat-btn{
    position:fixed;
    bottom:25px;
    right:25px;
    background:#ffcc00;
    color:#000;
    border:none;
    padding:14px 18px;
    border-radius:50px;
    cursor:pointer;
    font-weight:600;
}

.chat-box{
    position:fixed;
    bottom:90px;
    right:25px;
    width:320px;
    background:#1f3b70;
    border-radius:16px;
    display:none;
    flex-direction:column;
    overflow:hidden;
}

.chat-header{
    background:#ffcc00;
    color:#000;
    padding:12px;
    font-weight:600;
}

.chat-messages{
    padding:12px;
    height:260px;
    overflow-y:auto;
    font-size:14px;
}

.msg{
    margin-bottom:10px;
}
.bot{
    background:rgba(255,255,255,.15);
    padding:8px 12px;
    border-radius:12px;
    display:inline-block;
}
.user{
    background:#ffcc00;
    color:#000;
    padding:8px 12px;
    border-radius:12px;
    display:inline-block;
    float:right;
}

.chat-input{
    display:flex;
    border-top:1px solid rgba(255,255,255,.2);
}
.chat-input input{
    flex:1;
    padding:10px;
    border:none;
    outline:none;
}
.chat-input button{
    background:#ffcc00;
    border:none;
    padding:10px 16px;
    cursor:pointer;
}

/* ================= FOOTER ================= */
.footer{
    text-align:center;
    opacity:.7;
    font-size:13px;
    padding:30px 0 10px;
}
</style>
</head>

<body>

<!-- ================= NAVBAR ================= -->
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

<!-- ================= CONTENT ================= -->
<div class="container">
    <h1>Pusat Bantuan</h1>
    <div class="subtitle">Panduan dan dukungan penggunaan website cuaca</div>

    <div class="grid">
        <div class="card">
            <h3>📍 Lokasi & Pencarian</h3>
            Cari cuaca berdasarkan kota atau gunakan lokasi otomatis.
        </div>
        <div class="card">
            <h3>🌡️ Data Cuaca</h3>
            Menampilkan suhu, kelembapan, angin, hujan, dan jarak pandang.
        </div>
        <div class="card">
            <h3>📊 Grafik Detail</h3>
            Grafik suhu & hujan per jam untuk analisis cuaca.
        </div>
        <div class="card">
            <h3>⚠️ Notifikasi</h3>
            Peringatan dini untuk cuaca ekstrem.
        </div>
    </div>
</div>

<!-- ================= LIVE CHAT ================= -->
<button class="chat-btn" onclick="toggleChat()">💬 Live Chat</button>

<div class="chat-box" id="chatBox">
    <div class="chat-header">Bantuan Cuaca</div>
    <div class="chat-messages" id="messages">
        <div class="msg"><div class="bot">Halo 👋 Ada yang bisa kami bantu?</div></div>
    </div>
    <div class="chat-input">
        <input type="text" id="chatInput" placeholder="Ketik pesan...">
        <button onclick="sendMsg()">Kirim</button>
    </div>
</div>

<div class="footer">
    © <?= date('Y') ?> Cuaca Indonesia
</div>

<script>
function toggleChat(){
    const box=document.getElementById('chatBox');
    box.style.display = box.style.display==='flex'?'none':'flex';
}

function sendMsg(){
    const input=document.getElementById('chatInput');
    if(!input.value) return;

    const msg=document.createElement('div');
    msg.className='msg';
    msg.innerHTML='<div class="user">'+input.value+'</div>';
    document.getElementById('messages').appendChild(msg);

    setTimeout(()=>{
        const bot=document.createElement('div');
        bot.className='msg';
        bot.innerHTML='<div class="bot">Terima kasih, pesan Anda akan segera kami respon.</div>';
        document.getElementById('messages').appendChild(bot);
    },800);

    input.value='';
}
</script>

</body>
</html>
