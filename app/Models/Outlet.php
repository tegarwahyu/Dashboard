<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Outlet extends Model
{
    use Notifiable;
    
    protected $table = 'tb_outlet';
    protected $primaryKey = 'id';
    protected $fillable = ['nama_outlet','lokasi','kode_outlet','brand_id'];

    public function promosi()
    {
        return $this->hasMany(Promosi::class, 'outlet_id');
    }
    
    public function brand() {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
