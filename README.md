# JoeDoldel - Sistem Manajemen Kanal YouTube

JoeDoldel adalah aplikasi berbasis Laravel untuk mengelola kanal YouTube, mensinkronkan video dan komentar, serta menangani pembersihan spam secara otomatis. Sistem ini menyediakan fungsionalitas untuk memantau beberapa kanal YouTube, mensinkronkan video dan komentar mereka, serta menjaga bagian komentar tetap bersih dengan menyaring spam.

## Fitur Utama

- Sinkronisasi Kanal YouTube
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

### Windows

1. Instalasi:

   - Unduh dan pasang [PHP](https://windows.php.net/download/)
   - Pasang [Composer](https://getcomposer.org/download/)
   - Pasang [Node.js](https://nodejs.org/)
   - Pasang [Git](https://git-scm.com/download/windows)

2. Clone repositori:

   ```bash
   git clone [repository-url]
   cd joedoldel
   ```

3. Instalasi dependensi PHP:

   ```bash
   composer install
   ```

4. Instalasi dependensi JavaScript:
   ```bash
   npm install
   ```

### Linux/macOS

1. Instalasi:

   ```bash
   # Ubuntu/Debian
   sudo apt update
   sudo apt install php php-cli php-mbstring php-xml php-sqlite3 nodejs npm

   # macOS (menggunakan Homebrew)
   brew install php composer node
   ```

2. Clone repositori:

   ```bash
   git clone [repository-url]
   cd joedoldel
   ```

3. Instalasi dependensi PHP:

   ```bash
   composer install
   ```

4. Instalasi dependensi JavaScript:
   ```bash
   npm install
   ```

## Konfigurasi

1. Copy file `.env.example` ke `.env`:

   ```bash
   cp .env.example .env
   ```

2. Generate key aplikasi:

   ```bash
   php artisan key:generate
   ```

3. Konfigurasi database di `.env`:

   ```
   DB_CONNECTION=sqlite
   ```

4. Pengaturan kredensial YouTube API:
   - Dapatkan kredensial dari [Google Cloud Console](https://console.cloud.google.com/)
   - Tambahkan ke berkas `.env`:
     ```
     GOOGLE_CLIENT_ID=your-client-id
     GOOGLE_CLIENT_SECRET=your-client-secret
     GOOGLE_REDIRECT_URI=your-redirect-uri
     ```

## Pengaturan Database

1. Jalankan migrasi:

   ```bash
   php artisan migrate
   ```

2. (Opsional) Isi database dengan data awal:
   ```bash
   php artisan db:seed
   ```

## Menjalankan Aplikasi

1. Mulai server lokal:

   ```bash
   composer dev
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
