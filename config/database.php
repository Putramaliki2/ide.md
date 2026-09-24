<?php
const DB_HOST = 'localhost';
const DB_NAME = 'lost_found';
const DB_USER = 'root';
const DB_PASS = '';
const ADMIN_PASSWORD = 'admin123';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $ex) {
    die('Koneksi database gagal. Periksa config/database.php dan pastikan database/schema.sql sudah diimpor.');
}
