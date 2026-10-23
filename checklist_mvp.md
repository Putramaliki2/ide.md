# Checklist MVP Lost & Found

Status: **25/25 unit test PASSED**. Pemeriksaan HTTP dan browser lokal juga berhasil. Integrasi database/Auth real belum dapat dinyatakan lulus karena project Supabase belum dihubungkan ke konfigurasi aplikasi.

## Fitur MVP

| # | Fitur / kriteria | Status implementasi | Status pengujian |
|---|---|---|---|
| 1 | Pendaftaran dan login pengguna melalui Supabase Auth | Selesai | Belum diuji dengan project Supabase |
| 2 | Peran pengguna dan admin, dengan akses admin ditentukan di database | Selesai | Kebijakan SQL tersedia; verifikasi live menunggu konfigurasi |
| 3 | Buat laporan hilang/temuan dengan kategori, lokasi, tanggal, ciri, kontak, dan foto opsional | Selesai | Validasi inti unit PASSED; simpan DB menunggu Supabase |
| 4 | Validasi input kosong, panjang field, jenis laporan, tanggal kalender, dan batas ukuran foto | Selesai | Unit PASSED |
| 5 | Daftar publik hanya menampilkan laporan terverifikasi, tanpa kontak pribadi | Selesai | SQL view dan RLS disiapkan; belum diuji pada project live |
| 6 | Pencarian laporan dan filter semua/hilang/temuan/sudah kembali | Selesai | Unit PASSED |
| 7 | Dashboard pengguna untuk memantau laporan, klaim yang diajukan, dan klaim yang diterima | Selesai | Integrasi UI selesai; belum diuji terhadap data Supabase |
| 8 | Admin memverifikasi/menolak/menghapus laporan | Selesai | RPC/RLS disiapkan; belum diuji pada project live |
| 9 | Pengguna mengajukan klaim atas laporan terverifikasi milik pengguna lain | Selesai | Validasi aturan inti unit PASSED; insert DB menunggu Supabase |
| 10 | Admin menyetujui/menolak klaim; persetujuan menandai barang telah kembali | Selesai | RPC transaksional dan RLS disiapkan; belum diuji pada project live |
| 11 | Status loading, kosong, gagal jaringan, dan kegagalan operasi | Selesai | Smoke test tanpa konfigurasi menunjukkan pesan konfigurasi; uji jaringan live belum dilakukan |
| 12 | Data tersimpan, dibatasi berdasarkan peran, dan tetap tersedia setelah reload | Skema dan kebijakan disiapkan | Belum diuji dengan Supabase |
| 13 | Tampilan responsif untuk browser desktop dan mobile | Selesai | Pemeriksaan browser desktop; belum dilakukan visual test mobile |

## Hasil pengujian yang dijalankan

| Pengujian | Hasil |
|---|---|
| `node tests/unit/run-node.js` | **PASSED — 25/25** |
| Browser `/tests/unit/index.html` | **PASSED — 25/25** |
| HTTP lokal untuk halaman utama, admin, dan unit test | **PASSED — 200 OK** |
| JavaScript inline `index.html`, `login.html`, `admin.html` | **PASSED — syntax valid** |
| Login, persistensi database, RLS, moderasi admin, klaim end-to-end | **BELUM DIVERIFIKASI** — butuh Supabase URL/key dan akun demo |
| Video demo alur lengkap | **BELUM TERSEDIA** — alur live belum dapat direkam tanpa backend dan akun demo |

Untuk mengulang pengujian, ikuti [HowToRun.md](./HowToRun.md). Setelah Supabase dikonfigurasi dan akun demo tersedia, jalankan semua langkah di bagian **Alur demo end-to-end** dan perbarui status yang belum diverifikasi.
