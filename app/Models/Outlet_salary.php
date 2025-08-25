<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Outlet_salary extends Model
{
    use Notifiable;
    protected $guarded = [];
    public $timestamps = true;

    protected $table = 'outlet_salary_monthly';
    protected $primaryKey = 'id';
    protected $fillable = ['outlet_id','total_salary','month'];
}
