<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
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
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:16px 30px;
}

.topbar-left{
    display:flex;
    align-items:center;
    gap:20px;
}

.brand{
    font-size:18px;
    font-weight:600;
}

.menu-pill{
    list-style:none;
    display:flex;
    gap:10px;
    padding:0;
    margin:0;
}

.menu-pill li a{
    display:flex;
    align-items:center;
    gap:6px;
    padding:8px 16px;
    border-radius:20px;
    text-decoration:none;
    font-size:14px;
    color:#fff;
    background:rgba(255,255,255,.15);
}

.menu-pill li a.active,
.menu-pill li a:hover{
    background:#ffcc00;
    color:#000;
}

.search-nav{
    position:relative;
}
.search-nav input{
    padding:10px 40px 10px 16px;
    border:none;
    border-radius:25px;
    outline:none;
}
.search-nav i{
    position:absolute;
    right:14px;
    top:50%;
    transform:translateY(-50%);
    color:#555;
}

/* ================= CONTENT ================= */
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
        <div class="brand">☁️ Cuaca Indonesia</div>
        <ul class="menu-pill">
            <li><a href="index.php">Beranda</a></li>
            <li><a href="detail.php">Detail</a></li>
            <li><a href="berita.php">Berita</a></li>
            <li><a href="pengaturan.php">Pengaturan</a></li>
            <li><a href="bantuan.php" class="active">Bantuan</a></li>
        </ul>
    </div>
    <div class="topbar-right">
        <div class="search-nav">
            <input type="text" placeholder="Cari kota atau lokasi...">
            <i class="fas fa-search"></i>
        </div>
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
