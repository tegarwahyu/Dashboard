<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Device extends Model
{
    use Notifiable;

    protected $primaryKey = 'id';
    protected $table = 'device';
    protected $fillable = ['id_users','number','name','status'];

}
