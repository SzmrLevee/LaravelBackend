<?php

namespace App\Http\Controllers;

use App\Models\Panda;
use App\Http\Resources\PandaResource;
use Illuminate\Http\Request;

class PandaController extends Controller
{
    /**
     * Visszaadja az összes pandát, figyelve a rendezési paraméterekre.
     */
    public function index(Request $request)
    {
        $orderBy = $request->query('orderBy');
        $order = $request->query('order', 'asc');

        $query = Panda::query();

        if (in_array($orderBy, ['name', 'age']) && in_array($order, ['asc', 'desc'])) {
            if ($orderBy === 'age') {
                // Age alapján rendezéskor a birth mezőt fordított sorrendben kell rendezni
                // mert régebbi születés = nagyobb kor
                $sortOrder = $order === 'asc' ? 'desc' : 'asc';
                $query->orderBy('birth', $sortOrder);
            } else {
                $query->orderBy('name', $order);
            }
        }

        $pandas = $query->get();
        return PandaResource::collection($pandas);
    }

    /**
     * Visszaad egy konkrét pandát a resource segítségével.
     */
    public function show(Panda $panda)
    {
        return new PandaResource($panda);
    }

    /**
     * Töröl egy pandát az adatbázisból.
     */
    public function destroy(Panda $panda)
    {
        $panda->delete();
        return response()->noContent();
    }
}
