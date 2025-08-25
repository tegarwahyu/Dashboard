<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
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
            'kode_brand' => $this->kode_brand,
            'nama_brand' => $this->nama_brand,
            'logo_path' => $this->logo_path,
            'status' => $this->status,
            'outlets' => OutletResource::collection($this->whenLoaded('outlets')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
