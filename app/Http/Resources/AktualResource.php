<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AktualResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'traffic' => $this->traffic,
            'pax' => $this->pax,
            'bill' => $this->bill,
            'budget' => $this->budget,
            'sales' => $this->sales,
            'promosi' => new PromosiResource($this->whenLoaded('promosi')),
            'promosi_id' => $this->promosi_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
