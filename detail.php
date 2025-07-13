<?php
include 'koneksi.php';
$id = intval($_GET['id']);
$q = $koneksi->query("SELECT * FROM barang_sarpras WHERE id=$id");
$row = $q->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Detail Barang Sarpras</title>
    <link rel="stylesheet" href="style.css">
    <style>
    body {
        margin: 0;
        padding: 0;
        background: var(--bg, #f4f6fa);
        color: #1d263a;
        font-family: 'Inter', Arial, sans-serif;
        min-height: 100vh;
        transition: background .2s, color .2s;
    }
    .detail-wrapper {
        max-width: 820px;
        margin: 40px auto 0 auto;
        background: var(--card, #fff);
        box-shadow: 0 2px 28px rgba(0,0,0,0.07);
        border-radius: 22px;
        padding: 38px 38px 28px 38px;
        display: flex;
        gap: 32px;
        align-items: flex-start;
        justify-content: flex-start;
    }
    .detail-info {
        flex: 1 1 400px;
        display: grid;
        grid-template-columns: 150px 1fr;
        gap: 8px 18px;
        font-size: 1.15rem;
        align-items: center;
    }
    .detail-info .field-label {
        text-align: right;
        color: #5170b4;
        font-weight: 600;
        letter-spacing: 0.01em;
        opacity: 0.97;
        padding-right: 8px;
        font-size: 1.04em;
    }
    .detail-info .field-value {
        color: #1e2535;
        font-weight: 500;
        font-size: 1.08em;
        letter-spacing: 0.01em;
    }
    .detail-img {
        min-width: 200px;
        max-width: 260px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .detail-img img {
        width: 98%;
        max-width: 220px;
        border-radius: 13px;
        box-shadow: 0 1px 9px rgba(70,98,133,0.13);
        background: #f8fafc;
        border: 1px solid #e0e7ef;
    }
    .btn {
        background: #2563eb;
        color: #fff;
        border: none;
        border-radius: 11px;
        padding: 13px 35px;
        font-size: 1.15em;
        font-weight: bold;
        margin: 32px 0 0 38px;
        cursor: pointer;
        text-decoration: none;
        box-shadow: 0 2px 14px rgba(40,98,235,0.11);
        display: inline-block;
        transition: background .16s;
    }
    .btn:hover { background: #1741ac; }
    .toggle-mode {
        cursor: pointer;
        padding: 9px 20px;
        background: #232d3a;
        color: #fff;
        border: none;
        border-radius: 13px;
        position: fixed;
        right: 38px; top: 28px;
        font-size: 1.19rem;
        font-weight: 700;
        z-index: 200;
        box-shadow: 0 2px 10px rgba(0,0,0,0.10);
        border: 2px solid #fff2;
        display: flex;
        align-items: center;
        gap: 9px;
    }
    .toggle-mode:hover { background: #2563eb; color: #fff; }
    h2 {
        font-size: 2.3em;
        color: #3389ee;
        text-align: center;
        margin-top: 34px;
        margin-bottom: 15px;
        font-weight: 900;
        letter-spacing: 0.02em;
    }
    /* Responsive */
    @media (max-width: 950px) {
        .detail-wrapper { flex-direction: column; padding: 20px 6vw; gap: 15px;}
        .detail-img { max-width: 99vw; margin: 0 auto; }
    }
    @media (max-width: 650px) {
        h2 { font-size: 1.28em; }
        .detail-wrapper { padding: 10px 2vw 16px 2vw; border-radius: 9px; }
        .btn { margin-left: 0; font-size: 0.97em;}
        .detail-info { font-size: 1em; grid-template-columns: 115px 1fr;}
        .detail-img img { max-width: 99vw;}
    }
    /* Dark mode style */
    .dark-mode { background: #171a23 !important; color: #e3e9f9; }
    .dark-mode .detail-wrapper, .dark-mode .detail-info { background: #24263d !important; color: #e3e9f9 !important; }
    .dark-mode .detail-info .field-label { color: #6ab6f6 !important;}
    .dark-mode .detail-info .field-value { color: #fff !important;}
    .dark-mode .btn { background: #2563eb; color: #fff;}
    .dark-mode .btn:hover { background: #0e2f7c;}
    .dark-mode .toggle-mode { background: #fff; color: #222;}
    .dark-mode h2 { color: #55a4ff;}
    .dark-mode .detail-img img { background: #191a23; border-color: #333;}
    </style>
</head>
<body>
    <button class="toggle-mode" onclick="toggleDarkMode()" id="toggle-dark-btn" aria-label="Toggle Dark Mode">
        <span id="toggle-dark-icon">🌙</span>
    </button>
    <h2>Detail Barang Sarpras</h2>
    <div class="detail-wrapper">
        <div class="detail-info">
            <div class="field-label">Nama Barang:</div>
            <div class="field-value"><?= htmlspecialchars($row['nama_barang']); ?></div>
            <div class="field-label">Jenis:</div>
            <div class="field-value"><?= htmlspecialchars($row['jenis']); ?></div>
            <div class="field-label">Didapatkan:</div>
            <div class="field-value"><?= htmlspecialchars($row['didapatkan']); ?></div>
            <div class="field-label">Lokasi:</div>
            <div class="field-value"><?= htmlspecialchars($row['lokasi']); ?></div>
            <div class="field-label">Warna:</div>
            <div class="field-value"><?= htmlspecialchars($row['warna']); ?></div>
            <div class="field-label">Tanggal Terima:</div>
            <div class="field-value"><?= htmlspecialchars($row['tanggal_terima']); ?></div>
            <div class="field-label">Kondisi:</div>
            <div class="field-value"><?= htmlspecialchars($row['kondisi']); ?></div>
            <div class="field-label">Jumlah:</div>
            <div class="field-value"><?= (int)$row['jumlah']; ?> <?= htmlspecialchars($row['satuan']); ?></div>
            <div class="field-label">Ukuran:</div>
            <div class="field-value"><?= htmlspecialchars($row['ukuran']); ?></div>
            <div class="field-label">Merk:</div>
            <div class="field-value"><?= htmlspecialchars($row['merk']); ?></div>
            <div class="field-label">Nomor Urut:</div>
            <div class="field-value"><?= (int)$row['nomor_urut']; ?></div>
            <div class="field-label">Keterangan:</div>
            <div class="field-value"><?= nl2br(htmlspecialchars($row['keterangan'])); ?></div>
        </div>
        <div class="detail-img">
            <?php if ($row['gambar']) { ?>
                <img src="uploads/<?= htmlspecialchars($row['gambar']); ?>" alt="Gambar Barang">
            <?php } ?>
        </div>
    </div>
    <a href="index.php" class="btn">Kembali</a>
    <script>
        // Toggle dark mode + simpan preferensi di localStorage
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            if(document.body.classList.contains('dark-mode')){
                localStorage.setItem('dark-mode', '1');
                document.getElementById('toggle-dark-icon').innerHTML = '🌞';
            } else {
                localStorage.removeItem('dark-mode');
                document.getElementById('toggle-dark-icon').innerHTML = '🌙';
            }
        }
        // Auto aktifkan dark mode jika sebelumnya sudah diaktifkan
        if(localStorage.getItem('dark-mode') === '1'){
            document.body.classList.add('dark-mode');
            document.getElementById('toggle-dark-icon').innerHTML = '🌞';
        }
    </script>
</body>
</html>
