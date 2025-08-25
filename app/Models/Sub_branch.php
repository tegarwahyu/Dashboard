<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Sub_branch extends Model
{
    use Notifiable;

    protected $primaryKey = 'id';
    protected $table = 'sub_branch';
    protected $fillable = ['nama_sub_branch','branch_id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
