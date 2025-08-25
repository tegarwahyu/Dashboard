<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Member extends Model
{
    use Notifiable;
    
    protected $table = 'tb_member';
    protected $primaryKey = 'id';
    protected $fillable = ['nama_member','no_member','user_id'];
}
