<?php
include 'koneksi.php';
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="barang_sarpras.csv"');
$output = fopen('php://output', 'w');
fputcsv($output, [
    'ID','Nama Barang','Jenis','Didapatkan','Lokasi','Warna','Tanggal Terima',
    'Kondisi','Jumlah','Satuan','Nomor Urut','Ukuran','Merk','Keterangan','Gambar'
]);
$q = $koneksi->query("SELECT * FROM barang_sarpras");
while ($r = $q->fetch_assoc()) {
    fputcsv($output, [
        $r['id'], $r['nama_barang'], $r['jenis'], $r['didapatkan'], $r['lokasi'],
        $r['warna'], $r['tanggal_terima'], $r['kondisi'], $r['jumlah'], $r['satuan'],
        $r['nomor_urut'], $r['ukuran'], $r['merk'], $r['keterangan'], $r['gambar']
    ]);
}
fclose($output);
exit;
?>
