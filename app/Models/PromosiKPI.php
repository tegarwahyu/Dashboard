<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PromosiKPI extends Model
{
    use Notifiable;
    
    protected $table = 'tb_promosi_kpi';
    protected $primaryKey = 'id';
    protected $fillable = ['promo_id','menu_kode','menu_nama','qty_target','rupiah_target'
                            // ,'traffic','pax','bill','budget','sales'
                        ];

    public function promosi()
    {
        return $this->belongsTo(Promosi::class, 'promo_id');
    }
}
