<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Analisa_item extends Model
{
    use Notifiable;

    protected $primaryKey = 'id';
    protected $table = 'analisa_item';
    protected $fillable = ['menu_code','cost','branch'];
}
