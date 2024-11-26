<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['table_id','customer_id', 'reservation_id','total','paid','date'];
    public function meals(){
        return $this->belongsToMany(Meal::class,'order_details','order_id','meal_id')
        ->withPivot('amount_to_pay');
    }
    public function table()
    {
        return $this->belongsTo(Table::class);
    }
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
