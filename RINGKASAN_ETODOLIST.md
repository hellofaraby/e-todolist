# Ringkasan Aplikasi E-Todolist

## Gambaran Umum

E-Todolist adalah aplikasi manajemen tugas sederhana berbasis Laravel 11 yang berfungsi untuk mencatat, mencari, memperbarui, menandai status, dan menghapus daftar pekerjaan. Aplikasi ini memakai pola MVC Laravel dengan antarmuka server-rendered menggunakan Blade dan Bootstrap CDN.

## Tujuan Aplikasi

Aplikasi ini dibuat untuk membantu pengguna mengelola daftar tugas harian secara sederhana melalui satu halaman utama. Fokus utamanya adalah CRUD todo yang ringan, mudah dipahami, dan cocok dijadikan proyek latihan atau dasar pengembangan fitur lanjutan.

## Fitur Utama

1. Menampilkan daftar todo pada halaman utama `/`.
2. Menambahkan task baru melalui form input.
3. Melakukan validasi input task:
   - wajib diisi
   - minimal 3 karakter
   - maksimal 25 karakter
4. Mencari task berdasarkan kata kunci.
5. Mengedit isi task yang sudah ada.
6. Mengubah status task menjadi `Selesai` atau `Belum`.
7. Menampilkan task selesai dengan format coret.
8. Menghapus task dengan konfirmasi.
9. Menampilkan notifikasi sukses dan pesan error validasi.
10. Membagi data dengan paginasi 10 item per halaman.

## Alur Penggunaan

1. Pengguna membuka halaman utama aplikasi.
2. Pengguna mengisi form untuk menambahkan task baru.
3. Data dikirim ke controller lalu divalidasi.
4. Jika valid, data disimpan ke database SQLite pada tabel `todo`.
5. Daftar task ditampilkan kembali bersama pesan sukses.
6. Pengguna dapat mencari task melalui kolom pencarian.
7. Pengguna dapat membuka area edit untuk mengubah teks task dan status `is_done`.
8. Pengguna dapat menghapus task yang tidak diperlukan lagi.

## Arsitektur Singkat

Aplikasi mengikuti struktur standar Laravel:

- `routes/web.php`
  Mengatur endpoint web aplikasi.
- `app/Http/Controllers/Todo/TodoController.php`
  Menangani logika utama todo: daftar, tambah, update, dan hapus.
- `app/Models/Todo.php`
  Model Eloquent untuk tabel `todo`.
- `resources/views/todo/app.blade.php`
  Tampilan utama yang memuat form tambah, form pencarian, daftar task, edit inline, dan paginasi.
- `database/migrations/2024_05_05_082648_create_todo_table.php`
  Mendefinisikan struktur tabel utama aplikasi.

## Routing

Route utama yang dipakai aplikasi:

- `GET /`
  Menampilkan daftar todo.
- `POST /`
  Menyimpan todo baru.
- `PUT /{id}`
  Mengupdate task dan status task.
- `DELETE /{id}`
  Menghapus todo.

Ada juga route tambahan:

- `GET /halo`
  Route percobaan sederhana yang menampilkan view `coba.halo`.

## Struktur Data

Tabel utama yang dipakai adalah `todo` dengan kolom:

- `id`
- `task` bertipe string
- `is_done` bertipe boolean, default `false`
- `created_at`
- `updated_at`

Model `Todo` mengizinkan mass assignment untuk field:

- `task`
- `is_done`

## Logika Controller

`TodoController` memiliki tanggung jawab berikut:

- `index()`
  Mengambil data todo. Jika ada parameter `search`, data difilter berdasarkan kolom `task`. Hasil ditampilkan dengan paginasi 10 data.
- `store()`
  Memvalidasi input lalu menyimpan task baru.
- `update()`
  Memvalidasi ulang input, lalu memperbarui task dan status selesai/belum.
- `destroy()`
  Menghapus data berdasarkan `id`.

## Tampilan Antarmuka

Halaman utama memakai Bootstrap 5 dari CDN dengan komponen:

- navbar sederhana
- form tambah task
- alert sukses
- alert error validasi
- form pencarian
- daftar todo berbentuk list group
- tombol edit dan hapus
- form edit inline berbasis collapse
- paginasi bawaan Laravel

Tampilan task yang sudah selesai diberi elemen coret menggunakan tag HTML `del`.

## Teknologi yang Digunakan

- PHP 8.2
- Laravel 11
- Blade Template Engine
- Eloquent ORM
- SQLite
- Bootstrap 5 CDN
- Vite

## Penyimpanan dan Konfigurasi

- Database lokal tersedia di `database/database.sqlite`.
- Dependensi backend dikelola dengan Composer.
- Dependensi frontend dikelola dengan npm.
- Konfigurasi environment menggunakan file `.env`.

## Kelebihan Aplikasi

- Struktur sederhana dan mudah dipelajari.
- Sudah menerapkan pola MVC Laravel.
- Sudah memiliki validasi form.
- Sudah mendukung pencarian dan paginasi.
- Cocok sebagai dasar pengembangan CRUD yang lebih besar.

## Catatan Teknis

- Repository masih memiliki migration `create_todos_table` yang membuat tabel `todos`, tetapi model aktif menggunakan tabel `todo`. Secara implementasi aplikasi saat ini bergantung pada tabel `todo`.
- README menyebut adanya pengembangan fitur seperti tags, tetapi pada kode yang sedang aktif fitur tags belum terlihat di route, controller, model, maupun view utama.
- Folder `tests` masih berisi file contoh bawaan Laravel, jadi pengujian khusus fitur todo belum terlihat.

## Kesimpulan

E-Todolist adalah aplikasi CRUD todo sederhana berbasis Laravel 11 yang memusatkan semua interaksi pada satu halaman. Pengguna dapat menambah, mencari, memperbarui, memberi status selesai, dan menghapus task dengan antarmuka yang ringan. Dari sisi struktur, aplikasi ini sudah cukup rapi untuk dijadikan bahan belajar Laravel atau fondasi untuk pengembangan fitur lanjutan.
