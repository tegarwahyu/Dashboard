<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Srdr extends Model
{
     protected $guarded = []; 
     use Notifiable;
    
    protected $table = 'srdr';
    protected $primaryKey = 'id';
    protected $fillable = ['sales_number', 'sales_date', 'sales_date_in', 'sales_date_out', 'branch', 
                         'brand', 'city', 'visit_purpose', 'payment_method', 'menu_category', 
                         'menu_category_detail', 'menu', 'menu_code', 'order_mode', 'qty', 'price', 
                         'subtotal', 'discount', 'total', 'nett_sales', 'bill_discount', 
                         'total_after_bill_discount', 'waiters','promosi_id','tax','mark_id','service_charge'];

     // App\Models\Srdr.php
     public function promosi()
     {
          return $this->belongsTo(Promosi::class, 'promosi_id');
         
     }
}




