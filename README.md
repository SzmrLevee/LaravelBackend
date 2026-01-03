# Laravel REST API Fejlesztési Útmutató

> Átfogó útmutató Laravel REST API-k létrehozásához, az alapoktól a haladó funkciókig.

## Előkészületek

### 1. Laravel projekt létrehozása

#### Opció A: Klónozás fullstack2025 alapból (ajánlott)

```bash
git clone https://github.com/rcsnjszg/fullstack2025.git project-name
cd project-name
bash start.sh  # Automatikusan felállítja a teljes környezetet!
```

> **💡 Tipp**: A fullstack2025 repo egy teljes Docker-alapú környezetet tartalmaz Laravel + MySQL + Nginx-szel. A `start.sh` script mindent beállít és elindít automatikusan - nincs szükség további konfigurációra!

#### Opció B: Új Laravel projekt létrehozása nulláról

```bash
# Új Laravel projekt
composer create-project laravel/laravel project-name
cd project-name

# Környezeti változók beállítása
cp .env.example .env
# Állítsd be a .env fájlban az adatbázis kapcsolatot (DB_DATABASE, DB_USERNAME, stb.)

# Application key generálása
php artisan key:generate

# Adatbázis migrációk futtatása
php artisan migrate
```

### 2. Állítsa be, hogy a modelleket szigorú módban kezelje (ajánlott)

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

A strict mode előnyei:
- Lazy loading figyelmeztetések
- Nem létező attribútumok hozzáférése hibát dob
- Biztosabb, jobb kód

---

## Projektstruktúra

Egy tipikus Laravel REST API projektstruktúra:

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── UserController.php
│   │   └── ProductController.php
│   ├── Requests/
│   │   ├── StoreUserRequest.php
│   │   └── UpdateUserRequest.php
│   └── Resources/
│       └── UserResource.php
├── Models/
│   ├── User.php
│   └── Product.php
database/
├── migrations/
│   ├── 2024_01_01_create_users_table.php
│   └── 2024_01_02_create_products_table.php
└── seeders/
    ├── DatabaseSeeder.php
    ├── UserSeeder.php
    └── ProductSeeder.php
routes/
└── api.php
```

---

## Adatbázis - Migrációk

### Migráció létrehozása

```bash
php artisan make:migration create_products_table
```

### Alapvető migráció struktúra

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes(); // Opcionális soft delete
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

### Gyakori mező típusok

```php
// Egész számok
$table->id();                           // BIGINT UNSIGNED AUTO_INCREMENT PK
$table->bigInteger('votes');            // BIGINT
$table->integer('votes');               // INT
$table->tinyInteger('votes');           // TINYINT
$table->unsignedInteger('votes');       // UNSIGNED INT

// Szövegek
$table->string('name', 100);            // VARCHAR(100)
$table->char('code', 2);                // CHAR(2) - fix hossz
$table->text('description');            // TEXT
$table->longText('article');            // LONGTEXT

// Dátumok és idő
$table->date('birth_date');             // DATE
$table->time('alarm');                  // TIME
$table->dateTime('created_at');         // DATETIME
$table->timestamp('added_at');          // TIMESTAMP
$table->timestamps();                   // created_at + updated_at

// Logikai
$table->boolean('is_active');           // BOOLEAN (TINYINT(1))

// Valós számok
$table->float('height');                // FLOAT
$table->double('latitude', 8, 2);       // DOUBLE
$table->decimal('amount', 10, 2);       // DECIMAL (pénzügyi adatokhoz)

// JSON
$table->json('options');                // JSON

// Egyéb
$table->enum('status', ['pending', 'active', 'inactive']);
$table->softDeletes();                  // deleted_at mező
```

### Módosítók (Modifiers)

```php
$table->string('email')->nullable();           // NULL lehet
$table->string('name')->default('Guest');      // Alapértelmezett érték
$table->string('email')->unique();             // Egyedi értékek
$table->integer('votes')->unsigned();          // Előjel nélküli
$table->timestamp('created_at')->useCurrent(); // Jelenlegi időbélyeg
```

### Foreign KeyConstraintek

```php
// Modern módszer (ajánlott)
$table->foreignId('user_id')
      ->constrained()
      ->onDelete('cascade');

// Részletes kontroll
$table->foreignId('category_id')
      ->constrained('categories')
      ->onUpdate('cascade')
      ->onDelete('set null');

// Régebbi módszer
$table->unsignedBigInteger('user_id');
$table->foreign('user_id')
      ->references('id')
      ->on('users')
      ->onDelete('cascade');
```

### Migrációk futtatása

```bash
# Összes migráció futtatása
php artisan migrate

# Migráció visszavonása (utolsó batch)
php artisan migrate:rollback

# Összes migráció visszavonása
php artisan migrate:reset

# Adatbázis újraindítása
php artisan migrate:fresh

# Adatbázis újraindítása seederekkel
php artisan migrate:fresh --seed
```

---

## Modellek

### Model létrehozása

```bash
# Csak model
php artisan make:model Product

# Model + migráció
php artisan make:model Product -m

# Model + migráció + controller + resource + seeder + factory
php artisan make:model Product -a

# Model + migráció + controller (API resource controller)
php artisan make:model Product -mcr --api
```

### Alapvető model struktúra

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    // Tábla neve (opcionális, ha különbözik a konvenciótól)
    protected $table = 'products';

    // Elsődleges kulcs (opcionális, ha nem 'id')
    protected $primaryKey = 'product_id';

    // Inkrementált kulcs (false ha nem auto-increment)
    public $incrementing = true;

    // Timestamps kezelése
    public $timestamps = true;  // true = van created_at és updated_at
                                // false = nincs timestamps

    // Tömeges hozzárendelés (mass assignment)
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'is_active',
        'category_id',
    ];

    // Védett mezők (alternatíva a fillable-höz)
    // protected $guarded = ['id'];

    // Típuskonverziók (casting)
    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'options' => 'array',
    ];

    // Rejtett mezők (JSON válaszokban)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Mindig látható mezők
    protected $visible = [
        'id',
        'name',
        'price',
    ];

    // Alapértelmezett attribútumok
    protected $attributes = [
        'is_active' => true,
        'stock' => 0,
    ];
}
```

### Accessors (Getters) - Számított értékek

```php
// Laravel 9+ Accessor (ajánlott)
use Illuminate\Database\Eloquent\Casts\Attribute;

protected function fullName(): Attribute
{
    return Attribute::make(
        get: fn () => $this->first_name . ' ' . $this->last_name,
    );
}

// Használat: $user->full_name

// Régi módszer (Laravel 8 és korábbi)
public function getFullNameAttribute()
{
    return $this->first_name . ' ' . $this->last_name;
}
```

### Mutators (Setters) - Adatok módosítása mentés előtt

```php
// Modern módszer
protected function name(): Attribute
{
    return Attribute::make(
        get: fn ($value) => ucfirst($value),
        set: fn ($value) => strtolower($value),
    );
}

// Régi módszer
public function setNameAttribute($value)
{
    $this->attributes['name'] = strtolower($value);
}
```

### Példa: Kor számítása születési dátumból

```php
use Carbon\Carbon;

class User extends Model
{
    protected $casts = [
        'birth_date' => 'date',
    ];

    // Accessor - kor kiszámítása
    public function getAgeAttribute()
    {
        if (!$this->birth_date) {
            return null;
        }
        return $this->birth_date->diffInYears(Carbon::now());
    }
}

// Használat
$user = User::find(1);
echo $user->age; // pl. 25
```

---

## Resources - Válasz formázás

### Resource osztály létrehozása

```bash
php artisan make:resource ProductResource
php artisan make:resource ProductCollection
```

### Alapvető Resource

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => number_format($this->price, 2),
            'stock' => $this->stock,
            'is_active' => (bool) $this->is_active,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
```

### Resource használata Controller-ben

```php
// Egy elem
return new ProductResource($product);

// Több elem
return ProductResource::collection($products);

// Paginálással
return ProductResource::collection($products->paginate(15));
```

### Feltételes mezők

```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        
        // Csak akkor jelenjen meg, ha be van töltve
        'category' => new CategoryResource($this->whenLoaded('category')),
        
        // Csak akkor jelenjen meg, ha létezik
        'image_url' => $this->when($this->image, $this->image_url),
        
        // Csak admin felhasználóknak
        'cost' => $this->when($request->user()?->is_admin, $this->cost),
        
        // Több mező feltételesen
        $this->mergeWhen($request->user()?->is_admin, [
            'cost' => $this->cost,
            'margin' => $this->margin,
        ]),
    ];
}
```

### Kapcsolatok Resource-al

```php
// One-to-One / Many-to-One
'category' => new CategoryResource($this->whenLoaded('category')),

// One-to-Many / Many-to-Many
'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),

// Számított érték
'average_rating' => $this->when(
    $this->relationLoaded('reviews'),
    fn () => $this->reviews->avg('rating')
),
```

### Meta adatok hozzáadása

```php
public function with(Request $request): array
{
    return [
        'meta' => [
            'version' => '1.0.0',
            'timestamp' => now()->toIso8601String(),
        ],
    ];
}
```

### Collection Resource (opcionális)

Ha egyedi collection viselkedést szeretnél:

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'total' => $this->collection->count(),
            'meta' => [
                'total_value' => $this->collection->sum('price'),
            ],
        ];
    }
}
```

---

## Validáció - Request osztályok

### Request osztály létrehozása

```bash
php artisan make:request StoreProductRequest
php artisan make:request UpdateProductRequest
```

### Request osztály struktúra

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Authorize: ki érheti el ezt a request-et
     */
    public function authorize(): bool
    {
        return true; // Mindenki
        
        // Bejelentkezett felhasználók
        // return auth()->check();
        
        // Admin felhasználók
        // return auth()->user()?->is_admin ?? false;
    }

    /**
     * Validációs szabályok
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:100',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|between:0,999999.99',
            'stock' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
            'category_id' => 'required|integer|exists:categories,id',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ];
    }

    /**
     * Egyedi hibaüzenetek (opcionális)
     */
    public function messages(): array
    {
        return [
            'name.required' => 'A termék neve kötelező!',
            'name.min' => 'A termék nevének legalább :min karakter hosszúnak kell lennie.',
            'price.between' => 'Az ár :min és :max között kell legyen.',
            'category_id.exists' => 'A kiválasztott kategória nem létezik.',
        ];
    }

    /**
     * Mezőnevek fordítása (opcionális)
     */
    public function attributes(): array
    {
        return [
            'name' => 'termék neve',
            'price' => 'ár',
            'stock' => 'készlet',
        ];
    }

    /**
     * Adatok előkészítése validáció előtt (opcionális)
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->name),
        ]);
    }
}
```

### Gyakori validációs szabályok

```php
// Kötelező mezők
'field' => 'required',
'field' => 'required_if:other_field,value',
'field' => 'required_unless:other_field,value',
'field' => 'required_with:other_field',
'field' => 'required_without:other_field',

// Típusok
'field' => 'string',
'field' => 'integer',
'field' => 'numeric',
'field' => 'boolean',
'field' => 'array',
'field' => 'file',
'field' => 'image',
'field' => 'json',

// Hossz és méret
'field' => 'min:3',
'field' => 'max:100',
'field' => 'size:10',              // Pontosan 10
'field' => 'between:3,100',

// Számok
'field' => 'integer|min:0|max:100',
'field' => 'numeric|between:0,999.99',
'field' => 'digits:4',             // Pontosan 4 számjegy
'field' => 'digits_between:3,10',

// Dátumok
'field' => 'date',
'field' => 'date_format:Y-m-d',
'field' => 'after:2022-01-01',
'field' => 'after_or_equal:2022-01-01',
'field' => 'before:2023-12-31',
'field' => 'before_or_equal:2023-12-31',

// Idő
'field' => 'date_format:H:i:s',
'field' => 'after_or_equal:08:00:00',
'field' => 'before_or_equal:14:00:00',

// Szöveg formátumok
'field' => 'email',
'field' => 'url',
'field' => 'alpha',                // Csak betűk
'field' => 'alpha_num',            // Betűk és számok
'field' => 'alpha_dash',           // Betűk, számok, kötőjelek

// Egyediség
'field' => 'unique:table',
'field' => 'unique:table,column',
'field' => 'unique:table,column,' . $id,  // Update esetén

// Létezés ellenőrzése
'field' => 'exists:table,column',

// Enum/In értékek
'field' => 'in:value1,value2,value3',
'field' => Rule::in(['value1', 'value2', 'value3']),

// Fájlok
'file' => 'file|mimes:pdf,doc,docx|max:10240',
'image' => 'image|mimes:jpeg,png,jpg|max:2048',

// Tömb validáció
'items' => 'array',
'items.*' => 'string|max:50',      // Minden elem
'items.0' => 'required',           // Első elem
'items.*.id' => 'integer',         // Minden elem id-ja

// Opcionális (nullable)
'field' => 'nullable|string|max:100',

// Confirmed (jelszó megerősítés)
'password' => 'required|string|min:8|confirmed',
// Ehhez kell egy password_confirmation mező

// Regex
'field' => 'regex:/^[A-Z]{2}$/',   // Pl. országkód
```

### Update Request különbségei

Az update validáció gyakran hasonló a store-hoz, de lehetnek különbségek:

```php
class UpdateProductRequest extends FormRequest
{
    public function rules(): array
    {
        $productId = $this->route('product')->id;
        
        return [
            'name' => 'required|string|max:100|unique:products,name,' . $productId,
            'price' => 'required|numeric|between:0,999999.99',
            'stock' => 'sometimes|integer|min:0',  // 'sometimes' = csak ha benne van
        ];
    }
}
```

---

## Controllerek

### Controller létrehozása

```bash
# API Resource Controller (ajánlott REST API-hoz)
php artisan make:controller ProductController --api

# Normál Resource Controller
php artisan make:controller ProductController --resource

# Model-hez kötött controller
php artisan make:controller ProductController --model=Product --api
```

### Teljes CRUD API Controller példa

```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/products
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Szűrés
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Keresés
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Rendezés
        if ($request->has('orderBy') && in_array($request->orderBy, ['name', 'price', 'created_at'])) {
            $order = $request->has('order') && in_array($request->order, ['asc', 'desc']) 
                ? $request->order 
                : 'asc';
            
            $query->orderBy($request->orderBy, $order);
        }

        // Kapcsolatok betöltése (eager loading)
        $query->with('category');

        // Válasz Resource-al vagy anélkül
        return ProductResource::collection($query->get());
        // vagy: return $query->get();

        // Paginálás (opcionális)
        // return ProductResource::collection($query->paginate(15));
    }

    /**
     * Display the specified resource.
     * GET /api/products/{id}
     */
    public function show(Product $product)
    {
        // Kapcsolatok betöltése
        $product->load('category', 'reviews');

        return new ProductResource($product);
        // vagy: return $product;
    }

    /**
     * Store a newly created resource in storage.
     * POST /api/products
     */
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        return new ProductResource($product);
        // vagy: return response()->json($product, 201);
    }

    /**
     * Update the specified resource in storage.
     * PUT/PATCH /api/products/{id}
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return new ProductResource($product);
        // vagy: return $product;
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /api/products/{id}
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(null, 204);
        // vagy: return response()->noContent();
    }
}
```

### Route Model Binding

A Laravel automatikusan feloldja a modellt az ID alapján:

```php
// Ez automatikusan lekéri a Product modellt a route paraméter alapján
public function show(Product $product)
{
    return $product;
}

// Ha nem található, automatikus 404 választ küld
```

Ha egyedi kulcsot szeretnél használni:

```php
// Model-ben
public function getRouteKeyName()
{
    return 'slug'; // slug helyett id-t használ
}
```

### Egyedi műveletek

```php
/**
 * Keresés termékek között
 * GET /api/products/search?q=laptop
 */
public function search(Request $request)
{
    $query = $request->get('q', '');
    
    $products = Product::where('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->get();
    
    return ProductResource::collection($products);
}

/**
 * Kiemelt termékek
 * GET /api/products/featured
 */
public function featured()
{
    $products = Product::where('is_featured', true)
                      ->orderBy('created_at', 'desc')
                      ->limit(10)
                      ->get();
    
    return ProductResource::collection($products);
}

/**
 * Termék aktiválása/deaktiválása
 * PATCH /api/products/{id}/toggle-active
 */
public function toggleActive(Product $product)
{
    $product->is_active = !$product->is_active;
    $product->save();
    
    return new ProductResource($product);
}
```

---

## Routing

### API Route beállítása

**routes/api.php**:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// API Resource (RESTful CRUD)
Route::apiResource('products', ProductController::class);

// Korlátozott resource (csak bizonyos műveletek)
Route::apiResource('products', ProductController::class)
     ->only(['index', 'show']);

Route::apiResource('products', ProductController::class)
     ->except(['destroy']);

// Egyedi route-ok
Route::get('products/search', [ProductController::class, 'search']);
Route::get('products/featured', [ProductController::class, 'featured']);

// Paraméter validáció
Route::apiResource('products', ProductController::class)
     ->whereNumber('product');

// Csoportosítás
Route::prefix('v1')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::apiResource('categories', CategoryController::class);
});

// Middleware hozzáadása
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', ProductController::class);
});
```

### API Resource route-ok

Az `apiResource` automatikusan létrehozza a következő route-okat:

| HTTP Metódus | URI | Action | Route Name |
|--------------|-----|--------|------------|
| GET | /api/products | index | products.index |
| GET | /api/products/{id} | show | products.show |
| POST | /api/products | store | products.store |
| PUT/PATCH | /api/products/{id} | update | products.update |
| DELETE | /api/products/{id} | destroy | products.destroy |

**Megjegyzés**: Az `apiResource` nem tartalmazza a `create` és `edit` műveleteket (azok HTML form-okhoz kellenek).

### Route lista megtekintése

```bash
php artisan route:list
php artisan route:list --path=api
php artisan route:list --path=api/products
```

---

## Kapcsolatok (Relationships)

### One-to-Many (1:N)

**Példa**: Egy kategóriához több termék tartozik.

**Model beállítások**:

```php
// Category model
class Category extends Model
{
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

// Product model
class Product extends Model
{
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
```

**Migráció**:

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->foreignId('category_id')->constrained()->onDelete('cascade');
    $table->timestamps();
});
```

**Használat**:

```php
// Termék kategóriájának lekérése
$product = Product::find(1);
echo $product->category->name;

// Kategória termékeinek lekérése
$category = Category::find(1);
foreach ($category->products as $product) {
    echo $product->name;
}

// Új termék létrehozása kategóriához
$category->products()->create([
    'name' => 'New Product',
    'price' => 99.99,
]);

// Eager loading (N+1 query elkerülése)
$products = Product::with('category')->get();
```

### Many-to-Many (N:N)

**Példa**: Termékek és címkék (tags).

**Model beállítások**:

```php
// Product model
class Product extends Model
{
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}

// Tag model
class Tag extends Model
{
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
```

**Migrációk**:

```php
// Tags tábla
Schema::create('tags', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->timestamps();
});

// Pivot tábla (kapcsoló tábla)
Schema::create('product_tag', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->foreignId('tag_id')->constrained()->onDelete('cascade');
    $table->timestamps(); // Opcionális
});
```

**Használat**:

```php
// Termék címkéinek lekérése
$product = Product::find(1);
foreach ($product->tags as $tag) {
    echo $tag->name;
}

// Címke hozzáadása termékhez
$product->tags()->attach($tagId);

// Több címke hozzáadása
$product->tags()->attach([1, 2, 3]);

// Címke eltávolítása
$product->tags()->detach($tagId);

// Összes címke eltávolítása
$product->tags()->detach();

// Címkék szinkronizálása (csak ezek maradnak)
$product->tags()->sync([1, 2, 3]);

// Eager loading
$products = Product::with('tags')->get();
```

### One-to-One (1:1)

**Példa**: Felhasználó és profil.

```php
// User model
class User extends Model
{
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
}

// Profile model
class Profile extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

**Migráció**:

```php
Schema::create('profiles', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('bio')->nullable();
    $table->string('avatar')->nullable();
    $table->timestamps();
});
```

### Eager Loading - N+1 Query probléma elkerülése

```php
// ❌ Rossz - N+1 query probléma
$products = Product::all();
foreach ($products as $product) {
    echo $product->category->name; // Minden termékhez új query
}

// ✅ Jó - Eager loading
$products = Product::with('category')->get();
foreach ($products as $product) {
    echo $product->category->name; // Egyetlen extra query az összes kategóriához
}

// Több kapcsolat betöltése
$products = Product::with(['category', 'tags', 'reviews'])->get();

// Nested eager loading
$categories = Category::with('products.reviews')->get();

// Lazy eager loading (ha később kell)
$products = Product::all();
$products->load('category');
```

---

## Seederek

### Seeder létrehozása

```bash
php artisan make:seeder ProductSeeder
```

### Seeder struktúra

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Egyszerű adatok beszúrása
        Product::create([
            'name' => 'Laptop',
            'description' => 'High-end laptop',
            'price' => 1299.99,
            'stock' => 10,
            'is_active' => true,
            'category_id' => 1,
        ]);

        // Több adat beszúrása
        $products = [
            [
                'name' => 'Mouse',
                'price' => 29.99,
                'stock' => 50,
                'category_id' => 1,
            ],
            [
                'name' => 'Keyboard',
                'price' => 79.99,
                'stock' => 30,
                'category_id' => 1,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Vagy insert használata (gyorsabb, de nincs model events)
        Product::insert($products);
    }
}
```

### DatabaseSeeder - Központi seeder

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seederek sorrendje fontos (foreign key-k miatt)
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            TagSeeder::class,
            // ...
        ]);
    }
}
```

### Seederek futtatása

```bash
# Összes seeder futtatása
php artisan db:seed

# Konkrét seeder futtatása
php artisan db:seed --class=ProductSeeder

# Adatbázis újraindítása seederekkel
php artisan migrate:fresh --seed
```

### Factory használata Seederben

Factory-k segítségével gyorsan generálhatsz tesztadatokat:

```bash
php artisan make:factory ProductFactory
```

```php
// database/factories/ProductFactory.php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'stock' => $this->faker->numberBetween(0, 100),
            'is_active' => $this->faker->boolean(80), // 80% true
        ];
    }
}
```

```php
// Seederben használat
public function run(): void
{
    Product::factory()->count(50)->create();
}
```

---

## Gyakori funkciók

### Szűrés (Filtering)

```php
public function index(Request $request)
{
    $query = Product::query();

    // Egyszerű szűrés
    if ($request->has('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    // Boolean szűrés
    if ($request->has('is_active')) {
        $query->where('is_active', $request->boolean('is_active'));
    }

    // Tartomány szűrés (pl. ár)
    if ($request->has('min_price')) {
        $query->where('price', '>=', $request->min_price);
    }
    if ($request->has('max_price')) {
        $query->where('price', '<=', $request->max_price);
    }

    // Dátum szűrés
    if ($request->has('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }

    return ProductResource::collection($query->get());
}
```

### Keresés (Search)

```php
public function index(Request $request)
{
    $query = Product::query();

    if ($request->has('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    return ProductResource::collection($query->get());
}
```

### Rendezés (Sorting)

```php
public function index(Request $request)
{
    $query = Product::query();

    // Engedélyezett rendezési mezők
    $allowedOrderBy = ['name', 'price', 'created_at'];
    
    if ($request->has('orderBy') && in_array($request->orderBy, $allowedOrderBy)) {
        $order = $request->has('order') && in_array($request->order, ['asc', 'desc'])
            ? $request->order
            : 'asc';
        
        $query->orderBy($request->orderBy, $order);
    } else {
        // Alapértelmezett rendezés
        $query->orderBy('created_at', 'desc');
    }

    return ProductResource::collection($query->get());
}
```

### Paginálás (Pagination)

```php
public function index(Request $request)
{
    $perPage = $request->get('per_page', 15); // Alapértelmezett 15
    $perPage = min($perPage, 100); // Maximum 100
    
    $products = Product::paginate($perPage);
    
    return ProductResource::collection($products);
}
```

**Válasz formátum**:

```json
{
  "data": [...],
  "links": {
    "first": "http://example.com/api/products?page=1",
    "last": "http://example.com/api/products?page=10",
    "prev": null,
    "next": "http://example.com/api/products?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    "per_page": 15,
    "to": 15,
    "total": 150
  }
}
```

### Számítások (Aggregates)

```php
// Darabszám
$count = Product::count();
$activeCount = Product::where('is_active', true)->count();

// Összeg
$totalValue = Product::sum('price');

// Átlag
$averagePrice = Product::avg('price');

// Min/Max
$cheapest = Product::min('price');
$mostExpensive = Product::max('price');

// Egyedi értékek száma
$categoryCount = Product::distinct('category_id')->count();
```

### Soft Deletes

```php
// Model-ben
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
}

// Migráció-ban
$table->softDeletes();

// Használat
$product->delete(); // Soft delete (deleted_at kitöltése)
$product->forceDelete(); // Valódi törlés
$product->restore(); // Visszaállítás

// Törölt elemek lekérése
$deletedProducts = Product::onlyTrashed()->get();

// Törölt és aktív elemek együtt
$allProducts = Product::withTrashed()->get();

// Soft deleted elem lekérése ID alapján
$product = Product::withTrashed()->find($id);
```

---

## Best Practices

### 1. Használj Request validációt

❌ **Rossz**:
```php
public function store(Request $request)
{
    $request->validate([...]);
    $product = Product::create($request->all());
}
```

✅ **Jó**:
```php
public function store(StoreProductRequest $request)
{
    $product = Product::create($request->validated());
}
```

### 2. Használj Resource osztályokat

❌ **Rossz**:
```php
public function index()
{
    return Product::all();
}
```

✅ **Jó**:
```php
public function index()
{
    return ProductResource::collection(Product::all());
}
```

### 3. Kerüld az N+1 query problémát

❌ **Rossz**:
```php
$products = Product::all();
foreach ($products as $product) {
    echo $product->category->name;
}
```

✅ **Jó**:
```php
$products = Product::with('category')->get();
foreach ($products as $product) {
    echo $product->category->name;
}
```

### 4. Használj Route Model Binding-ot

❌ **Rossz**:
```php
public function show($id)
{
    $product = Product::findOrFail($id);
    return new ProductResource($product);
}
```

✅ **Jó**:
```php
public function show(Product $product)
{
    return new ProductResource($product);
}
```

### 5. Validáld a rendezési paramétereket

❌ **Rossz**:
```php
$products = Product::orderBy($request->orderBy, $request->order)->get();
```

✅ **Jó**:
```php
$allowedOrderBy = ['name', 'price'];
$orderBy = in_array($request->orderBy, $allowedOrderBy) ? $request->orderBy : 'name';
$order = in_array($request->order, ['asc', 'desc']) ? $request->order : 'asc';
$products = Product::orderBy($orderBy, $order)->get();
```

### 6. Használj $fillable-t a modellekben

❌ **Rossz**:
```php
protected $guarded = [];
```

✅ **Jó**:
```php
protected $fillable = ['name', 'price', 'stock'];
```

### 7. Consistent API válaszok

✅ **Jó**:
```php
// Success
return response()->json($data, 200);

// Created
return response()->json($data, 201);

// No Content
return response()->json(null, 204);

// Bad Request
return response()->json(['message' => 'Error'], 400);

// Not Found
return response()->json(['message' => 'Not found'], 404);
```

### 8. Használj Service osztályokat komplex logikához

```php
// app/Services/ProductService.php
class ProductService
{
    public function calculateDiscount(Product $product, $percentage)
    {
        // Komplex üzleti logika
        $discount = $product->price * ($percentage / 100);
        return $product->price - $discount;
    }
}

// Controller-ben
public function applyDiscount(Product $product, Request $request)
{
    $service = new ProductService();
    $newPrice = $service->calculateDiscount($product, $request->percentage);
    
    $product->update(['price' => $newPrice]);
    
    return new ProductResource($product);
}
```

---

## Hasznos parancsok

### Artisan parancsok összefoglalása

```bash
# Model létrehozás opciókkal
php artisan make:model Product -a              # Mindent generál
php artisan make:model Product -m              # Model + Migration
php artisan make:model Product -c              # Model + Controller
php artisan make:model Product -mcr --api      # Model + Migration + API Controller

# Controllerek
php artisan make:controller ProductController --api
php artisan make:controller ProductController --resource
php artisan make:controller ProductController --model=Product

# Request validációk
php artisan make:request StoreProductRequest
php artisan make:request UpdateProductRequest

# Resources
php artisan make:resource ProductResource
php artisan make:resource ProductCollection

# Migrációk
php artisan make:migration create_products_table
php artisan migrate
php artisan migrate:rollback
php artisan migrate:fresh
php artisan migrate:fresh --seed

# Seederek
php artisan make:seeder ProductSeeder
php artisan db:seed
php artisan db:seed --class=ProductSeeder

# Route lista
php artisan route:list
php artisan route:list --path=api
php artisan route:list --method=GET

# Cache kezelés
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimalizálás
php artisan optimize
php artisan config:cache
php artisan route:cache

# Tinker (interaktív console)
php artisan tinker
>>> Product::all()
>>> Product::find(1)
>>> Product::create(['name' => 'Test'])
```

### Git parancsok API fejlesztéshez

```bash
# Inicializálás
git init
git add .
git commit -m "Initial commit"

# Feature branch workflow
git checkout -b feature/products-api
git add .
git commit -m "Add products API endpoints"
git push origin feature/products-api

# Merge vissza main-be
git checkout main
git merge feature/products-api
git push origin main
```

---

## Hibakeresés

### Gyakori hibák és megoldások

#### 1. "Class not found" hiba

```
Class 'App\Models\Product' not found
```

**Megoldás**:
```bash
composer dump-autoload
```

#### 2. "Column not found" hiba

```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'products.deleted_at'
```

**Ok**: A modellben használsz `SoftDeletes`-t, de a migrációban nincs `softDeletes()`.

**Megoldás**: Add hozzá a migrációhoz:
```php
$table->softDeletes();
php artisan migrate:fresh
```

#### 3. "SQLSTATE[23000]: Integrity constraint violation"

**Ok**: Foreign key constraint hiba (nem létező ID-ra hivatkozol).

**Megoldás**: Ellenőrizd, hogy létező ID-t használsz-e, vagy állítsd be a cascade törlést.

#### 4. "N+1 Query Problem"

**Jelei**: Lassú API válaszok, sok adatbázis query.

**Megoldás**: Használj eager loading-ot:
```php
Product::with('category')->get();
```

**Debug**: Laravel Debugbar vagy Telescope használata.

#### 5. "Mass Assignment" hiba

```
Add [column_name] to fillable property to allow mass assignment
```

**Megoldás**: Add hozzá a mezőt a `$fillable` tömbhöz:
```php
protected $fillable = ['name', 'price', 'column_name'];
```

#### 6. Validation hiba nem jelenik meg

**Megoldás**: Ellenőrizd a Request osztály `authorize()` metódusát:
```php
public function authorize(): bool
{
    return true; // Ne legyen false!
}
```

### Debug eszközök

```bash
# Laravel Debugbar telepítése
composer require barryvdh/laravel-debugbar --dev

# Laravel Telescope (fejlettebb monitoring)
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### Log fájlok ellenőrzése

```bash
# Log fájl helye
storage/logs/laravel.log

# Tail (élő követés)
tail -f storage/logs/laravel.log
```

### Query debugging

```php
// Query log engedélyezése
\DB::enableQueryLog();

// Kód futtatása
$products = Product::with('category')->get();

// Query-k kiírása
dd(\DB::getQueryLog());
```

---

