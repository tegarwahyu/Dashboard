<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Srr extends Model
{
    protected $guarded = []; 
    use Notifiable;
    public $timestamps = true;

    protected $table = 'srr';
    protected $primaryKey = 'id';
    protected $fillable = ['sales_number','pax','grand_total','sales_date','mark_id'];
}
