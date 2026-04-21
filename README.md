# E-Todolist

E-Todolist adalah aplikasi manajemen tugas berbasis Laravel 11 yang dirancang untuk membantu pengguna mengelola pekerjaan harian secara lebih terstruktur. Aplikasi ini mendukung pencatatan tugas, prioritas, tenggat waktu, label, pencarian lanjutan, statistik progres, serta arsip tugas.

## Tentang Aplikasi E-Todolist

Aplikasi ini berbasis Laravel 11. Kode dasar didapat dari Channel Youtube "Programming Di Rumahrafif" dengan beberapa modifikasi seperti penambahan fitur tags dan pengembangan fitur lainnya. Project ini akan terus dikembangkan lebih lanjut dengan beberapa peningkatan tambahan sesuai kebutuhan pembelajaran dan implementasi.

## Free to Copy and About Me

Aplikasi ini bebas digunakan untuk proyek Anda, dengan tetap mencantumkan kredit kepada Programming Di Rumahrafif dan saya. Segala bentuk penyalahgunaan aplikasi di luar tanggung jawab saya.

Saya ingin mendedikasikan apa yang saya pelajari selama menjadi programmer ke dalam akun GitHub saya:

[https://github.com/hellofaraby](https://github.com/hellofaraby)

Di sana, saya akan membuat beberapa aplikasi CRUD sederhana menggunakan tech stack populer seperti React, Laravel, dan lainnya. Harapannya, aplikasi-aplikasi ini dapat membantu teman-teman, terutama mahasiswa, dalam mengerjakan proyek tugas maupun pekerjaan freelance.

Terima kasih sudah berkunjung dan semoga bermanfaat.

## Pratinjau Aplikasi

GitHub dapat menampilkan gambar pada `README.md` selama file gambar disimpan di dalam repository atau menggunakan URL publik.

Jika Anda ingin menampilkan screenshot aplikasi pada README, simpan file gambar ke path berikut:

```text
docs/images/todo-dashboard.png
```

Lalu gunakan sintaks berikut:

```md
![Pratinjau E-Todolist](docs/images/todo-dashboard.png)
```

Saat ini saya sudah menyiapkan struktur README agar bagian pratinjau mudah ditambahkan begitu file gambar tersebut dimasukkan ke repository.

## Fitur Utama

- CRUD tugas: tambah, tampilkan, ubah, hapus, dan arsipkan tugas
- Label many-to-many pada setiap tugas
- Smart input parser untuk membaca:
    - label dengan format `#label`
    - prioritas dengan format `!high`, `!medium`, `!low`
    - waktu seperti `besok jam 9`
- Prioritas tugas: `low`, `medium`, `high`
- Tenggat waktu dan indikator visual status waktu
- Dashboard statistik:
    - total tugas
    - tugas selesai
    - tugas berjalan
    - persentase progres
- Advanced search:
    - kata kunci biasa
    - `#label`
    - `is:done`
    - `is:pending`
    - `priority:high`
- Filter berdasarkan label
- Soft delete dengan fitur arsip dan pemulihan data
- Tampilan modern berbasis Bootstrap 5
- Mode gelap sederhana

## Teknologi yang Digunakan

- PHP 8.2+ atau versi yang sesuai dengan dependency project
- Laravel 11
- SQLite
- Blade Template Engine
- Bootstrap 5
- Bootstrap Icons
- Vite

## Struktur Fitur Utama

Beberapa file penting dalam aplikasi ini:

- `app/Http/Controllers/Todo/TodoController.php`
  Mengelola alur utama todo, pencarian, statistik, arsip, dan relasi label.
- `app/Models/Todo.php`
  Model utama todo dengan soft delete, cast, dan relasi ke label.
- `app/Models/Tag.php`
  Model label dengan relasi many-to-many ke todo.
- `app/Services/TodoParserService.php`
  Menangani parsing input pintar dan parsing query pencarian lanjutan.
- `app/Http/Requests/StoreTodoRequest.php`
  Validasi saat menambahkan tugas.
- `app/Http/Requests/UpdateTodoRequest.php`
  Validasi saat memperbarui tugas.
- `resources/views/todo/app.blade.php`
  Tampilan utama aplikasi.

## Contoh Smart Input

Contoh input:

```text
Bayar listrik besok jam 9 #rumah !high
```

Hasil parsing:

- Tugas: `Bayar listrik`
- Tenggat waktu: `besok jam 9`
- Label: `rumah`
- Prioritas: `high`

Contoh lain:

```text
Meeting client #kerja #urgent !high
```

## Contoh Advanced Search

Pencarian berikut didukung:

```text
#kerja is:pending priority:high
```

Artinya:

- tampilkan tugas dengan label `kerja`
- status belum selesai
- prioritas tinggi

Contoh query lain:

```text
laporan is:done
```

## Instalasi

1. Clone repository

```bash
git clone https://github.com/username/e-todolist.git
cd e-todolist
```

2. Install dependency backend

```bash
composer install
```

3. Install dependency frontend

```bash
npm install
```

4. Salin file environment

```bash
cp .env.example .env
```

Pada Windows, Anda dapat menyalin file secara manual jika perintah `cp` tidak tersedia.

5. Generate application key

```bash
php artisan key:generate
```

6. Pastikan konfigurasi database menggunakan SQLite pada file `.env`

```env
DB_CONNECTION=sqlite
DB_DATABASE=C:/laragon/www/e-todolist/database/database.sqlite
```

7. Jalankan migrasi

```bash
php artisan migrate
```

8. Jalankan seeder jika diperlukan

```bash
php artisan db:seed
```

9. Jalankan server development

```bash
php artisan serve
```

10. Jalankan Vite

```bash
npm run dev
```

## Catatan Database

Aplikasi ini menggunakan SQLite agar proses setup lebih sederhana. Pastikan file berikut tersedia:

```text
database/database.sqlite
```

Jika file belum ada, Anda dapat membuatnya terlebih dahulu.

## Pengujian

Untuk menjalankan pengujian:

```bash
php artisan test
```

Project ini sudah disiapkan untuk pengujian unit dan feature, termasuk pengujian parser, pencarian, serta alur soft delete.

## Status Pengembangan

Fitur yang sudah tersedia saat ini:

- sistem label
- prioritas tugas
- tenggat waktu
- parser input pintar
- pencarian lanjutan
- statistik dashboard
- soft delete
- dark mode sederhana

Pengembangan lanjutan yang dapat ditambahkan:

- autentikasi pengguna
- multi-user workspace
- notifikasi pengingat tugas
- export data
- filter tanggal yang lebih detail

## Lisensi

Project ini dapat digunakan sebagai referensi pembelajaran dan pengembangan lebih lanjut. Jika Anda mendistribusikan ulang atau mengadaptasi project ini, sebaiknya sertakan atribusi yang sesuai terhadap sumber pembelajaran yang digunakan.

## Catatan Tambahan untuk GitHub

Ya, gambar seperti screenshot yang Anda kirim bisa dimasukkan ke `README.md` dan akan tampil dengan baik di GitHub, dengan syarat:

- file gambar disimpan di dalam repository, misalnya `docs/images/todo-dashboard.png`, atau
- gambar diambil dari URL publik yang dapat diakses tanpa autentikasi

Jika Anda ingin, saya bisa lanjut bantu tahap berikutnya:

1. menyiapkan folder `docs/images/`
2. menambahkan blok gambar ke README
3. atau membuat README versi yang lebih profesional lagi dengan badge, daftar isi, dan section screenshot yang lebih menarik
