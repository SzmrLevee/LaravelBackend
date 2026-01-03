<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBicycleRequest;
use App\Http\Requests\UpdateBicycleRequest;
use App\Http\Resources\BicycleResource;
use App\Models\Bicycle;
use Illuminate\Http\Request;

class BicycleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Bicycle::query();

        if ($request->has('sex')) {
            $query->where('sex', $request->input('sex'));
        }

        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        $bicycles = $query->get();
        return BicycleResource::collection($bicycles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBicycleRequest $request)
    {
        $bicycle = Bicycle::create($request->validated());
        return new BicycleResource($bicycle);
    }

    /**
     * Display the specified resource.
     */
    public function show(Bicycle $bicycle)
    {
        return new BicycleResource($bicycle);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBicycleRequest $request, Bicycle $bicycle)
    {
        $bicycle->update($request->validated());
        return new BicycleResource($bicycle);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bicycle $bicycle)
    {
        try {
            $bicycle->delete();
            return response()->noContent();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete bicycle'], 500);
        }
    }
}
