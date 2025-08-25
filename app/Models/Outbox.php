<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Outbox extends Model
{
    use Notifiable;

    protected $primaryKey = 'id';
    protected $table = 'outbox';
    protected $fillable = ['number','text','status','id_device'];

    public function device(){
        return $this->hasOne(Device::class, 'id', 'id_device');
        return $this->belongsTo(Device::class, 'id', 'id_device');
    }
}
