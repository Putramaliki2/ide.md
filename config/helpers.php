<?php
session_start();

const KATEGORI = ['Dompet dan tas', 'Elektronik', 'Kunci', 'Dokumen dan kartu', 'Pakaian dan aksesori', 'Lainnya'];

function e($v)
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

function redirect($to)
{
    header('Location: ' . $to);
    exit;
}

function flash($msg = null)
{
    if ($msg !== null) {
        $_SESSION['flash'] = $msg;
        return null;
    }
    $m = $_SESSION['flash'] ?? '';
    unset($_SESSION['flash']);
    return $m;
}

function tanggal($d)
{
    return date('d/m/Y', strtotime($d));
}

function kelasStatus($r)
{
    return $r['status'] === 'dikembalikan' ? 'kembali' : $r['jenis'];
}

function labelStatus($r)
{
    $map = ['kembali' => 'Sudah kembali', 'hilang' => 'Hilang', 'temuan' => 'Temuan'];
    return $map[kelasStatus($r)];
}
