<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Kompetitor_visit extends Model
{
    use Notifiable;

    protected $primaryKey = 'id';
    protected $table = 'competitor_visit';
    protected $fillable = ['competitor_outlet_id','waktu_visit','estimasi_pengunjung'];
    
}
