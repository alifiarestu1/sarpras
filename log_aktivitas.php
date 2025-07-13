<?php
include 'koneksi.php';
$q = $koneksi->query("SELECT * FROM log_aktivitas ORDER BY waktu DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Log Aktivitas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Log Aktivitas CRUD</h2>
    <a href="index.php" class="btn">Kembali</a>
    <table border="1" cellpadding="8" cellspacing="0" style="margin:24px auto; min-width:600px;">
        <tr>
            <th>Aksi</th>
            <th>Keterangan</th>
            <th>Waktu</th>
        </tr>
        <?php while($r = $q->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($r['aksi']) ?></td>
            <td><?= htmlspecialchars($r['keterangan']) ?></td>
            <td><?= $r['waktu'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
