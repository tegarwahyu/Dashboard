<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Aktual extends Model
{
    use Notifiable;

    protected $primaryKey = 'id';
    protected $table = 'tb_aktual';
    protected $fillable = ['traffic','pax','bill','budget','sales','promosi_id','outlet_id','promo_date'];
    
    public function promosi()
    {
        return $this->belongsTo(Promosi::class, 'promosi_id');
    }

    public function outlet() {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }
}
