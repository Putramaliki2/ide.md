# Lost & Found

Aplikasi web untuk melaporkan barang hilang/temuan, mengajukan klaim, dan memoderasi laporan melalui dashboard admin. Aplikasi utama menggunakan Supabase Auth, PostgreSQL, Row Level Security (RLS), dan Realtime.

- [Petunjuk menjalankan aplikasi](./HowToRun.md)
- [Checklist MVP dan status pengujian](./checklist_mvp.md)
- [Skema database Supabase](./database/supabase-schema.sql)

## Ringkasan alur

1. Pengguna membuat akun atau masuk.
2. Pengguna mengirim laporan; laporan baru menunggu verifikasi admin.
3. Admin memverifikasi atau menolak laporan.
4. Laporan terverifikasi muncul di daftar publik dan dapat dicari/filter.
5. Pengguna lain mengirim klaim; klaim menunggu persetujuan admin.
6. Admin menyetujui atau menolak klaim. Persetujuan menandai laporan sudah kembali.

Laporan publik tidak menampilkan kontak pelapor. Kontak klaim hanya tersedia bagi pemohon, pemilik laporan, dan admin. Akses data juga dibatasi oleh kebijakan RLS Supabase.

## Pengujian

Jalankan unit test dengan `node tests/unit/run-node.js`. Laporan checklist memisahkan hasil unit test dari validasi browser/database yang memerlukan project Supabase terkonfigurasi.

Fitur di luar MVP: chat, peta/pelacakan lokasi, pembayaran, pencocokan AI, verifikasi KTP, notifikasi pihak ketiga, aplikasi native, dan pengiriman barang.
