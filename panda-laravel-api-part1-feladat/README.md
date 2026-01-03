# Óriáspandák API - Laravel REST API 1. rész (Read & Delete)

Egy egyszerűsített REST API létrehozása óriáspandák adatainak **lekérdezésére és törlésére** Laravel keretrendszerben. Ez a feladat csak a CRUD műveletek egy részét tartalmazza (Read és Delete).

**Fontos**: Ebben a feladatban **nincs** POST (create) és PUT (update) művelet!

---

## 1. Adatbázis struktúra és Migráció

### 📋 Feladat

Hozzon létre egy **pandas** táblát (ugyanaz mint a FULL CRUD-nál):

| Mező neve | Típus | Megjegyzés |
|-----------|-------|------------|
| id | Egész [AI,PK] | A panda azonosítója |
| name | Szöveg (max 10 karakter) | A panda neve |
| sex | Szöveg (pont 1 karakter) | A panda neme (M vagy F) |
| birth | Dátum (opcionális) | A panda születési dátuma |
| created_at | Dátum/Idő (opcionális) | A rekord létrehozásának pontos ideje |
| updated_at | Dátum/Idő (opcionális) | A rekord módosításának pontos ideje |

### ✅ Megoldás

```bash
php artisan make:migration create_pandas_table
```

**database/migrations/xxxx_create_pandas_table.php**:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pandas', function (Blueprint $table) {
            $table->id();
            $table->string('name', 10);
            $table->char('sex', 1);
            $table->date('birth')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pandas');
    }
};
```

```bash
php artisan migrate
```

---

## 2. Model létrehozása és Strict mode

### 📋 Feladat

Hozza létre a **Panda** modellt korral számítással és állítsa be a strict mode-ot.

### ✅ Megoldás

```bash
php artisan make:model Panda
```

**app/Models/Panda.php**:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Panda extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sex',
        'birth',
    ];

    protected $casts = [
        'birth' => 'date',
    ];

    // Kor számítása
    public function getAgeAttribute()
    {
        if (!$this->birth) {
            return null;
        }
        return $this->birth->diffInYears(Carbon::now());
    }
}
```

**app/Providers/AppServiceProvider.php**:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Model::shouldBeStrict();
    }
}
```

---

## 3. Resource létrehozása

### 📋 Feladat

Hozzon létre egy **PandaResource** osztályt (ugyanaz mint a FULL CRUD-nál).

### ✅ Megoldás

```bash
php artisan make:resource PandaResource
```

**app/Http/Resources/PandaResource.php**:

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PandaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sex' => $this->sex,
            'birth' => $this->birth ? $this->birth->format('Y-m-d') : null,
            'age' => $this->age,
        ];
    }
}
```

---

## 4. Controller létrehozása

### 📋 Feladat

Hozzon létre egy **PandaController**-t **CSAK** listázással, egyedi lekéréssel és törléssel. Rendezés támogatás is kell.

**Figyelem**: **NINCS** store és update metódus!

### ✅ Megoldás

```bash
php artisan make:controller PandaController
```

**app/Http/Controllers/PandaController.php**:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Panda;
use App\Http\Resources\PandaResource;
use Illuminate\Http\Request;

class PandaController extends Controller
{
    public function index(Request $request)
    {
        $query = Panda::query();

        // Rendezés kezelése
        if ($request->has('orderBy') && in_array($request->orderBy, ['name', 'age'])) {
            $order = $request->has('order') && in_array($request->order, ['asc', 'desc']) 
                ? $request->order 
                : 'asc';
            
            // Age esetén birth szerint rendezünk
            $orderByField = $request->orderBy === 'age' ? 'birth' : $request->orderBy;
            
            $query->orderBy($orderByField, $order);
        }

        return PandaResource::collection($query->get());
    }

    public function show(Panda $panda)
    {
        return new PandaResource($panda);
    }

    public function destroy(Panda $panda)
    {
        $panda->delete();
        return response()->json(null, 204);
    }
}
```

---

## 5. Routing beállítása

### 📋 Feladat

Állítsa be az API route-okat **CSAK** a három művelethez:

| Method | URI | Controller | Action | Name |
|--------|-----|------------|--------|------|
| GET | api/pandas | PandaController | index | pandas.index |
| GET | api/pandas/{id} | PandaController | show | pandas.show |
| DELETE | api/pandas/{id} | PandaController | destroy | pandas.destroy |

### ✅ Megoldás

**routes/api.php**:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PandaController;

Route::get('pandas', [PandaController::class, 'index'])->name('pandas.index');
Route::get('pandas/{panda}', [PandaController::class, 'show'])->name('pandas.show');
Route::delete('pandas/{panda}', [PandaController::class, 'destroy'])->name('pandas.destroy');

// Vagy részleges resource:
// Route::apiResource('pandas', PandaController::class)->only(['index', 'show', 'destroy']);
```

---

## 6. Seeder létrehozása (teszteléshez)

### 📋 Feladat

Hozzon létre egy **PandaSeeder**-t tesztadatokkal.

### ✅ Megoldás

```bash
php artisan make:seeder PandaSeeder
```

**database/seeders/PandaSeeder.php**:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Panda;

class PandaSeeder extends Seeder
{
    public function run(): void
    {
        Panda::create([
            'name' => 'Bao Bao',
            'sex' => 'F',
            'birth' => '2013-08-23',
        ]);

        Panda::create([
            'name' => 'Bei Bei',
            'sex' => 'M',
            'birth' => '2015-08-22',
        ]);

        Panda::create([
            'name' => 'Ming Ming',
            'sex' => 'F',
            'birth' => null,
        ]);
    }
}
```

**database/seeders/DatabaseSeeder.php**:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PandaSeeder::class,
        ]);
    }
}
```

```bash
php artisan db:seed
```

---

## 7. Tesztelés

```bash
# Összes panda listázása
curl http://localhost:8000/api/pandas

# Rendezés név szerint
curl http://localhost:8000/api/pandas?orderBy=name&order=asc

# Rendezés kor szerint
curl http://localhost:8000/api/pandas?orderBy=age&order=desc

# Egy konkrét panda lekérése
curl http://localhost:8000/api/pandas/1

# Panda törlése
curl -X DELETE http://localhost:8000/api/pandas/1
```

---

## Különbségek a FULL CRUD-hoz képest

| Jellemző | 1. rész (jelen feladat) | FULL CRUD |
|----------|-------------------------|-----------|
| Műveletek | Read, Delete | Create, Read, Update, Delete |
| HTTP metódusok | GET, DELETE | GET, POST, PUT, DELETE |
| Validáció | Nem szükséges | Request osztályok |
| Controller metódusok | index, show, destroy | index, show, store, update, destroy |

**Nincs ebben a feladatban:**
- store metódus
- update metódus
- StorePandaRequest
- UpdatePandaRequest
- POST és PUT route-ok
