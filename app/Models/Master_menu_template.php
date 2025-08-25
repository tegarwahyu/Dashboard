<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Master_menu_template extends Model
{
    use Notifiable;

    protected $primaryKey = 'id';
    protected $table = 'master_menu_template_data';
    protected $fillable = ['menu_template_name','menu_category','menu_category_detail','menu_name','menu_short_name','menu_code','price','status'];
}
