<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Srdr_marker extends Model
{
     protected $guarded = []; 
     use Notifiable;
    
    protected $table = 'srdr_marker';
    protected $primaryKey = 'id';
    protected $fillable = ['srdr_id ','unique_key'];

     // App\Models\Srdr.php
    //  public function promosi()
    //  {
    //       return $this->belongsTo(Promosi::class, 'promosi_id');
         
    //  }
}




