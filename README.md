 Lost & Found

## Menjalankan web dengan login Supabase

Halaman laporan publik berada di `index.html`. Login dan pendaftaran dipisahkan ke `login.html`; pengguna bisa melihat laporan tanpa masuk.

1. Buat project di Supabase.
2. Buka **SQL Editor**, lalu jalankan seluruh isi [`database/supabase-schema.sql`](./database/supabase-schema.sql). Jika SQL versi sebelumnya sudah pernah dijalankan, jalankan kembali skrip terbaru ini agar tampilan publik tersedia.
3. Di **Project Settings → API**, salin Project URL dan publishable key (atau anon key lama) ke [`supabase-config.js`](./supabase-config.js). Key ini memang digunakan browser; jangan pernah menaruh `service_role` key di file web.
4. Pastikan **Authentication → Providers → Email** aktif. Jika konfirmasi email aktif, pengguna perlu mengonfirmasi email sebelum masuk.
5. Jalankan situs melalui server lokal atau hosting HTTPS, lalu buka `index.html`. Contoh server PHP lokal: `php -S localhost:8000`, kemudian kunjungi `http://localhost:8000/index.html`. Jangan membuka halaman sebagai `file://`.
6. Klik **Masuk** atau **Buat akun** untuk membuka halaman login terpisah. Halaman laporan menyediakan tautan untuk kembali tanpa masuk.

Pengunjung dapat melihat dan mencari laporan tanpa masuk. Untuk melaporkan barang atau mengajukan klaim, pengguna perlu masuk atau membuat akun. Data kontak dan pemilik laporan hanya dimuat setelah login; pengunjung umum membaca tampilan publik yang tidak menyertakan data tersebut. Tombol hapus hanya tersedia untuk pemilik laporan. Pengunjung tanpa login melihat daftar diperbarui berkala selama halaman terbuka.

Klaim hanya bisa diajukan oleh pengguna yang masuk terhadap laporan terbuka milik pengguna lain. Klaim yang berhasil menandai laporan sebagai selesai sehingga tidak dapat diklaim ulang. Aturan ini ditegakkan di tampilan dan kebijakan Row Level Security Supabase. Jika skema lama sudah pernah dipasang, jalankan kembali seluruh `database/supabase-schema.sql` untuk memperbarui kebijakan tersebut.

### Unit test aturan bisnis

Lima suite unit test tanpa dependency eksternal tersedia di `tests/unit/`. Buka `tests/unit/index.html` di browser untuk menjalankan semua test. Hasil menampilkan jumlah test lulus dan rincian test yang gagal.

Alamat utama dan folder `pengguna/` diarahkan ke `index.html` agar pengunjung memakai login Supabase. Folder `admin/` masih merupakan halaman PHP/MySQL lama yang terpisah.

Jika memakai halaman PHP/MySQL lama, salin `config/database.example.php` menjadi `config/database.php`, lalu isi kredensial lokal dan password admin. File `config/database.php` tidak dilacak Git.

### 1. Masalah Nyata yang Akan Diselesaikan

Kehilangan barang di tempat umum sering membuat pemilik kesulitan untuk menemukannya kembali. Informasi mengenai barang yang hilang biasanya hanya disebarkan melalui grup chat, media sosial, atau bertanya secara langsung kepada orang di sekitar lokasi kejadian. Sementara itu, orang yang menemukan barang juga sering tidak mengetahui bagaimana cara mengembalikannya kepada pemilik.

Tidak adanya tempat khusus untuk mengumpulkan informasi barang hilang dan ditemukan membuat proses pencarian menjadi kurang efektif. Oleh karena itu, dibutuhkan sebuah platform yang dapat mengumpulkan laporan barang hilang dan barang temuan dalam satu sistem sehingga pengguna dapat mencari informasi dan melakukan proses klaim dengan lebih mudah.

### 2. Profil Target Pengguna

* **Masyarakat/Pengguna**: Orang yang kehilangan barang atau menemukan barang milik orang lain. Mereka membutuhkan platform yang mudah digunakan untuk membuat laporan, mencari barang, serta mengajukan klaim.
* **Admin**: Pihak yang bertugas mengelola laporan yang masuk, memeriksa informasi barang, dan menangani pengajuan klaim dari pengguna.

### 3. Manfaat Aplikasi

* Menjadi tempat terpusat untuk informasi barang hilang dan ditemukan
* Membantu pengguna mencari barang berdasarkan kategori, lokasi, atau tanggal
* Mempermudah seseorang melaporkan barang yang ditemukan
* Membantu pemilik barang mengajukan klaim secara lebih terstruktur
* Mengurangi ketergantungan pada penyebaran informasi melalui grup chat atau media sosial
* Membuat data laporan barang tersimpan secara rapi dan mudah dikelola

### 4. Daftar Fitur Inti

* Sistem login dan register dengan 2 role (User & Admin)
* User dapat membuat laporan barang hilang atau barang ditemukan
* User dapat menambahkan informasi seperti nama barang, deskripsi, kategori, lokasi, tanggal, dan foto
* User dapat melihat daftar barang yang telah dilaporkan
* Fitur pencarian dan filter berdasarkan kategori atau jenis laporan
* User dapat mengajukan klaim terhadap barang yang ditemukan
* Dashboard User untuk melihat laporan dan pengajuan klaim
* Dashboard Admin untuk mengelola laporan dan klaim
* Sistem status laporan dan klaim (pending, verified, approved, rejected, resolved)

### 5. Fitur yang Tidak Dikerjakan

* Sistem chat langsung antar pengguna
* Integrasi Google Maps atau pelacakan lokasi secara real-time
* Sistem pembayaran atau biaya pengembalian barang
* Pencocokan barang menggunakan Artificial Intelligence
* Verifikasi identitas menggunakan KTP
* Integrasi dengan WhatsApp atau platform media sosial
* Sistem rating dan ulasan pengguna
* Notifikasi melalui SMS
* Aplikasi versi mobile Android/iOS
* Sistem pengiriman barang kepada pemilik

### 6. Kriteria Aplikasi Dinyatakan Berhasil

* Pengguna dapat membuat akun dan masuk ke dalam sistem sesuai role
* Pengguna dapat membuat laporan barang hilang maupun barang ditemukan
* Informasi laporan dapat ditampilkan dan dicari melalui aplikasi
* Pengguna dapat mengajukan klaim terhadap barang yang ditemukan
* Admin dapat memeriksa dan mengelola laporan yang dibuat pengguna
* Admin dapat menerima atau menolak pengajuan klaim
* Status laporan dapat berubah sesuai dengan proses yang sedang berlangsung
* Dashboard User dan Admin menampilkan informasi yang sesuai dengan hak akses masing-masing
* Aplikasi dapat digunakan melalui browser dengan tampilan yang responsif
* Seluruh data tersimpan di database dan tetap tersedia setelah halaman dimuat ulang
