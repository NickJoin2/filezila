<?php

namespace App\Http\Resources;

use App\Models\Order;
use App\Models\Shift;
use App\Models\ShiftWorker;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'start' => $this->start,
            'end' => $this->end,
            'active' => $this->active,
            'orders' => ShiftsResource::collection(Order::all())
        ];
    }
}
