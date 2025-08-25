<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Kompetitor_outlet extends Model
{
    use Notifiable;

    protected $primaryKey = 'id';
    protected $table = 'competitor_outlets';
    protected $fillable = ['nama_outlet','lokasi','kapasitas_outlet','user_id'];
    
}
