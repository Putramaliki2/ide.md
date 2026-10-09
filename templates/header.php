<?php $base = $base ?? ''; ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'Lost & Found') ?></title>
<style>
*{box-sizing:border-box}
body{margin:0;background:#f4f5f7;color:#222;font:15px/1.5 Arial,Helvetica,sans-serif}
.top{background:#1f3a5f;color:#fff;padding:14px 20px}
.top div{max-width:1000px;margin:auto;display:flex;justify-content:space-between;align-items:baseline;flex-wrap:wrap;gap:6px}
.top b{font-size:20px}
.top a{color:#fff;margin-left:16px;font-size:14px}
.wrap{max-width:1000px;margin:20px auto;padding:0 16px}
.page{display:grid;grid-template-columns:290px 1fr;gap:20px;align-items:start}
.box{background:#fff;border:1px solid #d5d9df;border-radius:3px;padding:14px}
h2{margin:0 0 6px;font-size:17px}
label{display:block;margin-top:10px;font-size:13px;color:#444}
input,select,textarea{display:block;width:100%;margin-top:3px;padding:7px 8px;font:inherit;border:1px solid #b9c0ca;border-radius:3px}
input[type=radio]{display:inline;width:auto;margin-right:4px}
.radio label{display:inline-block;margin-right:14px;color:#222;font-size:14px}
button,.btn{font:inherit;padding:7px 12px;border:1px solid #b9c0ca;background:#f4f5f7;border-radius:3px;cursor:pointer;color:#222;text-decoration:none}
button.main{background:#1f3a5f;border-color:#1f3a5f;color:#fff;margin-top:12px}
button.del{color:#b42318}
.flash{background:#e7f4ec;border:1px solid #9ccfb0;padding:9px 12px;border-radius:3px;margin:0 0 14px}
.err{background:#fdecea;border:1px solid #f0aaa4;color:#8a1c14;padding:9px 12px;border-radius:3px;margin:10px 0 0;font-size:13px}
.err p{margin:0}
.tabs{display:flex;flex-wrap:wrap;border-bottom:1px solid #d5d9df;margin-top:10px}
.tabs a{padding:8px 12px;color:#222;text-decoration:none;border-bottom:3px solid transparent;margin-bottom:-1px}
.tabs a.on{border-bottom-color:#1f3a5f;font-weight:bold}
ul{list-style:none;margin:0;padding:0}
.row{padding:14px 0;border-bottom:1px solid #e6e9ed}
.row:last-child{border-bottom:0}
h3{margin:0 0 4px;font-size:16px}
.st{font-size:13px;font-weight:normal;margin-left:8px}
.hilang .st{color:#b42318}.temuan .st{color:#067647}.kembali .st,.kembali h3{color:#666}
dl{margin:0;font-size:13px;color:#555}
dl div{display:flex;gap:6px}
dt{width:56px;color:#888}
dd{margin:0}
.ct{font-size:13px;color:#1f3a5f;margin:6px 0 0}
details.klaim{margin-top:8px;font-size:14px}
summary{cursor:pointer;color:#1f3a5f}
table{width:100%;border-collapse:collapse;font-size:14px}
th,td{text-align:left;padding:8px;border-bottom:1px solid #e6e9ed;vertical-align:top}
.stat{display:flex;gap:24px;margin-bottom:14px}
.stat b{display:block;font-size:24px}
@media(max-width:800px){.page{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="top"><div>
    <b>Lost &amp; Found</b>
    <nav><a href="<?= $base ?>pengguna/">Pengguna</a><a href="<?= $base ?>admin/">Admin</a></nav>
</div></div>
<div class="wrap">
<?php if ($m = flash()): ?><p class="flash"><?= e($m) ?></p><?php endif; ?>
