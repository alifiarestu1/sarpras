<?php
include 'koneksi.php';

// MASTER DATA untuk filter, search, dll
function getMaster($koneksi, $table) {
    $data = [];
    $q = $koneksi->query("SELECT * FROM $table ORDER BY nama ASC");
    while ($r = $q->fetch_assoc()) $data[] = $r['nama'];
    return $data;
}
$lokasiList = getMaster($koneksi, 'master_lokasi');
$kondisiList = getMaster($koneksi, 'master_kondisi');

// FILTER SQL
$where = [];
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
if ($keyword) {
    $where[] = "(
        nama_barang LIKE '%$keyword%' OR jenis LIKE '%$keyword%' OR
        didapatkan LIKE '%$keyword%' OR lokasi LIKE '%$keyword%' OR warna LIKE '%$keyword%' OR
        tanggal_terima LIKE '%$keyword%' OR kondisi LIKE '%$keyword%' OR satuan LIKE '%$keyword%' OR
        ukuran LIKE '%$keyword%' OR merk LIKE '%$keyword%' OR keterangan LIKE '%$keyword%' OR
        nomor_urut LIKE '%$keyword%' OR jumlah LIKE '%$keyword%'
    )";
}
if (!empty($_GET['filter_lokasi'])) $where[] = "lokasi='".addslashes($_GET['filter_lokasi'])."'";
if (!empty($_GET['filter_kondisi'])) $where[] = "kondisi='".addslashes($_GET['filter_kondisi'])."'";
$filterSql = $where ? "WHERE ".implode(" AND ", $where) : "";

// Data utama
$result = $koneksi->query("SELECT * FROM barang_sarpras $filterSql ORDER BY id DESC");

// Kode penomoran helper
function getMasterId($koneksi, $table, $value) {
    $q = $koneksi->query("SELECT id FROM $table WHERE nama='$value' LIMIT 1");
    if ($r = $q->fetch_assoc()) return str_pad($r['id'], 3, '0', STR_PAD_LEFT);
    return '000';
}

// Bulk action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['bulk_delete']) && !empty($_POST['bulk'])) {
        $ids = array_map('intval', $_POST['bulk']);
        $idstr = implode(',',$ids);
        $koneksi->query("DELETE FROM barang_sarpras WHERE id IN ($idstr)");
        $koneksi->query("INSERT INTO log_aktivitas (aksi, keterangan) VALUES ('Bulk Delete','Menghapus ".count($ids)." barang')");
        echo "<script>showToast('Data terpilih dihapus!'); window.location='index.php';</script>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>CRUD PENOMORAN ADM SARPRAS</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrious"></script>
</head>
<body>
    <div id="toast" class="toast"></div>
    <!-- Tombol dark mode pakai icon -->
    <button class="toggle-mode" onclick="toggleDarkMode()" id="toggle-dark-btn" aria-label="Toggle Dark Mode" tabindex="0">
        <span id="toggle-dark-icon">🌙</span>
    </button>

    <h2>Data Penomoran Sarpras</h2>

    <!-- FILTER, SEARCH, & BUTTON GROUP -->
    <div class="filter-box">
        <form method="get" class="form" style="width:100%;display:flex;flex-direction:column;align-items:center;gap:20px;">
            <div class="form-row" style="gap:16px; justify-content:center;">
                <input type="text" name="keyword" placeholder="Cari semua field..." value="<?= htmlspecialchars($keyword) ?>">
                <select name="filter_lokasi">
                    <option value="">-- Lokasi --</option>
                    <?php foreach($lokasiList as $lok): ?>
                        <option value="<?= htmlspecialchars($lok) ?>" <?= (isset($_GET['filter_lokasi']) && $_GET['filter_lokasi']==$lok) ? 'selected' : '' ?>><?= htmlspecialchars($lok) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="filter_kondisi">
                    <option value="">-- Kondisi --</option>
                    <?php foreach($kondisiList as $kond): ?>
                        <option value="<?= htmlspecialchars($kond) ?>" <?= (isset($_GET['filter_kondisi']) && $_GET['filter_kondisi']==$kond) ? 'selected' : '' ?>><?= htmlspecialchars($kond) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="button-group" style="display:flex;gap:18px;justify-content:center;flex-wrap:wrap;">
                <button type="submit" class="btn">Cari</button>
                <a href="index.php" class="btn btn-clear">Reset</a>
                <a href="add.php" class="btn">Tambah Data</a>
                <a href="export.php" class="btn" target="_blank">Export CSV</a>
                <a href="export_sql.php" class="btn" target="_blank">Backup SQL</a>
                <a href="log_aktivitas.php" class="btn" target="_blank">Log Aktivitas</a>
            </div>
        </form>
    </div>

    <!-- Checkbox Pilih Semua -->
    <div style="margin-bottom:18px; margin-top: 8px;">
        <label>
            <input type="checkbox" id="checkAll" /> Pilih Semua
        </label>
    </div>

    <!-- LIST DATA -->
    <form method="post" class="bulk-action">
        <div class="container">
        <?php while($row = $result->fetch_assoc()):
            $kodeArr = [];
            $kodeArr[] = getMasterId($koneksi, 'master_nama_barang', $row['nama_barang']);
            $kodeArr[] = getMasterId($koneksi, 'master_jenis', $row['jenis']);
            $kodeArr[] = getMasterId($koneksi, 'master_didapatkan', $row['didapatkan']);
            $kodeArr[] = getMasterId($koneksi, 'master_lokasi', $row['lokasi']);
            $kodeArr[] = getMasterId($koneksi, 'master_warna', $row['warna']);
            $kodeArr[] = $row['tanggal_terima'];
            $kodeArr[] = getMasterId($koneksi, 'master_kondisi', $row['kondisi']);
            $kodeArr[] = str_pad($row['nomor_urut'], 2, '0', STR_PAD_LEFT) . '/' . str_pad($row['jumlah'], 2, '0', STR_PAD_LEFT);
            $kode_barang = implode('.', $kodeArr);
        ?>
            <div class="item print-label">
                <div class="label-header">
                    <input type="checkbox" name="bulk[]" value="<?= $row['id'] ?>" class="label-checkbox" title="Centang untuk print/hapus">
                    <img src="logo_kampus.png" class="logo-print" alt="Logo Kampus">
                    <svg id="barcode<?= $row['id'] ?>" class="barcode"></svg>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="flex:1;">
                        <p class="kode"><?= $kode_barang; ?></p>
                    </div>
                    <canvas id="qrcode<?= $row['id'] ?>" width="38" height="38" style="margin-left:8px"></canvas>
                </div>  
                <div class="label-detail" style="margin-top: 8px;">
                    <b><?= htmlspecialchars($row['nama_barang']) ?></b> &ndash; <?= htmlspecialchars($row['jenis']) ?><br>
                    <span>Lokasi:</span> <?= htmlspecialchars($row['lokasi']) ?><br>
                    <span>Warna:</span> <?= htmlspecialchars($row['warna']) ?>
                </div>
                <div class="jumlah-satuan">
                    Jumlah: <?= (int)$row['jumlah'] ?> <?= htmlspecialchars($row['satuan']) ?>
                </div>
                <div class="card-btns" style="margin-top:16px;">
                    <a href="detail.php?id=<?= $row['id']; ?>" class="btn-detail">Detail</a>
                    <a href="edit.php?id=<?= $row['id']; ?>" class="btn-detail">Edit</a>
                    <a href="delete.php?id=<?= $row['id']; ?>" class="btn-detail" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </div>
                <script>
                try {
                    JsBarcode("#barcode<?= $row['id'] ?>", "<?= $kode_barang ?>", {format: "CODE128", width: 2, height: 35, displayValue: false});
                } catch(e) {}
                try {
                    new QRious({element: document.getElementById("qrcode<?= $row['id'] ?>"), value: "<?= 'https://yourdomain.com/detail.php?id='.$row['id'] ?>", size: 38});
                } catch(e) {}
                </script>
            </div>
        <?php endwhile; ?>
        </div>

        <div class="petunjuk-print">
            <small>Centang label lalu klik <b>Print Label</b></small>
        </div>
        <button onclick="printSelectedLabels()" type="button" class="btn print-btn">Print Label</button>
        <button type="submit" name="bulk_delete" class="btn" onclick="return confirm('Yakin hapus data terpilih?')">Hapus Terpilih</button>
    </form>

    <script>
    function printSelectedLabels() {
        let allCards = document.querySelectorAll('.item, .print-label');
        let selectedIds = [];
        let checkboxes = document.querySelectorAll('input[type="checkbox"][name="bulk[]"]');
        checkboxes.forEach(function(cb) {
            if (cb.checked) selectedIds.push(cb.value);
        });
        if(selectedIds.length == 0) {
            alert("Pilih minimal satu label yang ingin dicetak!"); return;
        }
        allCards.forEach(function(card) {
            let checkbox = card.querySelector('input[type="checkbox"][name="bulk[]"]');
            if(!checkbox || !checkbox.checked) card.classList.add('hide-for-print');
        });
        let style = document.createElement('style');
        style.id = 'print-select-hide';
        style.innerHTML = '@media print {.hide-for-print {display:none !important;}}';
        document.head.appendChild(style);

        window.print();

        setTimeout(function() {
            document.querySelectorAll('.hide-for-print').forEach(function(card){
                card.classList.remove('hide-for-print');
            });
            let style = document.getElementById('print-select-hide');
            if(style) style.parentNode.removeChild(style);
        }, 1000);
    }

    function toggleDarkMode() {
        document.body.classList.toggle('dark-mode');
        document.getElementById('toggle-dark-icon').innerHTML =
            document.body.classList.contains('dark-mode') ? '🌞' : '🌙';
    }
    window.onload = function(){
        document.getElementById('toggle-dark-icon').innerHTML =
            document.body.classList.contains('dark-mode') ? '🌞' : '🌙';
    }
    function showToast(msg, isError=false) {
        var x = document.getElementById("toast");
        x.innerText = msg;
        x.className = "toast show" + (isError ? " error" : "");
        setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
    }

    // Checkbox "Pilih Semua"
    document.getElementById('checkAll').addEventListener('change', function() {
        const checked = this.checked;
        document.querySelectorAll('input[type="checkbox"][name="bulk[]"]').forEach(cb => {
            cb.checked = checked;
        });
    });

    // Checkbox per item update status "Pilih Semua"
    document.querySelectorAll('input[type="checkbox"][name="bulk[]"]').forEach(cb => {
        cb.addEventListener('change', function() {
            const all = document.querySelectorAll('input[type="checkbox"][name="bulk[]"]');
            const checked = document.querySelectorAll('input[type="checkbox"][name="bulk[]"]:checked');
            document.getElementById('checkAll').checked = all.length === checked.length;
        });
    });
    </script>
</body>
</html>
