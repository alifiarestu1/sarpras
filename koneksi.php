<?php
$koneksi = new mysqli("localhost", "root", "", "sarpras_db");
if ($koneksi->connect_error) die("Koneksi gagal: " . $koneksi->connect_error);
?>
