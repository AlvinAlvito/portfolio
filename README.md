# Avinto Project

Website portofolio dan pemasaran Paris Alvito (Alvin Alvito), dibangun dengan Laravel 12. Aplikasi ini mempertahankan bahasa visual SI Pental dan mengubah produk menjadi portfolio CMS serta pipeline permintaan klien.

## Fitur

- Beranda pemasaran, profil profesional/CV, layanan, testimoni, dan pengalaman.
- Katalog proyek dinamis dengan filter kategori, teknologi, tautan, serta galeri screenshot.
- Form inquiry proyek yang menyimpan kontak, tipe proyek, brief, budget, target, dan preferensi komunikasi.
- Chatbot Groq dengan persona Vinto untuk menjawab pertanyaan tentang Avinto Project.
- Dashboard admin, CRUD proyek multi-gambar, dan manajemen status permintaan klien.

## Menjalankan proyek

```bash
composer install
npm install
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Salin `.env.example` menjadi `.env`, lalu isi koneksi database, kredensial admin, dan `GROQ_API_KEY`.

## Pengujian

```bash
composer test
npm run build
```
