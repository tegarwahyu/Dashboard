<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Target_outlet extends Model
{
    protected $guarded = []; 
    use Notifiable;
    public $timestamps = true;

    protected $table = 'target_sales_outlet';
    protected $primaryKey = 'id';
    protected $fillable = ['senin','selasa','rabu','kamis','jumat','sabtu','minggu','week_number','month','sub_branch_id','week_number'];
}
