<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../config/helpers.php';

$title = 'Admin - Lost & Found';
$base = '../';
$loginError = '';

if (isset($_GET['keluar'])) {
    unset($_SESSION['admin']);
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['password'])) {
        if (hash_equals(ADMIN_PASSWORD, $_POST['password'])) {
            $_SESSION['admin'] = true;
            redirect('index.php');
        }
        $loginError = 'Password salah.';
    } elseif (!empty($_SESSION['admin'])) {
        $id = (int) ($_POST['id'] ?? 0);
        if (($_POST['aksi'] ?? '') === 'hapus') {
            $pdo->prepare('DELETE FROM laporan WHERE id = ?')->execute([$id]);
            flash('Laporan dihapus.');
        } elseif (($_POST['aksi'] ?? '') === 'buka') {
            $pdo->prepare("UPDATE laporan SET status = 'terbuka', nama_pengambil = NULL, kontak_pengambil = NULL WHERE id = ?")->execute([$id]);
            flash('Laporan dibuka kembali.');
        }
        redirect('index.php');
    }
}

include __DIR__ . '/../templates/header.php';

if (empty($_SESSION['admin'])):
?>
<div class="box" style="max-width:340px;margin:auto">
    <h2>Login admin</h2>
    <form method="post">
        <label>Password<input type="password" name="password" autofocus></label>
        <?php if ($loginError): ?><div class="err"><?= e($loginError) ?></div><?php endif; ?>
        <button class="main">Masuk</button>
    </form>
</div>
<?php
    include __DIR__ . '/../templates/footer.php';
    exit;
endif;

$rows = $pdo->query('SELECT * FROM laporan ORDER BY created_at DESC, id DESC')->fetchAll();
$hitung = fn($s) => count(array_filter($rows, fn($r) => kelasStatus($r) === $s));
?>
<div class="box">
    <h2>Kelola laporan <a class="btn" style="float:right" href="?keluar=1">Keluar</a></h2>
    <div class="stat">
        <span><b><?= $hitung('hilang') ?></b>Hilang</span>
        <span><b><?= $hitung('temuan') ?></b>Temuan</span>
        <span><b><?= $hitung('kembali') ?></b>Sudah kembali</span>
    </div>
    <div style="overflow-x:auto">
    <table>
        <tr><th>Barang</th><th>Lokasi</th><th>Tanggal</th><th>Status</th><th>Kontak</th><th>Aksi</th></tr>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><?= e($r['nama_barang']) ?><br><small><?= e($r['kategori']) ?></small></td>
            <td><?= e($r['lokasi']) ?></td>
            <td><?= tanggal($r['tanggal']) ?></td>
            <td><?= labelStatus($r) ?><?php if ($r['status'] === 'dikembalikan'): ?><br><small><?= e($r['nama_pengambil']) ?> (<?= e($r['kontak_pengambil']) ?>)</small><?php endif; ?></td>
            <td><?= e($r['kontak']) ?></td>
            <td>
                <form method="post" style="display:flex;gap:6px" onsubmit="return confirm('Yakin?')">
                    <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                    <?php if ($r['status'] === 'dikembalikan'): ?><button name="aksi" value="buka">Buka lagi</button><?php endif; ?>
                    <button class="del" name="aksi" value="hapus">Hapus</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    </div>
</div>
<?php include __DIR__ . '/../templates/footer.php'; ?>
