<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\Sub_branch;

class Branch extends Model
{
    use Notifiable;

    protected $primaryKey = 'id';
    protected $table = 'branch';
    protected $fillable = ['nama_branch','brand_id '];
    
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function subBranches()
    {
        return $this->hasMany(Sub_branch::class, 'branch_id');
    }
}
