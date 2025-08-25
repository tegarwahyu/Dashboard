<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Srr_marker extends Model
{
     protected $guarded = []; 
     use Notifiable;
    
    protected $table = 'srr_marker';
    protected $primaryKey = 'id';
    protected $fillable = ['srr_id','unique_key'];

     // App\Models\Srdr.php
    //  public function promosi()
    //  {
    //       return $this->belongsTo(Promosi::class, 'promosi_id');
         
    //  }
}




