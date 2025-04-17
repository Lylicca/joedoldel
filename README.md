# Joedoldel - Pembasmi Spam Komen YouTube

Joedoldel adalah aplikasi berbasis Laravel untuk mengelola channel YouTube, mensinkronkan video dan komentar, serta menangani pembersihan spam secara otomatis. Sistem ini menyediakan fungsionalitas untuk memantau beberapa channel YouTube, mensinkronkan video dan komentar mereka, serta menjaga bagian komentar tetap bersih dengan menyaring spam judol.

## Fitur Utama

- Sinkronisasi channel YouTube
- Pengambilan konten video otomatis
- Sinkronisasi dan pengelolaan komentar
- Deteksi dan pembersihan spam
- Pembaruan otomatis kredensial Google
- Pembersihan spam dengan probabilitas tinggi
- Pengelolaan kata-kata yang diblokir

## Requirement

- PHP 8.1 atau lebih tinggi
- Composer
- Node.js & NPM
- SQLite/MySQL
- Kredensial API Google/YouTube

## Cara Instalasi

1. Download Laravel Herd [disini](https://herd.laravel.com/)

2. Download/clone repository ini

   ```bash
   git clone https://github.com/Lylicca/joedoldel.git
   ```

3. Buka `cmd` di folder hasil ekstrak

4. Instal dependensi dengan Composer:

  ```bash
  composer install
  composer setup
  ```

## Konfigurasi

1. Pengaturan kredensial YouTube API:
   - Dapatkan kredensial dari [Google Cloud Console](https://console.cloud.google.com/)
   - Tambahkan ke berkas `.env`:
     ```
     GOOGLE_CLIENT_ID=your-client-id
     GOOGLE_CLIENT_SECRET=your-client-secret
     GOOGLE_REDIRECT_URI=your-redirect-uri
     ```

## Menjalankan Aplikasi di `cmd`

```bash
composer start
```

## Cara Berkontribusi

1. Fork repositori
2. Buat branch fitur Anda
3. Commit perubahan Anda
4. Push ke branch
5. Buat Pull Request baru

## Lisensi

[MIT](LICENSE)

## Bantuan

Jika Anda mengalami masalah atau memiliki pertanyaan, silakan bertanya di [Discord](https://discord.gg/Bq7nSHtAM3) kami
