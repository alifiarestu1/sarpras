<?php
include 'koneksi.php';

$id = intval($_GET['id']);
$q = $koneksi->query("SELECT * FROM barang_sarpras WHERE id=$id");
$row = $q->fetch_assoc();

function getMaster($koneksi, $table) {
    $data = [];
    $q = $koneksi->query("SELECT * FROM $table ORDER BY nama ASC");
    while ($r = $q->fetch_assoc()) $data[] = $r['nama'];
    return $data;
}
$namaList = getMaster($koneksi, 'master_nama_barang');
$jenisList = getMaster($koneksi, 'master_jenis');
$didapatkanList = getMaster($koneksi, 'master_didapatkan');
$warnaList = getMaster($koneksi, 'master_warna');
$lokasiList = getMaster($koneksi, 'master_lokasi');
$kondisiList = getMaster($koneksi, 'master_kondisi');
$satuanList = getMaster($koneksi, 'master_satuan');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    function hybridValue($main, $baru) {
        return $main === '__baru__' ? trim($baru) : trim($main);
    }
    function saveMaster($koneksi, $table, $val) {
        if (!empty($val)) $koneksi->query("INSERT IGNORE INTO $table (nama) VALUES ('".$koneksi->real_escape_string($val)."')");
        return $val;
    }

    $nama_barang = saveMaster($koneksi, 'master_nama_barang', hybridValue($_POST['nama_barang'], $_POST['nama_barang_baru']));
    $jenis = saveMaster($koneksi, 'master_jenis', hybridValue($_POST['jenis'], $_POST['jenis_baru']));
    $didapatkan = saveMaster($koneksi, 'master_didapatkan', hybridValue($_POST['didapatkan'], $_POST['didapatkan_baru']));
    $warna = saveMaster($koneksi, 'master_warna', hybridValue($_POST['warna'], $_POST['warna_baru']));
    $lokasi = saveMaster($koneksi, 'master_lokasi', hybridValue($_POST['lokasi'], $_POST['lokasi_baru']));
    $kondisi = saveMaster($koneksi, 'master_kondisi', hybridValue($_POST['kondisi'], $_POST['kondisi_baru']));
    $satuan = saveMaster($koneksi, 'master_satuan', hybridValue($_POST['satuan'], $_POST['satuan_baru']));

    $tanggal_terima = $_POST['tanggal_terima'];
    $jumlah = $_POST['jumlah'];
    $nomor_urut = $_POST['nomor_urut'];
    $ukuran = $_POST['ukuran'];
    $merk = $_POST['merk'];
    $keterangan = $_POST['keterangan'];

    $gambar = $row['gambar'];
    if (isset($_FILES['gambar']['name']) && $_FILES['gambar']['name']) {
        if ($gambar && file_exists("uploads/$gambar")) @unlink("uploads/$gambar");
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $fileSize = $_FILES['gambar']['size'];
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']) && $fileSize <= 2*1024*1024) {
            $gambar = uniqid() . '_' . preg_replace('/[^a-z0-9\._-]+/i', '_', basename($_FILES['gambar']['name']));
            move_uploaded_file($_FILES['gambar']['tmp_name'], "uploads/" . $gambar);
        }
    }

    $sql = "UPDATE barang_sarpras SET 
        nama_barang='$nama_barang', jenis='$jenis', didapatkan='$didapatkan', lokasi='$lokasi', warna='$warna',
        tanggal_terima='$tanggal_terima', kondisi='$kondisi', jumlah='$jumlah', nomor_urut='$nomor_urut',
        satuan='$satuan', ukuran='$ukuran', merk='$merk', keterangan='$keterangan', gambar='$gambar'
        WHERE id=$id";
    if ($koneksi->query($sql)) {
        $koneksi->query("INSERT INTO log_aktivitas (aksi, keterangan) VALUES ('Edit','Mengedit barang $nama_barang')");
        echo "<script>window.onload=function(){showToast('Data berhasil diupdate!'); setTimeout(function(){window.location='index.php';},1200);};</script>";
    } else {
        echo "<script>window.onload=function(){showToast('Gagal update data!',true);};</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Data Sarpras</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Tambahan drag-drop style jika belum ada */
        .drop-area {
            border: 2px dashed #5ba7ff;
            border-radius: 14px;
            padding: 18px 0 18px 0;
            text-align: center;
            background: #f5f8ff;
            cursor: pointer;
            margin-bottom: 8px;
            transition: border 0.16s, background 0.15s;
        }
        .drop-area.dragover {
            border-color: #2563eb;
            background: #e3f0ff;
        }
        .drop-area #preview img {
            max-width: 110px;
            max-height: 90px;
            margin: 4px 2px;
            border-radius: 9px;
            box-shadow: 0 1px 6px rgba(37,99,235,0.09);
        }
        .form-main { margin-top:32px; }
        @media (max-width: 700px) {
            .form-main { padding: 18px 2vw 24px 2vw;}
        }
    </style>
</head>
<body>
    <div id="toast" class="toast"></div>
    <button class="toggle-mode" onclick="toggleDarkMode()">Toggle Dark Mode</button>
    <h2>Edit Data Sarpras</h2>
    <div class="form-container">
    <form method="post" enctype="multipart/form-data" class="form-main" autocomplete="off">
        <?php
        function hybridField($name, $label, $data, $value) {
            $inputId = $name . "_baru";
            ?>
            <div class="form-group">
                <select name="<?= $name ?>" id="<?= $name ?>" onchange="showInput(this)" required>
                    <option value="">Pilih <?= $label ?></option>
                    <?php foreach($data as $v): ?>
                        <option value="<?= htmlspecialchars($v) ?>"<?= $value==$v?' selected':''; ?>><?= htmlspecialchars($v) ?></option>
                    <?php endforeach; ?>
                    <option value="__baru__">+ Tambah Baru</option>
                </select>
                <label for="<?= $name ?>"><?= $label ?></label>
                <input type="text" name="<?= $name ?>_baru" id="<?= $inputId ?>" placeholder="Tambah <?= $label ?>" style="display:none; margin-top:10px;">
            </div>
            <?php
        }
        hybridField('nama_barang', 'Nama Barang', $namaList, $row['nama_barang']);
        hybridField('jenis', 'Jenis Barang', $jenisList, $row['jenis']);
        hybridField('didapatkan', 'Didapatkan', $didapatkanList, $row['didapatkan']);
        hybridField('lokasi', 'Lokasi', $lokasiList, $row['lokasi']);
        hybridField('warna', 'Warna', $warnaList, $row['warna']);
        ?>
        <div class="form-group">
            <input type="text" name="tanggal_terima" id="tanggal_terima" value="<?= htmlspecialchars($row['tanggal_terima']) ?>" placeholder=" " required maxlength="7" pattern="\d{2}/\d{4}">
            <label for="tanggal_terima">Bulan/Tahun Diterima (MM/YYYY)</label>
        </div>
        <?php
        hybridField('kondisi', 'Kondisi', $kondisiList, $row['kondisi']);
        ?>
        <div class="form-row">
            <div class="form-group" style="width:48%;display:inline-block;">
                <input type="number" name="jumlah" id="jumlah" value="<?= (int)$row['jumlah'] ?>" placeholder=" " required>
                <label for="jumlah">Jumlah</label>
            </div>
            <?php hybridField('satuan', 'Satuan', $satuanList, $row['satuan']); ?>
        </div>
        <div class="form-row">
            <div class="form-group" style="width:48%;display:inline-block;">
                <input type="number" name="nomor_urut" id="nomor_urut" value="<?= (int)$row['nomor_urut'] ?>" placeholder=" " required>
                <label for="nomor_urut">Nomor Urut</label>
            </div>
            <div class="form-group" style="width:48%;display:inline-block;">
                <input type="text" name="ukuran" id="ukuran" value="<?= htmlspecialchars($row['ukuran']) ?>" placeholder=" ">
                <label for="ukuran">Ukuran</label>
            </div>
        </div>
        <div class="form-group">
            <input type="text" name="merk" id="merk" value="<?= htmlspecialchars($row['merk']) ?>" placeholder=" ">
            <label for="merk">Merk</label>
        </div>
        <div class="form-group">
            <textarea name="keterangan" id="keterangan" rows="2" placeholder=" "><?= htmlspecialchars($row['keterangan']) ?></textarea>
            <label for="keterangan">Keterangan</label>
        </div>
        <div class="form-group">
            <label for="gambar">Gambar (bisa drag & drop, preview di bawah, max 2MB)</label>
            <div id="drop-area" class="drop-area">
                <input type="file" name="gambar" id="gambar" accept="image/*" hidden>
                <p style="margin:0;color:#9bb;">Drop file gambar di sini atau klik untuk memilih</p>
                <div id="preview">
                    <?php if ($row['gambar']) { ?>
                        <img src="uploads/<?= htmlspecialchars($row['gambar']) ?>" width="80" style="margin-top:8px;border-radius:8px;">
                    <?php } ?>
                </div>
            </div>
        </div>
        <button type="submit" class="btn">Update</button>
    </form>
    </div>
    <script>
        // Hybrid select fix for ALL fields, id always matches
        function showInput(sel) {
            var inputBaru = document.getElementById(sel.id + "_baru");
            document.querySelectorAll('input[id$="_baru"]').forEach(function(inp){
                if(inp !== inputBaru) { inp.style.display="none"; inp.required=false; }
            });
            if (sel.value === "__baru__") {
                inputBaru.style.display = "block";
                inputBaru.required = true;
                sel.required = false;
                setTimeout(function(){inputBaru.focus();}, 150);
            } else {
                inputBaru.style.display = "none";
                inputBaru.required = false;
                sel.required = true;
            }
        }
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            if(document.body.classList.contains('dark-mode')){
                localStorage.setItem('dark-mode', '1');
            } else {
                localStorage.removeItem('dark-mode');
            }
        }
        if(localStorage.getItem('dark-mode') === '1'){
            document.body.classList.add('dark-mode');
        }
        function showToast(msg, isError=false) {
            var x = document.getElementById("toast");
            x.innerText = msg;
            x.className = "toast show" + (isError ? " error" : "");
            setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
        }
        document.querySelector("form").addEventListener("submit", function(e){
            let valid = true;
            this.querySelectorAll("[required]").forEach(function(fld){
                if(!fld.value) { fld.style.borderColor="#d32f2f"; valid=false; }
                else { fld.style.borderColor="#ccc"; }
            });
            if(!valid) {
                e.preventDefault();
                showToast("Lengkapi data wajib!", true);
            }
        });

        // DRAG & DROP UPLOAD LOGIC
        const dropArea = document.getElementById('drop-area');
        const inputFile = document.getElementById('gambar');
        const preview = document.getElementById('preview');
        dropArea.addEventListener('click', () => inputFile.click());
        dropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropArea.classList.add('dragover');
        });
        dropArea.addEventListener('dragleave', () => dropArea.classList.remove('dragover'));
        dropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropArea.classList.remove('dragover');
            inputFile.files = e.dataTransfer.files;
            showPreview(inputFile.files);
        });
        inputFile.addEventListener('change', () => showPreview(inputFile.files));
        function showPreview(files) {
            preview.innerHTML = '';
            [...files].forEach(file => {
                if (file && file.type.startsWith('image/')) {
                    if (file.size > 2*1024*1024) {
                        showToast("Gambar terlalu besar! Maksimal 2MB", true);
                        inputFile.value = '';
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = e => {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        preview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                } else {
                    showToast("File bukan gambar!", true);
                    inputFile.value = '';
                }
            });
        }

        // AUTO-FORMAT MM/YYYY pada field tanggal_terima
        document.getElementById('tanggal_terima').addEventListener('input', function(e) {
            let val = this.value.replace(/\D/g,'').slice(0,6);
            if(val.length > 2) val = val.slice(0,2)+'/'+val.slice(2,6);
            this.value = val;
        });
    </script>
</body>
</html>
