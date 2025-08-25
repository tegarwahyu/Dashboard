<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromosiResource extends JsonResource
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
            'judul_promosi' => $this->judul_promosi,
            'img_path' => $this->img_path,
            'deskripsi' => $this->deskripsi,
            'schedule_status' => $this->schedule_status,
            'is_enabled' => $this->is_enabled,
            'mulai_promosi' => $this->mulai_promosi,
            'akhir_promosi'=> $this->akhir_promosi,
            'outlet_id'=> $this->outlet_id,
            'jenis_promosi'=> $this->jenis_promosi,
            'outlet' => new OutletResource($this->whenLoaded('outlet')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
