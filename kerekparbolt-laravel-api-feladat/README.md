# Kerékpárbolt API - Laravel REST API Feladat

Egy kerékpárbolt számára kell elkészíteni egy REST API-t Laravel keretrendszerben, amely lehetővé teszi kerékpárok és gyártók kezelését.

---

## 1. Előkészületek

### 📋 Feladat

1. Klónozza le a fullstack alapot `kerekparbolt-api-vnev-knev` néven
2. Másolja le a `.env.example` fájlt `.env` néven
3. Módosítsa a `.env` fájlban a `DB_DATABASE` értékét `kerekparbolt`-ra
4. Másolja be a forrásként kapott `openapi.yaml` fájlt a `swagger` mappába
5. Állítsa be, hogy a modelleket szigorú módban kezelje

### ✅ Megoldás

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

## 2. Adatbázis struktúra és Migrációk

### 📋 Feladat

Hozzon létre két táblát: **manufacturers** és **bicycles**. A két tábla között 1:N kapcsolat áll fenn (egy gyártó több kerékpárt is gyárthat).

#### manufacturers tábla

| Mező neve | Típus | Megjegyzés |
|-----------|-------|------------|
| id | Egész [AI,PK] | A gyártó azonosítója |
| name | Szöveg (max 20 karakter) | A gyártó neve |
| website | Szöveg (max 255 karakter, opcionális) | Hivatkozás a gyártó weboldalára |
| created_at | Dátum/Idő (opcionális) | A rekord létrehozásának pontos ideje |
| updated_at | Dátum/Idő (opcionális) | A rekord módosításának pontos ideje |
| deleted_at | Dátum/Idő (opcionális) | A rekord törlésének pontos ideje |

#### bicycles tábla

| Mező neve | Típus | Megjegyzés |
|-----------|-------|------------|
| id | Egész [AI,PK] | A kerékpár azonosítója |
| name | Szöveg (max 80 karakter) | A kerékpár neve |
| wheel_size | Valós | A kerék mérete |
| gears | Egész | A váltó sebesség fokozatainak száma |
| sex | Szöveg (max 10 karakter) | Kiknek tervezték (férfi, női, unisex) |
| type | Szöveg (max 10 karakter) | Kerékpár típusa (MTB, városi, országúti, cross) |
| size | Szöveg (max 10 karakter) | A kerékpár mérete |
| color | Szöveg (max 20 karakter) | A kerékpár színe |
| manufacturer_id | Egész [FK] | A kerékpár gyártójának azonosítója |
| created_at | Dátum/Idő (opcionális) | A rekord létrehozásának pontos ideje |
| updated_at | Dátum/Idő (opcionális) | A rekord módosításának pontos ideje |
| deleted_at | Dátum/Idő (opcionális) | A rekord törlésének pontos ideje |

### ✅ Megoldás

#### Manufacturers migráció létrehozása:

```bash
php artisan make:migration create_manufacturers_table
```

**database/migrations/xxxx_create_manufacturers_table.php**:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manufacturers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20);
            $table->string('website', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manufacturers');
    }
};
```

#### Bicycles migráció létrehozása:

```bash
php artisan make:migration create_bicycles_table
```

**database/migrations/xxxx_create_bicycles_table.php**:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bicycles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->float('wheel_size');
            $table->integer('gears');
            $table->string('sex', 10);
            $table->string('type', 10);
            $table->string('size', 10)->nullable();
            $table->string('color', 20)->nullable();
            $table->foreignId('manufacturer_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bicycles');
    }
};
```

#### Migrációk futtatása:

```bash
php artisan migrate
```

---

## 3. Modellek létrehozása

### 📋 Feladat

Hozza létre a **Manufacturer** és **Bicycle** modelleket, állítsa be a kapcsolatokat és a fillable mezőket.

### ✅ Megoldás

#### Manufacturer model:

```bash
php artisan make:model Manufacturer
```

**app/Models/Manufacturer.php**:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Manufacturer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'website',
    ];

    public function bicycles()
    {
        return $this->hasMany(Bicycle::class);
    }
}
```

#### Bicycle model:

```bash
php artisan make:model Bicycle
```

**app/Models/Bicycle.php**:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bicycle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'wheel_size',
        'gears',
        'sex',
        'type',
        'size',
        'color',
        'manufacturer_id',
    ];

    protected $casts = [
        'wheel_size' => 'float',
        'gears' => 'integer',
    ];

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }
}
```

---

## 4. Validációs szabályok (Request osztályok)

### 📋 Feladat

Hozzon létre Request osztályokat a gyártók és kerékpárok validálására.

**Gyártók validációs szabályai:**
- **name**: kötelező, szöveg, max 20 karakter
- **website**: opcionális, szöveg, max 255 karakter, URL formátum

**Kerékpárok validációs szabályai:**
- **name**: kötelező, szöveg, max 80 karakter
- **wheel_size**: kötelező, valós szám
- **gears**: kötelező, egész szám, 1 és 30 között
- **sex**: kötelező, szöveg, csak "férfi", "női", vagy "unisex"
- **type**: kötelező, szöveg, csak "MTB", "városi", "országúti", vagy "cross"
- **size**: opcionális, szöveg, max 10 karakter
- **color**: opcionális, szöveg, max 20 karakter
- **manufacturer_id**: kötelező, egész szám, létező gyártó ID

### ✅ Megoldás

#### Manufacturer validáció:

```bash
php artisan make:request StoreManufacturerRequest
php artisan make:request UpdateManufacturerRequest
```

**app/Http/Requests/StoreManufacturerRequest.php**:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreManufacturerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:20',
            'website' => 'nullable|string|url|max:255',
        ];
    }
}
```

**app/Http/Requests/UpdateManufacturerRequest.php**: Ugyanaz, mint a Store verzió.

#### Bicycle validáció:

```bash
php artisan make:request StoreBicycleRequest
php artisan make:request UpdateBicycleRequest
```

**app/Http/Requests/StoreBicycleRequest.php**:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBicycleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:80',
            'wheel_size' => 'required|numeric',
            'gears' => 'required|integer|between:1,30',
            'sex' => ['required', 'string', Rule::in(['férfi', 'női', 'unisex'])],
            'type' => ['required', 'string', Rule::in(['MTB', 'városi', 'országúti', 'cross'])],
            'size' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:20',
            'manufacturer_id' => 'required|integer|exists:manufacturers,id',
        ];
    }
}
```

**app/Http/Requests/UpdateBicycleRequest.php**: Ugyanaz, mint a Store verzió.

---

## 5. Controllerek létrehozása

### 📋 Feladat

Hozzon létre két controllert: **ManufacturerController** és **BicycleController**. Implementálja a teljes CRUD funkcionalitást.

**BicycleController funkciók:**
- **index**: Összes kerékpár listázása, szűrés támogatással (sex, type)
- **show**: Egy kerékpár megjelenítése
- **store**: Új kerékpár létrehozása
- **update**: Kerékpár módosítása
- **destroy**: Kerékpár törlése

**ManufacturerController funkciók:**
- **index**: Összes gyártó listázása
- **show**: Egy gyártó megjelenítése
- **store**: Új gyártó létrehozása
- **update**: Gyártó módosítása
- **destroy**: Gyártó törlése

### ✅ Megoldás

#### ManufacturerController:

```bash
php artisan make:controller ManufacturerController --api
```

**app/Http/Controllers/ManufacturerController.php**:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Manufacturer;
use App\Http\Requests\StoreManufacturerRequest;
use App\Http\Requests\UpdateManufacturerRequest;

class ManufacturerController extends Controller
{
    public function index()
    {
        return Manufacturer::all();
    }

    public function show(Manufacturer $manufacturer)
    {
        return $manufacturer;
    }

    public function store(StoreManufacturerRequest $request)
    {
        $manufacturer = Manufacturer::create($request->validated());
        return response()->json($manufacturer, 201);
    }

    public function update(UpdateManufacturerRequest $request, Manufacturer $manufacturer)
    {
        $manufacturer->update($request->validated());
        return $manufacturer;
    }

    public function destroy(Manufacturer $manufacturer)
    {
        $manufacturer->delete();
        return response()->json(null, 204);
    }
}
```

#### BicycleController:

```bash
php artisan make:controller BicycleController --api
```

**app/Http/Controllers/BicycleController.php**:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Bicycle;
use App\Http\Requests\StoreBicycleRequest;
use App\Http\Requests\UpdateBicycleRequest;
use Illuminate\Http\Request;

class BicycleController extends Controller
{
    public function index(Request $request)
    {
        $query = Bicycle::query();

        // Szűrés sex alapján
        if ($request->has('sex') && $request->sex) {
            $query->where('sex', $request->sex);
        }

        // Szűrés type alapján
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        return $query->get();
    }

    public function show(Bicycle $bicycle)
    {
        return $bicycle;
    }

    public function store(StoreBicycleRequest $request)
    {
        $bicycle = Bicycle::create($request->validated());
        return response()->json($bicycle, 201);
    }

    public function update(UpdateBicycleRequest $request, Bicycle $bicycle)
    {
        $bicycle->update($request->validated());
        return $bicycle;
    }

    public function destroy(Bicycle $bicycle)
    {
        $bicycle->delete();
        return response()->json(null, 204);
    }
}
```

---

## 6. Routing beállítása

### 📋 Feladat

Állítsa be az API route-okat a következők szerint:

| Method | URI | Name | Controller | Action |
|--------|-----|------|------------|--------|
| GET | /api/bicycles | bicycles.index | BicycleController | index |
| GET | /api/bicycles/{bicycle} | bicycles.show | BicycleController | show |
| DELETE | /api/bicycles/{bicycle} | bicycles.destroy | BicycleController | destroy |
| POST | /api/bicycles | bicycles.store | BicycleController | store |
| PUT | /api/bicycles/{bicycle} | bicycles.update | BicycleController | update |
| GET | /api/manufacturers | manufacturers.index | ManufacturerController | index |
| GET | /api/manufacturers/{manufacturer} | manufacturers.show | ManufacturerController | show |
| DELETE | /api/manufacturers/{manufacturer} | manufacturers.destroy | ManufacturerController | destroy |
| POST | /api/manufacturers | manufacturers.store | ManufacturerController | store |
| PUT | /api/manufacturers/{manufacturer} | manufacturers.update | ManufacturerController | update |

**Fontos**: Az útvonalak elérésében az azonosító csak szám lehet!

### ✅ Megoldás

**routes/api.php**:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BicycleController;
use App\Http\Controllers\ManufacturerController;

Route::apiResource('bicycles', BicycleController::class)->whereNumber('bicycle');
Route::apiResource('manufacturers', ManufacturerController::class)->whereNumber('manufacturer');
```

---

## 7. Seederek beállítása

### 📋 Feladat

Másolja be a kapott seeder fájlokat, majd biztosítsa, hogy automatikusan lefussanak a DatabaseSeeder lefutásakor a megfelelő sorrendben!

### ✅ Megoldás

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
            ManufacturerSeeder::class,
            BicycleSeeder::class,
        ]);
    }
}
```

```bash
php artisan db:seed
```

---

## 8. Tesztelés

```bash
# Összes kerékpár listázása
curl http://localhost:8000/api/bicycles

# Szűrés férfi kerékpárokra
curl http://localhost:8000/api/bicycles?sex=férfi

# Szűrés MTB típusra
curl http://localhost:8000/api/bicycles?type=MTB

# Szűrés férfi MTB kerékpárokra
curl http://localhost:8000/api/bicycles?sex=férfi&type=MTB

# Egy konkrét kerékpár lekérése
curl http://localhost:8000/api/bicycles/1

# Új gyártó létrehozása
curl -X POST http://localhost:8000/api/manufacturers \
  -H "Content-Type: application/json" \
  -d '{"name":"Trek","website":"https://www.trekbikes.com"}'

# Új kerékpár létrehozása
curl -X POST http://localhost:8000/api/bicycles \
  -H "Content-Type: application/json" \
  -d '{"name":"Trek X-Caliber 9","wheel_size":29,"gears":12,"sex":"unisex","type":"MTB","size":"L","color":"fekete","manufacturer_id":1}'
```

---

## Gyakori hibák és megoldásaik

1. **Foreign key constraint hiba**: Győződj meg róla, hogy a manufacturers tábla létrejött a bicycles előtt
2. **Validation hiba**: Ellenőrizd, hogy a küldött adatok megfelelnek-e a validációs szabályoknak
3. **Route not found**: Győződj meg róla, hogy az `api.php`-ban vannak az útvonalak, nem a `web.php`-ban

## Hasznos parancsok

```bash
# Összes route listázása
php artisan route:list

# Adatbázis újraindítása seederekkel
php artisan migrate:fresh --seed

# Cache törlése
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```
