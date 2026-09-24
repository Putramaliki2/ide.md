<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../config/helpers.php';

$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $in = array_map(fn($v) => is_string($v) ? trim($v) : '', $_POST);
    $aksi = $in['aksi'] ?? '';

    if ($aksi === 'tambah') {
        $old = $in;
        if (($in['nama_barang'] ?? '') === '') $errors[] = 'Nama barang wajib diisi.';
        if (($in['lokasi'] ?? '') === '') $errors[] = 'Lokasi wajib diisi.';
        if (($in['kontak'] ?? '') === '') $errors[] = 'Kontak wajib diisi.';
        if (!strtotime($in['tanggal'] ?? '')) $errors[] = 'Tanggal tidak valid.';
        if (!$errors) {
            $jenis = ($in['jenis'] ?? '') === 'temuan' ? 'temuan' : 'hilang';
            $kategori = in_array($in['kategori'] ?? '', KATEGORI, true) ? $in['kategori'] : 'Lainnya';
            $pdo->prepare('INSERT INTO laporan (jenis, nama_barang, kategori, lokasi, tanggal, deskripsi, kontak) VALUES (?,?,?,?,?,?,?)')
                ->execute([$jenis, $in['nama_barang'], $kategori, $in['lokasi'], date('Y-m-d', strtotime($in['tanggal'])), $in['deskripsi'] ?? '', $in['kontak']]);
            flash('Laporan berhasil disimpan.');
            redirect('index.php');
        }
    }

    if ($aksi === 'klaim') {
        if (($in['who'] ?? '') === '' || ($in['kontak'] ?? '') === '') {
            flash('Nama dan kontak wajib diisi.');
        } else {
            $pdo->prepare("UPDATE laporan SET status = 'dikembalikan', nama_pengambil = ?, kontak_pengambil = ? WHERE id = ? AND status = 'terbuka'")
                ->execute([$in['who'], $in['kontak'], (int) ($in['id'] ?? 0)]);
            flash('Laporan ditandai sudah kembali.');
        }
        redirect('index.php');
    }
}

$q = trim($_GET['q'] ?? '');
$f = $_GET['f'] ?? 'semua';
$where = [];
$params = [];
if ($q !== '') {
    $where[] = '(nama_barang LIKE ? OR lokasi LIKE ? OR deskripsi LIKE ?)';
    array_push($params, "%$q%", "%$q%", "%$q%");
}
if ($f === 'dikembalikan') {
    $where[] = "status = 'dikembalikan'";
} elseif ($f === 'hilang' || $f === 'temuan') {
    $where[] = "status = 'terbuka' AND jenis = " . $pdo->quote($f);
}
$sql = 'SELECT * FROM laporan' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY tanggal DESC, id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$tabs = ['semua' => 'Semua', 'hilang' => 'Hilang', 'temuan' => 'Temuan', 'dikembalikan' => 'Sudah kembali'];
$title = 'Lost & Found';
$base = '../';
include __DIR__ . '/../templates/header.php';
?>
<div class="page">
<aside class="box">
    <h2>Buat laporan baru</h2>
    <form method="post">
        <input type="hidden" name="aksi" value="tambah">
        <div class="radio">
            <label><input type="radio" name="jenis" value="hilang" <?= ($old['jenis'] ?? 'hilang') === 'hilang' ? 'checked' : '' ?>>Barang hilang</label>
            <label><input type="radio" name="jenis" value="temuan" <?= ($old['jenis'] ?? '') === 'temuan' ? 'checked' : '' ?>>Barang temuan</label>
        </div>
        <label>Nama barang<input name="nama_barang" maxlength="80" value="<?= e($old['nama_barang'] ?? '') ?>"></label>
        <label>Kategori
            <select name="kategori">
                <?php foreach (KATEGORI as $k): ?>
                    <option <?= ($old['kategori'] ?? '') === $k ? 'selected' : '' ?>><?= e($k) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Lokasi<input name="lokasi" maxlength="100" value="<?= e($old['lokasi'] ?? '') ?>"></label>
        <label>Tanggal<input type="date" name="tanggal" value="<?= e($old['tanggal'] ?? date('Y-m-d')) ?>"></label>
        <label>Ciri-ciri<textarea name="deskripsi" rows="3" maxlength="300"><?= e($old['deskripsi'] ?? '') ?></textarea></label>
        <label>Kontak (WA atau email)<input name="kontak" maxlength="80" value="<?= e($old['kontak'] ?? '') ?>"></label>
        <?php if ($errors): ?><div class="err"><?php foreach ($errors as $er): ?><p><?= e($er) ?></p><?php endforeach; ?></div><?php endif; ?>
        <button class="main">Simpan laporan</button>
    </form>
</aside>

<section class="box">
    <form method="get">
        <input type="hidden" name="f" value="<?= e($f) ?>">
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Cari barang atau lokasi">
    </form>
    <div class="tabs">
        <?php foreach ($tabs as $k => $t): ?>
            <a class="<?= $f === $k ? 'on' : '' ?>" href="?f=<?= $k ?>&q=<?= urlencode($q) ?>"><?= $t ?></a>
        <?php endforeach; ?>
    </div>
    <ul>
    <?php if (!$rows): ?><li class="row">Belum ada laporan yang cocok.</li><?php endif; ?>
    <?php foreach ($rows as $r): ?>
        <li class="row <?= kelasStatus($r) ?>">
            <h3><?= e($r['nama_barang']) ?><span class="st"><?= labelStatus($r) ?></span></h3>
            <dl>
                <div><dt>Lokasi</dt><dd><?= e($r['lokasi']) ?></dd></div>
                <div><dt>Tanggal</dt><dd><?= tanggal($r['tanggal']) ?></dd></div>
                <div><dt>Jenis</dt><dd><?= e($r['kategori']) ?></dd></div>
            </dl>
            <?php if ($r['deskripsi'] !== ''): ?><p style="margin:6px 0 0"><?= e($r['deskripsi']) ?></p><?php endif; ?>
            <?php if ($r['status'] === 'terbuka'): ?>
                <p class="ct">Kontak: <?= e($r['kontak']) ?></p>
                <details class="klaim">
                    <summary><?= $r['jenis'] === 'hilang' ? 'Saya menemukannya' : 'Ini punya saya' ?></summary>
                    <form method="post">
                        <input type="hidden" name="aksi" value="klaim">
                        <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                        <label>Nama<input name="who" maxlength="60"></label>
                        <label>Kontak<input name="kontak" maxlength="80"></label>
                        <button class="main">Simpan</button>
                    </form>
                </details>
            <?php else: ?>
                <p class="ct">Dikembalikan ke/oleh <?= e($r['nama_pengambil']) ?> (<?= e($r['kontak_pengambil']) ?>)</p>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
    </ul>
</section>
</div>
<?php include __DIR__ . '/../templates/footer.php'; ?>
