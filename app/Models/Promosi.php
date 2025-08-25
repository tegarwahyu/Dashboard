<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Promosi extends Model
{
     use Notifiable;
    
    protected $table = 'tb_promosi';
    protected $primaryKey = 'id';
    protected $fillable = ['judul_promosi','img_path','deskripsi','akhir_promosi',
                            'outlet_id','jenis_promosi','mulai_promosi','schedule_status',
                            'is_enabled','uploaded_by','pendapatan_program','budget_marketing','target_sales'];

     public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }
    
    public function promosi_kip() {
        return $this->hasMany(PromosiKPI::class, 'promo_id');
    }

    // app/Models/Promotion.php
    public function scopeByBrand($query, $brandId)
    {
        if ($brandId) {
            $query->whereHas('outlet', fn($q) => $q->where('brand_id', $brandId));
        }
    }
}
