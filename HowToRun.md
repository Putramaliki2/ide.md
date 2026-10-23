# How to Run — Lost & Found

## Persyaratan

- Browser modern dengan JavaScript aktif.
- Project Supabase dengan Authentication dan PostgreSQL.
- Python 3 (server web lokal) atau ekstensi VS Code Live Server.
- Node.js 18+ (hanya untuk unit test).

> **Catatan konfigurasi:** Aplikasi utama menggunakan Supabase dan tidak memuat konfigurasi PHP/MySQL lama. Jika konfigurasi PHP tersebut dipakai untuk modul terpisah, `config/database.example.php` mewajibkan `DB_HOST`, `DB_NAME`, `DB_USER`, dan `DB_PASS` dari environment, serta menolak user database `root`. Simpan kredensial hanya di environment/server secret store; jangan commit `config/database.php` atau password.

## 1. Siapkan Supabase

1. Buat project Supabase, lalu buka **SQL Editor**.
2. Jalankan seluruh isi [`database/supabase-schema.sql`](./database/supabase-schema.sql). Skrip menyiapkan profil/peran, laporan, klaim, fungsi admin, RLS, tampilan publik, dan Realtime. Skrip ini juga memigrasikan laporan lama menjadi terverifikasi.
3. Pada **Authentication → Providers → Email**, aktifkan penyedia Email. Tentukan apakah konfirmasi email diperlukan untuk demo.
4. Pada **Project Settings → API**, salin **Project URL** dan **publishable key** (atau anon key lama) ke [`supabase-config.js`](./supabase-config.js):

   ```js
   window.SUPABASE_CONFIG = {
     url: "https://PROJECT_REF.supabase.co",
     anonKey: "SUPABASE_PUBLISHABLE_OR_ANON_KEY"
   };
   ```

   Publishable/anon key memang digunakan oleh browser. Jangan masukkan `service_role` key atau kredensial admin database ke file frontend.

5. Buat akun untuk demo melalui `login.html?mode=signup`, lalu konfirmasi email jika fitur tersebut aktif.
6. Promosikan akun admin melalui SQL Editor, mengganti alamat email berikut:

   ```sql
   update public.profiles
   set role = 'admin'
   where user_id in (
     select id from auth.users where email = 'admin@example.com'
   );
   ```

   Pastikan hasil query menunjukkan satu baris berubah. Buat juga akun pengguna biasa untuk memperagakan pengiriman laporan dan klaim.

## 2. Jalankan aplikasi

Dari direktori root repository, jalankan:

```powershell
python -m http.server 8000
```

Buka `http://localhost:8000/`. Alternatifnya, buka folder proyek di VS Code dan jalankan **Live Server**. Jangan membuka halaman menggunakan URL `file://`.

Halaman yang tersedia:

- `http://localhost:8000/` — daftar laporan, pencarian, filter, formulir laporan, dan dashboard pengguna.
- `http://localhost:8000/login.html` — masuk/pendaftaran.
- `http://localhost:8000/admin.html` — moderasi laporan dan pengajuan klaim (khusus admin).
- `http://localhost:8000/tests/unit/` — hasil unit test di browser.

## 3. Jalankan unit test

```powershell
node tests/unit/run-node.js
```

Perintah harus berakhir dengan exit code `0` dan seluruh test berstatus `LULUS`. Test browser yang sama dapat dibuka melalui halaman `/tests/unit/`.

## 4. Alur demo end-to-end

Gunakan dua sesi browser (atau satu jendela biasa dan satu jendela privat) agar akun admin dan pengguna tetap terpisah.

1. Masuk sebagai pengguna biasa, buat laporan barang hilang atau temuan, dan pastikan status awal **Menunggu**.
2. Masuk sebagai admin di sesi lain; buka `admin.html` dan verifikasi laporan tersebut.
3. Kembali ke sesi pengguna; pastikan laporan muncul di daftar publik dan cari/filter laporan.
4. Dari akun pengguna lain, ajukan klaim pada laporan terverifikasi.
5. Pada dashboard pengguna, pastikan klaim berstatus **Menunggu**. Pada dashboard pemilik laporan, pastikan detail pemohon hanya tampak bagi pemilik.
6. Sebagai admin, setujui klaim dan pastikan laporan menjadi **Sudah kembali**. Ulangi dengan penolakan bila demo mencakup kedua hasil.
7. Tunjukkan validasi form (misalnya nama kosong), pencarian tanpa hasil, tampilan kosong, serta pesan error/loading bila koneksi Supabase dimatikan sementara.

## Pemecahan masalah

- **Supabase belum dikonfigurasi**: periksa URL dan publishable/anon key di `supabase-config.js`.
- **Tidak dapat masuk/mendaftar**: periksa Email provider, konfirmasi email, dan log Authentication di Supabase.
- **Tabel/policy/function tidak ditemukan**: jalankan ulang seluruh `database/supabase-schema.sql` di SQL Editor.
- **Akses admin ditolak**: periksa `profiles.role` untuk akun tersebut; nilai yang benar adalah `admin`.
- **Tidak muncul laporan baru**: laporan harus disetujui admin sebelum tampil pada daftar publik.
- **Port 8000 digunakan**: pilih port lain, misalnya `python -m http.server 8080`, lalu gunakan URL dengan port tersebut.
