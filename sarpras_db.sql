
CREATE DATABASE sarpras_db;
USE sarpras_db;

CREATE TABLE barang_sarpras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_barang VARCHAR(255),
    nama_barang VARCHAR(100),
    jenis VARCHAR(100),
    didapatkan VARCHAR(50),
    warna VARCHAR(50),
    lokasi VARCHAR(100),
    tanggal_terima VARCHAR(50),
    kondisi VARCHAR(50),
    jumlah INT,
    nomor_urut INT,
    gambar VARCHAR(255)
);

CREATE TABLE master_nama_barang (id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(100) UNIQUE);
CREATE TABLE master_jenis (id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(100) UNIQUE);
CREATE TABLE master_didapatkan (id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(50) UNIQUE);
CREATE TABLE master_warna (id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(50) UNIQUE);
CREATE TABLE master_lokasi (id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(100) UNIQUE);
CREATE TABLE master_kondisi (id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(50) UNIQUE);
