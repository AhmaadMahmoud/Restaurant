<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;
    protected $fillable = ['name','price','description','quantity_available','discount'];

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_details','meal_id',"order_id")
        ->withPivot('amount_to_pay');
    }
}
