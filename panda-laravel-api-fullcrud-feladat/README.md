# Óriáspandák API - Laravel REST API FULL CRUD Feladat

Egy teljes CRUD (Create, Read, Update, Delete) REST API létrehozása óriáspandák adatainak kezelésére Laravel keretrendszerben.

---

## 1. Adatbázis struktúra és Migráció

### 📋 Feladat

Hozzon létre egy **pandas** táblát:

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

Hozza létre a **Panda** modellt, állítsa be a fillable mezőket és számítsa ki a pandas korát accessor segítségével. Állítsa be a strict mode-ot is.

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

Hozzon létre egy **PandaResource** osztályt, amely visszaadja a panda adatait a számított korral együtt.

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

## 4. Validációs szabályok

### 📋 Feladat

Hozzon létre Request osztályokat a pandák validálására.

**Validációs szabályok:**
- **name**: kötelező, legalább 1 karakter, maximum 10 karakter
- **sex**: kötelező, csak 'M' vagy 'F' értéket fogad el
- **birth**: opcionális, érvényes dátum

### ✅ Megoldás

```bash
php artisan make:request StorePandaRequest
php artisan make:request UpdatePandaRequest
```

**app/Http/Requests/StorePandaRequest.php** és **UpdatePandaRequest.php**:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePandaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:1|max:10',
            'sex' => ['required', 'string', Rule::in(['M', 'F'])],
            'birth' => 'nullable|date',
        ];
    }
}
```

---

## 5. Controller létrehozása

### 📋 Feladat

Hozzon létre egy **PandaController**-t teljes CRUD funkcionalitással és rendezés támogatással.

**Rendezés szabályai:**
- **orderBy**: A rendezés alapja (`name` vagy `age`) - age esetén birth szerint rendez
- **order**: A rendezés iránya (`asc` vagy `desc`) - csak kisbetűs!

### ✅ Megoldás

```bash
php artisan make:controller PandaController --api
```

**app/Http/Controllers/PandaController.php**:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Panda;
use App\Http\Requests\StorePandaRequest;
use App\Http\Requests\UpdatePandaRequest;
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

    public function store(StorePandaRequest $request)
    {
        $panda = Panda::create($request->validated());
        return new PandaResource($panda);
    }

    public function update(UpdatePandaRequest $request, Panda $panda)
    {
        $panda->update($request->validated());
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

## 6. Routing beállítása

### 📋 Feladat

Állítsa be az API route-okat:

| Method | URI | Controller | Action | Name |
|--------|-----|------------|--------|------|
| GET | api/pandas | PandaController | index | pandas.index |
| GET | api/pandas/{id} | PandaController | show | pandas.show |
| POST | api/pandas | PandaController | store | pandas.store |
| PUT | api/pandas/{id} | PandaController | update | pandas.update |
| DELETE | api/pandas/{id} | PandaController | destroy | pandas.destroy |

### ✅ Megoldás

**routes/api.php**:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PandaController;

Route::apiResource('pandas', PandaController::class);
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

# Új panda létrehozása
curl -X POST http://localhost:8000/api/pandas \
  -H "Content-Type: application/json" \
  -d '{"name":"Bao Bao","sex":"F","birth":"2013-08-23"}'

# Panda módosítása
curl -X PUT http://localhost:8000/api/pandas/1 \
  -H "Content-Type: application/json" \
  -d '{"name":"Bao Bao","sex":"F","birth":"2013-08-23"}'

# Panda törlése
curl -X DELETE http://localhost:8000/api/pandas/1
```

---

## Fontos megjegyzések

- Az **age** mező nem létezik az adatbázisban, accessor segítségével számított érték
- Az age szerinti rendezésnél valójában **birth** szerint rendezünk
- **Növekvő birth** = csökkenő age!
