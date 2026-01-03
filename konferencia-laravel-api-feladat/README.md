# Konferencia Regisztráció API - Laravel REST API Feladat

Egy több napos rendezvénysorozat számára kell egy regisztrációs REST API-t készíteni Laravel keretrendszerben. A rendezvénysorozat **2022-02-01 és 2022-03-14** között kerül megrendezésre.

---

## 1. Előkészületek

### 📋 Feladat

1. Klónozza le a fullstack alapot `konferencia-api-vnev-knev` néven
2. Másolja be a forrásként kapott `openapi.yaml` fájlt a `swagger` mappába
3. Állítsa be, hogy a modelleket szigorú módban kezelje

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

## 2. Adatbázis struktúra és Migráció

### 📋 Feladat

Hozzon létre egy **registrations** táblát az adatbázisban, amely támogatja a SoftDelete-et.

| Mező neve | Típus | Megjegyzés |
|-----------|-------|------------|
| id | Egész [AI,PK] | A regisztráció azonosítója |
| name | Szöveg (max 40 karakter) | A regisztráló neve |
| vegetarian | Logikai | Vegetáriánus étrendet kér e (1), vagy sem (0) |
| date | Dátum | Erre a napra szól a regisztráció |
| arrived | Idő (opcionális) | Az érkezés ideje, ha nem érkezett meg, akkor null |
| created_at | Dátum/Idő (opcionális) | A rekord létrehozásának pontos ideje |
| updated_at | Dátum/Idő (opcionális) | A rekord módosításának pontos ideje |
| deleted_at | Dátum/Idő (opcionális) | A rekord törlésének pontos ideje |

### ✅ Megoldás

```bash
php artisan make:migration create_registrations_table
```

**database/migrations/xxxx_create_registrations_table.php**:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40);
            $table->boolean('vegetarian');
            $table->date('date');
            $table->time('arrived')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
```

```bash
php artisan migrate
```

---

## 3. Model létrehozása

### 📋 Feladat

Hozza létre a **Registration** modellt. Állítsa be a fillable mezőket, castokat, és biztosítsa a SoftDelete támogatást.

### ✅ Megoldás

```bash
php artisan make:model Registration
```

**app/Models/Registration.php**:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Registration extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'vegetarian',
        'date',
        'arrived',
    ];

    protected $casts = [
        'vegetarian' => 'boolean',
        'date' => 'date',
        'arrived' => 'datetime:H:i:s',
    ];
}
```

---

## 4. Resource létrehozása

### 📋 Feladat

Hozzon létre egy **RegistrationResource** osztályt, amely a következő formában adja vissza az adatokat:

- **id**: A regisztráció azonosítója
- **name**: A regisztráló személy neve
- **vegetarian**: Logikai érték (boolean), hogy vegetáriánus-e
- **date**: A regisztráció dátuma
- **arrived**: Az érkezés ideje, vagy "Nem érkezett meg" szöveg, ha null

### ✅ Megoldás

```bash
php artisan make:resource RegistrationResource
```

**app/Http/Resources/RegistrationResource.php**:

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegistrationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'vegetarian' => (bool) $this->vegetarian,
            'date' => $this->date->format('Y-m-d'),
            'arrived' => $this->arrived ? $this->arrived->format('H:i:s') : 'Nem érkezett meg',
        ];
    }
}
```

---

## 5. Validációs szabályok (Request osztályok)

### 📋 Feladat

Hozzon létre Request osztályokat az új és meglévő regisztrációk validálására.

**Validációs szabályok:**
- **name**: kötelező, legalább 1 karakter, maximum 40 karakter
- **vegetarian**: kötelező, csak boolean érték (true/false)
- **date**: kötelező, érvényes dátum, 2022-02-01 és 2022-03-14 között
- **arrived**: opcionális, érvényes időpont, 08:00:00 és 14:00:00 között

### ✅ Megoldás

```bash
php artisan make:request StoreRegistrationRequest
php artisan make:request UpdateRegistrationRequest
```

**app/Http/Requests/StoreRegistrationRequest.php**:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Bárki regisztrálhat
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:1|max:40',
            'vegetarian' => 'required|boolean',
            'date' => 'required|date|after_or_equal:2022-02-01|before_or_equal:2022-03-14',
            'arrived' => 'nullable|date_format:H:i:s|after_or_equal:08:00:00|before_or_equal:14:00:00',
        ];
    }
}
```

**app/Http/Requests/UpdateRegistrationRequest.php**: Ugyanazok a szabályok.

---

## 6. Controller létrehozása

### 📋 Feladat

Hozzon létre egy **RegistrationController**-t, amely implementálja a következő funkciókat:

- **index**: Összes regisztráció listázása rendezéssel (orderBy, order)
- **show**: Egy regisztráció megjelenítése
- **store**: Új regisztráció létrehozása
- **update**: Regisztráció módosítása
- **destroy**: Regisztráció törlése
- **count**: Regisztrációk számának visszaadása

**Rendezés szabályai:**
- **orderBy**: A rendezés alapja (`name` vagy `date`)
- **order**: A rendezés iránya (`asc` vagy `desc`) - csak kisbetűs!

### ✅ Megoldás

```bash
php artisan make:controller RegistrationController --api
```

**app/Http/Controllers/RegistrationController.php**:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Http\Requests\StoreRegistrationRequest;
use App\Http\Requests\UpdateRegistrationRequest;
use App\Http\Resources\RegistrationResource;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $query = Registration::query();

        // Rendezés kezelése
        if ($request->has('orderBy') && in_array($request->orderBy, ['name', 'date'])) {
            $order = $request->has('order') && in_array($request->order, ['asc', 'desc']) 
                ? $request->order 
                : 'asc';
            
            $query->orderBy($request->orderBy, $order);
        }

        return RegistrationResource::collection($query->get());
    }

    public function show(Registration $registration)
    {
        return new RegistrationResource($registration);
    }

    public function store(StoreRegistrationRequest $request)
    {
        $registration = Registration::create($request->validated());
        return new RegistrationResource($registration);
    }

    public function update(UpdateRegistrationRequest $request, Registration $registration)
    {
        $registration->update($request->validated());
        return new RegistrationResource($registration);
    }

    public function destroy(Registration $registration)
    {
        $registration->delete();
        return response()->json(null, 204);
    }

    public function count()
    {
        return response()->json(['count' => Registration::count()]);
    }
}
```

---

## 7. Routing beállítása

### 📋 Feladat

Állítsa be az API route-okat:

| Method | URI | Controller | Action | Name |
|--------|-----|------------|--------|------|
| GET | /api/registrations | RegistrationController | index | registrations.index |
| GET | /api/registrations/{registration} | RegistrationController | show | registrations.show |
| GET | /api/registrations/count | RegistrationController | count | registrations.count |
| POST | /api/registrations | RegistrationController | store | registrations.store |
| PUT | /api/registrations/{registration} | RegistrationController | update | registrations.update |
| DELETE | /api/registrations/{registration} | RegistrationController | destroy | registrations.destroy |

**Fontos**: A `count` útvonalat a `show` elé kell helyezni!

### ✅ Megoldás

**routes/api.php**:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrationController;

// Fontos: a count útvonalnak előbb kell lennie, mint a show-nak!
Route::get('registrations/count', [RegistrationController::class, 'count'])->name('registrations.count');

Route::apiResource('registrations', RegistrationController::class)->whereNumber('registration');
```

---

## 8. Seeder beállítása

### 📋 Feladat

Másolja be a `RegistrationSeeder` fájlt és állítsa be a futtatást a DatabaseSeeder-ben.

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
            RegistrationSeeder::class,
        ]);
    }
}
```

```bash
php artisan db:seed
```

---

## 9. Tesztelés

```bash
# Összes regisztráció listázása
curl http://localhost:8000/api/registrations

# Rendezés név szerint növekvő
curl http://localhost:8000/api/registrations?orderBy=name&order=asc

# Rendezés dátum szerint csökkenő
curl http://localhost:8000/api/registrations?orderBy=date&order=desc

# Regisztrációk száma
curl http://localhost:8000/api/registrations/count

# Egy konkrét regisztráció lekérése
curl http://localhost:8000/api/registrations/1

# Új regisztráció létrehozása
curl -X POST http://localhost:8000/api/registrations \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Kiss János",
    "vegetarian": true,
    "date": "2022-02-15",
    "arrived": "09:30:00"
  }'

# Regisztráció módosítása
curl -X PUT http://localhost:8000/api/registrations/1 \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Kiss János",
    "vegetarian": false,
    "date": "2022-02-16",
    "arrived": "10:00:00"
  }'

# Regisztráció törlése
curl -X DELETE http://localhost:8000/api/registrations/1
```

---

## Gyakori hibák és megoldásaik

1. **Count útvonal nem működik**: Győződj meg róla, hogy a `count` útvonal a `show` elé van helyezve
2. **Boolean validáció hibás**: Ellenőrizd, hogy `true`/`false` értékeket küldesz-e, nem `1`/`0`-t
3. **Dátum validáció**: A dátumformátum `Y-m-d` (pl. 2022-02-15)
4. **Idő validáció**: Az időformátum `H:i:s` (pl. 09:30:00)

## Hasznos parancsok

```bash
# Összes route listázása
php artisan route:list

# Adatbázis újraindítása seederekkel
php artisan migrate:fresh --seed
```
