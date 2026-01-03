<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BicycleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'wheel_size' => $this->wheel_size,
            'gears' => $this->gears,
            'sex' => $this->sex,
            'type' => $this->type,
            'size' => $this->size,
            'color' => $this->color,
            'manufacturer_id' => $this->manufacturer_id,
        ];
    }
}
