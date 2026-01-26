Berikut `README.md` langsung dalam bentuk kode:

````markdown
# ⚙️ core_apk — Laravel Starter Template

Template awal proyek Laravel untuk kebutuhan pengembangan aplikasi web. Siap digunakan untuk berbagai jenis proyek (CMS, admin panel, dsb).

---

## 📦 Fitur Default

- Laravel 10+
- Autentikasi (Login & Register)
- Routing dasar
- Layout Auth modern (split layout)
- Bootstrap 5 / Tailwind CSS (bisa ganti)
- Struktur bersih & modular
- Siap dikembangkan lebih lanjut

---

## 🚀 Cara Clone & Setup

### 1. Clone repository
```bash
git clone https://gitlab.mobilus-interactive.com/iesien22/core_apk.git
cd core_apk
````

### 2. Salin file `.env`

```bash
cp .env.example .env
```

### 3. Edit file `.env`

Atur database dan konfigurasi lainnya:

```env
APP_NAME="Core APK"
APP_URL=http://localhost:8000

DB_DATABASE=core_apk
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Install dependency Laravel

```bash
composer install
```

### 5. Generate key aplikasi

```bash
php artisan key:generate
```

### 6. Jalankan migrasi & seeder (jika tersedia)

```bash
php artisan migrate --seed
```

### 7. Install frontend dependencies (jika pakai Vite)

```bash
npm install
npm run dev
```

### 8. Jalankan aplikasi

```bash
php artisan serve
```

Buka: [http://localhost:8000](http://localhost:8000)

---

## 🧾 Akun Default (Jika Seeder Disediakan)

| Email                                         | Password  |
| --------------------------------------------- | --------  |
| [admin@example.com](mailto:admin@example.com) | 123456789 |

---

## 📂 Struktur Folder

| Folder                 | Deskripsi                              |
| ---------------------- | -------------------------------------- |
| `app/Models`           | Eloquent Models                        |
| `app/Http/Controllers` | Controller utama aplikasi              |
| `resources/views`      | Blade templates dan layout             |
| `routes/web.php`       | Routing web                            |
| `public/`              | Public asset folder (gambar, js, dll.) |
| `database/migrations`  | File migrasi database                  |

---

## 🧠 Catatan

Template ini cocok untuk dijadikan base project Laravel sederhana.
Untuk fitur lanjutan seperti:

* Role & Permission (Spatie)
* Upload Gambar (Spatie Media Library)
* Inertia.js / React / Vue
* Fitur API

Silakan tambahkan sesuai kebutuhanmu.

---

## 👨‍💻 Author

Dikembangkan oleh tim internal
Lisensi: MIT

```

Kalau ada hal khusus mau dimasukkan (logo, badge GitHub, dsb), tinggal beri tahu saja.
```
![Preview](https://i.pinimg.com/originals/1c/06/a8/1c06a8c6747ce5a26cb75e7ff908e329.gif)
# laravel-ecourse
