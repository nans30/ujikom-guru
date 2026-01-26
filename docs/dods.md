Untuk dokumentasi selanjutnya setelah `README.md`, kamu bisa buat struktur dokumentasi modular seperti berikut agar developer lain (atau kamu sendiri) bisa cepat paham:

---

### 🧾 STRUKTUR DOKUMENTASI LANJUTAN UNTUK `core_apk`

**Direkomendasikan dalam direktori `docs/` atau tetap dilampirkan di `README.md` secara bertahap. Berikut saran isi dokumentasinya:**

---

## 📁 Dokumentasi Tambahan

### 1. 🔧 Instalasi Package Tambahan

#### ✅ DataTables Yajra

```bash
composer require yajra/laravel-datatables-oracle:"^10"
```

> Provider dan alias otomatis di Laravel 5.5 ke atas, tidak perlu register manual.

Jika belum publish config:

```bash
php artisan vendor:publish --tag=datatables
```

#### ✅ Spatie Role & Permission

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

Tambahkan `HasRoles` ke model `User`:

```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
}
```

#### ✅ Repository Pattern

Buat direktori:

```bash
mkdir app/Repositories
mkdir app/Repositories/Contracts
```

Contoh interface:

```php
// app/Repositories/Contracts/UserRepositoryInterface.php
namespace App\Repositories\Contracts;

interface UserRepositoryInterface {
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
```

Implementasi:

```php
// app/Repositories/UserRepository.php
namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface {
    public function all() {
        return User::all();
    }
    public function find($id) {
        return User::findOrFail($id);
    }
    public function create(array $data) {
        return User::create($data);
    }
    public function update($id, array $data) {
        $user = $this->find($id);
        $user->update($data);
        return $user;
    }
    public function delete($id) {
        $user = $this->find($id);
        return $user->delete();
    }
}
```

Binding di `AppServiceProvider`:

```php
$this->app->bind(
    \App\Repositories\Contracts\UserRepositoryInterface::class,
    \App\Repositories\UserRepository::class
);
```

### 2. 🧙‍♂️ CRUD Generator Command

#### Buat file `AdminCrudMakeCommand`

```bash
php artisan make:command AdminCrudMakeCommand
```

Isi awal bisa gunakan package seperti [InfyOm Laravel Generator](https://github.com/InfyOmLabs/laravel-generator) atau generator buatan sendiri.

Jika buat sendiri:

```php
php artisan make:model Post -mcr
php artisan make:request StorePostRequest
php artisan make:request UpdatePostRequest
```

Lalu pakai stub/template untuk auto generate view (create.blade.php, edit, index, show), dan inject request + repository.

Contoh command:

```bash
php artisan admin:crud User
```

Akan generate:

* Model + Migration
* Controller (dengan Yajra + Repository)
* Requests (Store, Update)
* Blade view
* Route + Sidebar menu

### 3. 📊 Implementasi Yajra DataTables di Controller

```php
use Yajra\DataTables\Facades\DataTables;

public function index(Request $request)
{
    if ($request->ajax()) {
        $data = User::query();
        return DataTables::of($data)
            ->addColumn('action', function($row){
                return view('admin.users.partials.actions', compact('row'));
            })
            ->make(true);
    }

    return view('admin.users.index');
}
```

Frontend (dengan JS):

```js
$('#datatable').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route("admin.users.index") }}',
    columns: [
        { data: 'name', name: 'name' },
        { data: 'email', name: 'email' },
        { data: 'action', name: 'action', orderable: false, searchable: false }
    ]
});
```

---

## 📂 Struktur Dokumen Rekomendasi

```
docs/
├── install.md           -> step by step clone dan setup
├── datatables.md        -> integrasi yajra
├── permission.md        -> spatie roles
├── repository.md        -> setup interface + implementasi
├── crud-generator.md    -> custom artisan command
├── controller-pattern.md-> contoh controller + request
```

---

Kalau kamu ingin, saya bisa bantu langsung buatkan isi dari tiap file `docs/*.md` tadi atau langsung generate `AdminCrudMakeCommand` juga. Mau lanjut ke mana dulu?
