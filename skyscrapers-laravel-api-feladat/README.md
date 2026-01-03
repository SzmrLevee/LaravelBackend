# Felhőkarcolók API - Laravel REST API Feladat

Egy teljes CRUD REST API létrehozása felhőkarcolók és városok adatainak kezelésére Laravel keretrendszerben. A két entitás között **1:N kapcsolat** van (egy városban több felhőkarcoló is állhat).

**Fontos**: Egyik tábla sem tárolja az időbélyegeket (timestamps)!

---

## 1. Előkészületek

### 📋 Feladat

Állítsa be a strict mode-ot a modellekhez.

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

Hozzon létre két táblát 1:N kapcsolattal. **Fontos**: Egyik tábla sem tárolja az időbélyegeket!

#### cities tábla

| Mező neve | Típus | Megjegyzés |
|-----------|-------|------------|
| id | Egész [AI,PK] | A város azonosítója |
| country_code | Szöveg (2 karakter) | A város országának a kétbetűs kódja |
| name | Szöveg (max 25 karakter) | A város neve |

#### skyscrapers tábla

| Mező neve | Típus | Megjegyzés |
|-----------|-------|------------|
| id | Egész [AI,PK] | A felhőkarcoló azonosítója |
| name | Szöveg (max 50 karakter) | A felhőkarcoló neve |
| city_id | Egész [FK] | A felhőkarcoló városának azonosítója |
| height | Valós | A felhőkarcoló magassága |
| stories | Egész (opcionális) | A felhőkarcoló emeleteinek száma |
| finished | Egész (opcionális) | A felhőkarcoló befejezésének éve |

### ✅ Megoldás

#### Cities migráció:

```bash
php artisan make:migration create_cities_table
```

**database/migrations/xxxx_create_cities_table.php**:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->char('country_code', 2);
            $table->string('name', 25);
            // Nincs timestamps!
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
```

#### Skyscrapers migráció:

```bash
php artisan make:migration create_skyscrapers_table
```

**database/migrations/xxxx_create_skyscrapers_table.php**:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skyscrapers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->float('height');
            $table->integer('stories')->nullable();
            $table->integer('finished')->nullable();
            // Nincs timestamps!
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skyscrapers');
    }
};
```

```bash
php artisan migrate
```

---

## 3. Modellek létrehozása

### 📋 Feladat

Hozza létre a **City** és **Skyscraper** modelleket kapcsolatokkal. **Fontos**: Kapcsolja ki a timestamps kezelést!

### ✅ Megoldás

#### City model:

```bash
php artisan make:model City
```

**app/Models/City.php**:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory;

    // Nincs timestamps
    public $timestamps = false;

    protected $fillable = [
        'country_code',
        'name',
    ];

    // Kapcsolat: egy városhoz több felhőkarcoló tartozik
    public function skyscrapers()
    {
        return $this->hasMany(Skyscraper::class);
    }
}
```

#### Skyscraper model:

```bash
php artisan make:model Skyscraper
```

**app/Models/Skyscraper.php**:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Skyscraper extends Model
{
    use HasFactory;

    // Nincs timestamps
    public $timestamps = false;

    protected $fillable = [
        'name',
        'city_id',
        'height',
        'stories',
        'finished',
    ];

    protected $casts = [
        'height' => 'float',
        'stories' => 'integer',
        'finished' => 'integer',
    ];

    // Kapcsolat: minden felhőkarcoló egy városhoz tartozik
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
```

---

## 4. Validációs szabályok

### 📋 Feladat

Hozzon létre Request osztályokat mindkét entitáshoz.

**Cities validációs szabályai:**
- **country_code**: kötelező, szöveg, pontosan 2 karakter
- **name**: kötelező, szöveg, maximum 25 karakter

**Skyscrapers validációs szabályai:**
- **name**: kötelező, szöveg, legalább 2 karakter, maximum 50 karakter
- **city_id**: kötelező, létező város ID (exists:cities,id)
- **height**: kötelező, szám, 140 és 1000 között
- **stories**: opcionális, szám, 25 és 300 között
- **finished**: opcionális, szám, 1900 és 3000 között

### ✅ Megoldás

#### City validációk:

```bash
php artisan make:request StoreCityRequest
php artisan make:request UpdateCityRequest
```

**app/Http/Requests/StoreCityRequest.php** és **UpdateCityRequest.php**:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country_code' => 'required|string|size:2',
            'name' => 'required|string|max:25',
        ];
    }
}
```

#### Skyscraper validációk:

```bash
php artisan make:request StoreSkyscraperRequest
php artisan make:request UpdateSkyscraperRequest
```

**app/Http/Requests/StoreSkyscraperRequest.php** és **UpdateSkyscraperRequest.php**:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSkyscraperRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:50',
            'city_id' => 'required|integer|exists:cities,id',
            'height' => 'required|numeric|between:140,1000',
            'stories' => 'nullable|integer|between:25,300',
            'finished' => 'nullable|integer|between:1900,3000',
        ];
    }
}
```

---

## 5. Controllerek létrehozása

### 📋 Feladat

Hozzon létre két controllert teljes CRUD funkcionalitással. A Skyscraper listázásnál használjon eager loading-ot.

### ✅ Megoldás

#### CityController:

```bash
php artisan make:controller CityController --api
```

**app/Http/Controllers/CityController.php**:

```php
<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;

class CityController extends Controller
{
    public function index()
    {
        return City::all();
    }

    public function show(City $city)
    {
        return $city;
    }

    public function store(StoreCityRequest $request)
    {
        $city = City::create($request->validated());
        return response()->json($city, 201);
    }

    public function update(UpdateCityRequest $request, City $city)
    {
        $city->update($request->validated());
        return $city;
    }

    public function destroy(City $city)
    {
        $city->delete();
        return response()->json(null, 204);
    }
}
```

#### SkyscraperController:

```bash
php artisan make:controller SkyscraperController --api
```

**app/Http/Controllers/SkyscraperController.php**:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Skyscraper;
use App\Http\Requests\StoreSkyscraperRequest;
use App\Http\Requests\UpdateSkyscraperRequest;

class SkyscraperController extends Controller
{
    public function index()
    {
        // Eager loading: betöltjük a városokat is
        return Skyscraper::with('city')->get();
    }

    public function show(Skyscraper $skyscraper)
    {
        return $skyscraper->load('city');
    }

    public function store(StoreSkyscraperRequest $request)
    {
        $skyscraper = Skyscraper::create($request->validated());
        return response()->json($skyscraper->load('city'), 201);
    }

    public function update(UpdateSkyscraperRequest $request, Skyscraper $skyscraper)
    {
        $skyscraper->update($request->validated());
        return $skyscraper->load('city');
    }

    public function destroy(Skyscraper $skyscraper)
    {
        $skyscraper->delete();
        return response()->json(null, 204);
    }
}
```

---

## 6. Routing beállítása

### 📋 Feladat

Állítsa be az API route-okat mindkét entitáshoz. Az azonosító csak szám lehet!

### ✅ Megoldás

**routes/api.php**:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;
use App\Http\Controllers\SkyscraperController;

Route::apiResource('cities', CityController::class)->whereNumber('city');
Route::apiResource('skyscrapers', SkyscraperController::class)->whereNumber('skyscraper');
```

---

## 7. Seederek beállítása

### 📋 Feladat

Másolja be a kapott seeder fájlokat és állítsa be a sorrend végre hajtást (először cities, utána skyscrapers).

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
            CitySeeder::class,
            SkyscraperSeeder::class,
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
# Összes város listázása
curl http://localhost:8000/api/cities

# Új város létrehozása
curl -X POST http://localhost:8000/api/cities \
  -H "Content-Type: application/json" \
  -d '{"country_code":"US","name":"New York"}'

# Összes felhőkarcoló listázása (város adatokkal együtt)
curl http://localhost:8000/api/skyscrapers

# Új felhőkarcoló létrehozása
curl -X POST http://localhost:8000/api/skyscrapers \
  -H "Content-Type: application/json" \
  -d '{"name":"Empire State Building","city_id":1,"height":443.2,"stories":102,"finished":1931}'

# Város törlése (cascade delete miatt a felhőkarcolói is törlődnek)
curl -X DELETE http://localhost:8000/api/cities/1
```

---

## Fontos megjegyzések

1. **Timestamps kikapcsolása**: `public $timestamps = false;` mindkét modellben
2. **Eager Loading**: `Skyscraper::with('city')->get()` - elkerüli az N+1 query problémát
3. **Cascade Delete**: Ha törlöd a várost, az összes felhőkarcolója is törlődik
4. **size vs max**: `size:2` = pontosan 2 karakter, `max:25` = maximum 25 karakter
