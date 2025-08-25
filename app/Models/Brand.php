<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\Outlet;

class Brand extends Model
{
    use Notifiable;

    protected $primaryKey = 'id';
    protected $table = 'brand';
    protected $fillable = ['nama_brand','status','logo_path','kode_brand'];

    public function outlets() {
        return $this->hasMany(Outlet::class, 'brand_id');
    }

    public function branches()
    {
        return $this->hasMany(Branch::class, 'brand_id');
    }
}
