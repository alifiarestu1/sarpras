<?php
include 'koneksi.php';
$id = intval($_GET['id']);
$q = $koneksi->query("SELECT * FROM barang_sarpras WHERE id=$id");
$row = $q->fetch_assoc();
if ($row['gambar']) @unlink("uploads/" . $row['gambar']);
$koneksi->query("DELETE FROM barang_sarpras WHERE id=$id");
$koneksi->query("INSERT INTO log_aktivitas (aksi, keterangan) VALUES ('Hapus','Menghapus barang $row[nama_barang]')");
echo "<script>window.onload=function(){alert('Data dihapus!'); window.location='index.php';};</script>";
?>
