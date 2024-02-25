<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShiftsResource extends JsonResource
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
            'table' => 'Столик №' . $this->table_id,
            'shift_workers' => $this->user->name,
            'create_at' => $this->create_at,
            'status' => $this->status,
            'price' => $this->price,
        ];
    }
}
